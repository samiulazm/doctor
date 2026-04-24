"""Report duplicate $lang['key'] assignments in system_syntax_lang.php (last wins in PHP)."""
import re
import collections
import sys

def main(path):
    t = open(path, encoding="utf-8", errors="replace").read()
    keys = re.findall(r"\$lang\['([^']+)'\]", t)
    c = collections.Counter(keys)
    dups = sorted([(n, k) for k, n in c.items() if n > 1], reverse=True)
    print(f"File: {path}")
    print(f"Total keys: {len(keys)} unique: {len(c)} duplicates: {len(dups)}")
    for n, k in dups[:50]:
        print(f"  {n}x  {k}")

if __name__ == "__main__":
    p = r"c:\Users\Samiul\Pictures\doctor\Multi-Hospital\application\language\english\system_syntax_lang.php"
    main(p)
