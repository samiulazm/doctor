<?php

declare(strict_types=1);

/**
 * Create or reset a superadmin user (Ion Auth group "superadmin", id 1 in `groups`).
 *
 * Usage:
 *   php scripts/create_superadmin.php [email] [password] ["Display Name"]
 *
 * Examples:
 *   php scripts/create_superadmin.php
 *   php scripts/create_superadmin.php superadmin@hms.com "YourStrongPassword123!"
 *
 * Environment (optional):
 *   DB_HOST, DB_USER, DB_PASS, DB_NAME — override application/config/database.php defaults
 */

$root = dirname(__DIR__);

$host = getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'democa_hmz_v2';

$argvEmail = $argv[1] ?? 'superadmin@hms.com';
$argvPassword = $argv[2] ?? getenv('SUPERADMIN_PASSWORD') ?: 'ChangeMe123!';
$displayName = $argv[3] ?? 'Super Admin';

if ($argvPassword === '') {
    fwrite(STDERR, "Password cannot be empty.\n");
    exit(1);
}

$mysqli = @new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "Database connection failed: {$mysqli->connect_error}\n");
    exit(1);
}
$mysqli->set_charset('utf8');

// Match application/config/ion_auth.php: bcrypt cost 8 (Ion Auth uses crypt()/verify).
$hash = password_hash($argvPassword, PASSWORD_BCRYPT, ['cost' => 8]);
if ($hash === false) {
    fwrite(STDERR, "Failed to hash password (bcrypt).\n");
    exit(1);
}

$moduleList = 'home,hospital,package,request,superadmin,email,pgateway,slide,service,systems';

$stmt = $mysqli->prepare('SELECT id, username FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $argvEmail);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

$now = time();
$ip = '127.0.0.1';

if ($row) {
    $uid = (int) $row['id'];
    $upd = $mysqli->prepare(
        'UPDATE users SET password = ?, salt = ?, active = 1, ip_address = ? WHERE id = ?'
    );
    $emptySalt = '';
    $upd->bind_param('sssi', $hash, $emptySalt, $ip, $uid);
    $upd->execute();
    $upd->close();

    $chk = $mysqli->prepare('SELECT id FROM users_groups WHERE user_id = ? AND group_id = 1 LIMIT 1');
    $chk->bind_param('i', $uid);
    $chk->execute();
    $chkRes = $chk->get_result();
    $hasGroup = $chkRes->fetch_assoc();
    $chk->close();

    if (!$hasGroup) {
        $ins = $mysqli->prepare('INSERT INTO users_groups (user_id, group_id) VALUES (?, 1)');
        $ins->bind_param('i', $uid);
        $ins->execute();
        $ins->close();
    }

    $sa = $mysqli->prepare('SELECT id FROM superadmin WHERE ion_user_id = ? LIMIT 1');
    $ionId = (string) $uid;
    $sa->bind_param('s', $ionId);
    $sa->execute();
    $saRes = $sa->get_result();
    $saRow = $saRes->fetch_assoc();
    $sa->close();

    if ($saRow) {
        $u2 = $mysqli->prepare('UPDATE superadmin SET email = ?, name = ?, module = ? WHERE ion_user_id = ?');
        $u2->bind_param('ssss', $argvEmail, $displayName, $moduleList, $ionId);
        $u2->execute();
        $u2->close();
    } else {
        $max = $mysqli->query('SELECT COALESCE(MAX(id), 0) + 1 AS n FROM superadmin');
        $nextId = (int) $max->fetch_assoc()['n'];
        $img = 'uploads/userIcon.png';
        $phone = '';
        $address = '';
        $insSa = $mysqli->prepare(
            'INSERT INTO superadmin (id, name, email, phone, address, img_url, ion_user_id, module) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $insSa->bind_param(
            'isssssss',
            $nextId,
            $displayName,
            $argvEmail,
            $phone,
            $address,
            $img,
            $ionId,
            $moduleList
        );
        $insSa->execute();
        $insSa->close();
    }

    fwrite(STDOUT, "Updated superadmin user id {$uid} ({$argvEmail}). Password has been reset.\n");
    $mysqli->close();
    exit(0);
}

$username = $displayName;
$ins = $mysqli->prepare(
    'INSERT INTO users (ip_address, username, password, salt, email, created_on, active, first_name, last_name, company, phone, img_url, sidebar) VALUES (?, ?, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?, ?)'
);
$fn = 'Super';
$ln = 'Admin';
$company = 'ADMIN';
$phone = '0';
$img = '';
$sidebar = '1';
$emptySalt = '';
$ins->bind_param(
    'sssssissssss',
    $ip,
    $username,
    $hash,
    $emptySalt,
    $argvEmail,
    $now,
    $fn,
    $ln,
    $company,
    $phone,
    $img,
    $sidebar
);
$ins->execute();
$newId = (int) $mysqli->insert_id;
$ins->close();

$g = $mysqli->prepare('INSERT INTO users_groups (user_id, group_id) VALUES (?, 1)');
$g->bind_param('i', $newId);
$g->execute();
$g->close();

$max = $mysqli->query('SELECT COALESCE(MAX(id), 0) + 1 AS n FROM superadmin');
$nextId = (int) $max->fetch_assoc()['n'];
$ionId = (string) $newId;
$img = 'uploads/userIcon.png';
$phone = '';
$address = '';
$insSa = $mysqli->prepare(
    'INSERT INTO superadmin (id, name, email, phone, address, img_url, ion_user_id, module) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
);
$insSa->bind_param(
    'isssssss',
    $nextId,
    $displayName,
    $argvEmail,
    $phone,
    $address,
    $img,
    $ionId,
    $moduleList
);
$insSa->execute();
$insSa->close();

fwrite(STDOUT, "Created superadmin user id {$newId} ({$argvEmail}).\n");
$mysqli->close();
exit(0);
