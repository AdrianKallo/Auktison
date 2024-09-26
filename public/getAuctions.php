<?php
require_once '../src/config/database.php';
require_once '../src/controllers/AuctionController.php';

$auctionController = new AuctionController($db);
$activeAuctions = $auctionController->getActiveAuctions($_GET['category'] ?? null);

header('Content-Type: application/json');

$activeAuctions = array_map(function($auction) {
    $auction['biddingEndDate'] = $auction['biddingEndDate']->toDateTime()->format(DATE_ISO8601);
    return $auction;
}, $activeAuctions);


echo json_encode($activeAuctions);
?>
