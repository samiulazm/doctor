<?php
/**
 * Emit a 64-hex-char encryption key for CI_ENCRYPTION_KEY (32 bytes).
 * Usage: php scripts/generate_encryption_key.php
 */
declare(strict_types=1);

echo bin2hex(random_bytes(32)) . PHP_EOL;
