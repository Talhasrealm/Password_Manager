<?php
require_once __DIR__ . '/Database.php';

class PasswordEntry {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Save a new password entry
    public function save(int $userId, string $siteName, string $plainSitePassword, string $key): bool {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($plainSitePassword, 'AES-256-CBC', $key, 0, $iv);
        $stored = base64_encode($iv) . ':' . $encrypted;

        $stmt = $this->db->prepare(
            "INSERT INTO password_entries (user_id, site_name, encrypted_password) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$userId, $siteName, $stored]);
    }

    // Get all passwords for a user (decrypted)
    public function getAll(int $userId, string $key): array {
        $stmt = $this->db->prepare(
            "SELECT id, site_name, encrypted_password, created_at FROM password_entries WHERE user_id = ?"
        );
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            [$ivBase64, $enc] = explode(':', $row['encrypted_password'], 2);
            $iv = base64_decode($ivBase64);
            $row['plain_password'] = openssl_decrypt($enc, 'AES-256-CBC', $key, 0, $iv);
        }
        return $rows;
    }
}