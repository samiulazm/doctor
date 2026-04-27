"""
FTP Deployment Script for the CodeIgniter app (public_html/)
Uploads the codebase to the remote FTP server with auto-reconnect.

Usage:
    python deploy.py                  # Deploy all files
    python deploy.py --config-only    # Upload only application/config (base URL, env bootstrap)
    python deploy.py --only-changed   # Deploy only git-changed files
    python deploy.py --dry-run        # Preview what would be uploaded
    python deploy.py --skip-assets    # Skip large asset dirs (fontawesome, adminlte)
    python deploy.py --resume         # Resume from last failed upload
    python deploy.py --preflight      # Validate deploy config without uploading
    python deploy.py --project-root ..\\public_html  # Deploy a specific project root
"""

import ftplib
import os
import sys
import time
import json
import argparse
import subprocess
from pathlib import Path, PurePosixPath

# Directories/files to skip during upload
SKIP = {
    '.git', '.gitignore', '.env', '__pycache__', '.phpunit.cache',
    'node_modules', 'deploy.py', 'tests', 'phpunit.xml.dist',
    'run-tests.ps1', 'composer.ps1', '.github', 'deploy_progress.json',
}

# Large asset directories to skip with --skip-assets
HEAVY_ASSETS = {
    'fontawesome5pro', 'fontawesome', 'font-awesome', 'adminlte',
}

PROGRESS_FILE = 'deploy_progress.json'
MAX_RETRIES = 3
KEEPALIVE_INTERVAL = 20  # send NOOP every N files

# Quick security/config updates (paths relative to Multi-Hospital root)
CONFIG_ONLY_REL_PATHS = (
    'application/config/config.php',
    'application/config/env_bootstrap.php',
)


def load_env(env_path):
    config = {}
    with open(env_path) as f:
        for line in f:
            line = line.strip()
            if line and not line.startswith('#') and '=' in line:
                key, value = line.split('=', 1)
                config[key.strip()] = value.strip()
    return config


def preflight_config(config, base_dir):
    errors = []

    def req_nonempty(key):
        val = config.get(key, '').strip()
        if not val:
            errors.append(f"Missing required {key} in {base_dir / '.env'}")
        return val

    host = req_nonempty('FTP_HOST')
    user = req_nonempty('FTP_USER')
    password = req_nonempty('FTP_PASS')

    port_raw = config.get('FTP_PORT', '21').strip() or '21'
    try:
        port = int(port_raw)
        if port <= 0 or port > 65535:
            errors.append("FTP_PORT must be between 1 and 65535")
    except ValueError:
        errors.append("FTP_PORT must be an integer")
        port = 21

    remote_dir = config.get('FTP_REMOTE_DIR', '/').strip() or '/'
    if not remote_dir.startswith('/'):
        errors.append("FTP_REMOTE_DIR must be an absolute POSIX path (start with '/'), e.g. /public_html")

    # Make it harder to accidentally deploy from a wrong directory.
    if not (base_dir / 'application').is_dir() or not (base_dir / 'index.php').is_file():
        errors.append(f"Project root does not look like a CodeIgniter app: {base_dir}")

    if errors:
        for e in errors:
            print(f"[ERROR] {e}")
        return False

    # Do not print password.
    print("[*] Preflight OK")
    print(f"[*] Project root: {base_dir}")
    print(f"[*] FTP Target: {user}@{host}:{port}")
    print(f"[*] Remote dir: {remote_dir}")
    return True


def get_changed_files(base_dir):
    try:
        result = subprocess.run(
            ['git', 'diff', '--name-only', 'HEAD'],
            capture_output=True, text=True, cwd=base_dir
        )
        staged = subprocess.run(
            ['git', 'diff', '--name-only', '--cached'],
            capture_output=True, text=True, cwd=base_dir
        )
        untracked = subprocess.run(
            ['git', 'ls-files', '--others', '--exclude-standard'],
            capture_output=True, text=True, cwd=base_dir
        )
        files = set()
        for out in [result.stdout, staged.stdout, untracked.stdout]:
            for line in out.strip().splitlines():
                if line:
                    files.add(line.replace('\\', '/'))
        return files
    except FileNotFoundError:
        print("[!] git not found, uploading all files.")
        return None


def should_skip(path_parts, skip_assets=False):
    if any(part in SKIP for part in path_parts):
        return True
    if skip_assets and any(part.lower() in HEAVY_ASSETS for part in path_parts):
        return True
    return False


