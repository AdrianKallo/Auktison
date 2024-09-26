<?php
require '../src/config/database.php';
require '../src/models/Bid.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['productId'];
    $productName = $_POST['productName'];
    $fullName = $_POST['fullName'];
    $amount = $_POST['amount'];

    $bidModel = new Bid($db);
    try {
        $bidModel->placeBid($productId, $fullName, $amount, $productName);
        // Redirect back to the auction list after placing a bid
        header('Location: index.php');
        exit();
    } catch (Exception $e) {
        // Redirect back to the auction list with an error message
        header('Location: index.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}
?>
