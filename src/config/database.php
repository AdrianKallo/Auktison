<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Correct path to vendor/autoload.php

$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->selectDatabase("auctionDB");
?>