class FTPDeployer:
    def __init__(self, host, port, user, password, remote_dir):
        self.host = host
        self.port = port
        self.user = user
        self.password = password
        self.remote_dir = remote_dir
        self.ftp = None
        self._created_dirs = set()

    def connect(self):
        if self.ftp:
            try:
                self.ftp.quit()
            except Exception:
                pass
        self.ftp = ftplib.FTP()
        self.ftp.connect(self.host, self.port, timeout=300)
        self.ftp.login(self.user, self.password)
        self.ftp.set_pasv(True)
        print(f"[+] Connected to {self.host}")

    def reconnect(self):
        print("[*] Reconnecting...")
        time.sleep(2)
        self.connect()

    def keepalive(self):
        try:
            self.ftp.voidcmd('NOOP')
        except Exception:
            self.reconnect()

    def ensure_remote_dir(self, remote_dir):
        if remote_dir in self._created_dirs:
            return
        parts = PurePosixPath(remote_dir).parts
        current = ''
        for part in parts:
            if part == '/':
                current = '/'
                continue
            current = current + '/' + part if current != '/' else '/' + part
            if current in self._created_dirs:
                continue
            try:
                self.ftp.cwd(current)
                self._created_dirs.add(current)
            except ftplib.error_perm:
                try:
                    self.ftp.mkd(current)
                    self._created_dirs.add(current)
                except ftplib.error_perm:
                    self._created_dirs.add(current)

    def upload_file(self, local_path, remote_path):
        remote_dir = str(PurePosixPath(remote_path).parent)
        self.ensure_remote_dir(remote_dir)
        with open(local_path, 'rb') as f:
            self.ftp.storbinary(f'STOR {remote_path}', f, blocksize=8192)

    def upload_with_retry(self, local_path, remote_path):
        for attempt in range(1, MAX_RETRIES + 1):
            try:
                self.upload_file(local_path, remote_path)
                return True
            except (ftplib.error_temp, ftplib.error_reply, OSError, EOFError, ConnectionError, TimeoutError) as e:
                if attempt < MAX_RETRIES:
                    print(f"    [RETRY {attempt}/{MAX_RETRIES}] {e}")
                    self.reconnect()
                else:
                    raise

    def close(self):
        if self.ftp:
            try:
                self.ftp.quit()
            except Exception:
                pass


def collect_files(base_dir, only_changed=False, skip_assets=False, config_only=False):
    if config_only:
        out = []
        for rel in CONFIG_ONLY_REL_PATHS:
            p = base_dir / rel
            if p.is_file():
                out.append((str(p), rel.replace('\\', '/')))
            else:
                print(f"[!] Missing: {rel}")
        return out

    files = []
    changed = get_changed_files(base_dir) if only_changed else None

    for root, dirs, filenames in os.walk(base_dir):
        rel_root = Path(root).relative_to(base_dir)
        parts = rel_root.parts if str(rel_root) != '.' else ()

        dirs[:] = [d for d in dirs if d not in SKIP]
        if skip_assets:
            dirs[:] = [d for d in dirs if d.lower() not in HEAVY_ASSETS]

        if should_skip(parts, skip_assets):
            continue

        for filename in filenames:
            if filename in SKIP:
                continue
            local_path = Path(root) / filename
            rel_path = local_path.relative_to(base_dir)
            rel_posix = rel_path.as_posix()

            if changed is not None and rel_posix not in changed:
                continue

            files.append((str(local_path), rel_posix))

    return files


def load_progress(base_dir):
    p = Path(base_dir) / PROGRESS_FILE
    if p.exists():
        with open(p) as f:
            return set(json.load(f).get('completed', []))
    return set()


def save_progress(base_dir, completed):
    p = Path(base_dir) / PROGRESS_FILE
    with open(p, 'w') as f:
        json.dump({'completed': list(completed)}, f)


def clear_progress(base_dir):
    p = Path(base_dir) / PROGRESS_FILE
    if p.exists():
        p.unlink()


