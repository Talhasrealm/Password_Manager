<?php
require_once 'classes/Database.php';

try {
    $db = Database::getConnection();
    echo "✅ Connected to database successfully!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}