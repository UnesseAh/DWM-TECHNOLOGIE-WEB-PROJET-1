<?php
require_once '../config/database.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM contacts WHERE id = ?");
    if ($stmt->execute([$id])) {
        header("Location: ../index.php?deleted=1");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}