def deploy(base_dir, dry_run=False, only_changed=False, skip_assets=False, resume=False, config_only=False, preflight=False):
    env_path = base_dir / '.env'

    if not env_path.exists():
        print("[ERROR] .env file not found. Copy .env.example to .env and set FTP_* credentials.")
        sys.exit(1)

    config = load_env(env_path)
    if not preflight_config(config, base_dir):
        sys.exit(1)
    if preflight:
        return

    host = config.get('FTP_HOST', '')
    port = int(config.get('FTP_PORT', 21))
    user = config.get('FTP_USER', '')
    password = config.get('FTP_PASS', '')
    remote_dir = config.get('FTP_REMOTE_DIR', '/')

    print(f"[*] FTP Target: {user}@{host}:{port}")
    print(f"[*] Remote dir: {remote_dir}")
    print(f"[*] Mode: {'config-only' if config_only else ('changed only' if only_changed else 'full')}"
          f"{' | skip-assets' if skip_assets else ''}"
          f"{' | resume' if resume else ''}")
    print()

    if config_only and only_changed:
        print("[!] --config-only ignores --only-changed.")
    files = collect_files(
        base_dir,
        only_changed=only_changed and not config_only,
        skip_assets=skip_assets,
        config_only=config_only,
    )

    if not files:
        print("[*] No files to upload.")
        return

    # Filter already-uploaded files if resuming
    completed = set()
    if resume:
        completed = load_progress(base_dir)
        before = len(files)
        files = [(lp, rp) for lp, rp in files if rp not in completed]
        print(f"[*] Resuming: {before - len(files)} already done, {len(files)} remaining.")

    print(f"[*] {len(files)} file(s) to upload.\n")

    if dry_run:
        for _, rel in files:
            print(f"  {rel}")
        return

    deployer = FTPDeployer(host, port, user, password, remote_dir)
    deployer.connect()

    if remote_dir != '/':
        deployer.ensure_remote_dir(remote_dir)

    uploaded = 0
    failed = 0
    failed_files = []
    start_time = time.time()

    for i, (local_path, rel_path) in enumerate(files):
        remote_path = str(PurePosixPath(remote_dir) / rel_path)

        # Keepalive every N files
        if i > 0 and i % KEEPALIVE_INTERVAL == 0:
            deployer.keepalive()
            elapsed = time.time() - start_time
            rate = uploaded / elapsed if elapsed > 0 else 0
            print(f"  --- Progress: {uploaded}/{len(files)} | "
                  f"{rate:.1f} files/sec | "
                  f"Failed: {failed} ---")

        try:
            deployer.upload_with_retry(local_path, remote_path)
            uploaded += 1
            completed.add(rel_path)

            # Save progress every 100 files
            if uploaded % 100 == 0:
                save_progress(base_dir, completed)

            print(f"  [OK] {rel_path}")
        except Exception as e:
            failed += 1
            failed_files.append(rel_path)
            print(f"  [FAIL] {rel_path} -> {e}")
            # Try to reconnect for next file
            try:
                deployer.reconnect()
            except Exception:
                pass

    # Final save
    save_progress(base_dir, completed)

    deployer.close()

    elapsed = time.time() - start_time
    print(f"\n{'='*50}")
    print(f"[*] Done in {elapsed:.1f}s")
    print(f"[*] Uploaded: {uploaded} | Failed: {failed}")

    if failed_files:
        print(f"\n[!] Failed files ({failed}):")
        for f in failed_files:
            print(f"  - {f}")
        print(f"\n[*] Run with --resume to retry failed files.")
    else:
        # All done, clean up progress file
        clear_progress(base_dir)
        print("[*] All files uploaded successfully!")


if __name__ == '__main__':
    parser = argparse.ArgumentParser(description='FTP Deploy for CodeIgniter app (public_html)')
    parser.add_argument('--project-root', default='',
                        help='Path to the project root to deploy (defaults to this script directory).')
    parser.add_argument('--dry-run', action='store_true', help='Preview without uploading')
    parser.add_argument('--only-changed', action='store_true', help='Upload only git-changed files')
    parser.add_argument('--skip-assets', action='store_true',
                        help='Skip large asset dirs (fontawesome, adminlte)')
    parser.add_argument('--resume', action='store_true',
                        help='Resume from where last upload stopped')
    parser.add_argument('--config-only', action='store_true',
                        help='Upload only application/config/config.php and env_bootstrap.php')
    parser.add_argument('--preflight', action='store_true',
                        help='Validate .env/FTP settings and project root without uploading')
    args = parser.parse_args()

    base_dir = Path(args.project_root).expanduser().resolve() if args.project_root else Path(__file__).parent.resolve()

    deploy(base_dir=base_dir, dry_run=args.dry_run, only_changed=args.only_changed,
           skip_assets=args.skip_assets, resume=args.resume, config_only=args.config_only, preflight=args.preflight)
