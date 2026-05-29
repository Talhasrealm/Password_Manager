<?php
require_once __DIR__ . '/Database.php';

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    // Creates a random key and locks it with the user's password
    private function generateEncryptedKey(string $plainPassword): string {
        $rawKey = bin2hex(random_bytes(16));
        $iv = random_bytes(16);
        $encryptedKey = openssl_encrypt($rawKey, 'AES-256-CBC', $plainPassword, 0, $iv);
        return base64_encode($iv) . ':' . $encryptedKey;
    }

    // Register a new user
    public function register(string $username, string $plainPassword): bool {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
        $encryptedKey = $this->generateEncryptedKey($plainPassword);

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password_hash, encryption_key) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$username, $hash, $encryptedKey]);
    }

    // Login — returns user data if correct, null if wrong
    public function login(string $username, string $plainPassword): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($plainPassword, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    // Unlocks the encryption key using the user's password
    public function decryptKey(string $encryptedKeyData, string $plainPassword): string {
        [$ivBase64, $encryptedKey] = explode(':', $encryptedKeyData, 2);
        $iv = base64_decode($ivBase64);
        return openssl_decrypt($encryptedKey, 'AES-256-CBC', $plainPassword, 0, $iv);
    }
}