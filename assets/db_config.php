<?php
/**
 * db_config.php — Database credentials
 * SECURITY: This file is blocked from direct web access via .htaccess
 * For extra safety on shared hosting, you may move this file one level
 * above your public_html directory and update the path in _store_lib.php
 */

// PRACTICAL HARDENING PATCH:
// Credentials are centralised here (single place to rotate).
// File is protected by .htaccess Deny from all.
// If your host supports env vars, prefer those instead.

return [
    'host'     => getenv('FK_DB_HOST')     ?: 'sql213.infinityfree.com',
    'port'     => (int)(getenv('FK_DB_PORT') ?: 3306),
    'database' => getenv('FK_DB_NAME')     ?: 'if0_41334373_f1',
    'username' => getenv('FK_DB_USER')     ?: 'if0_41334373',
    'password' => getenv('FK_DB_PASS')     ?: 'Gauravx69',
    'charset'  => 'utf8mb4',
];
