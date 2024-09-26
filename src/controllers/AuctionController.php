<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Auction.php';
require_once __DIR__ . '/../models/Bid.php';

class AuctionController {
    private $auctionModel;

    public function __construct($db) {
        $this->auctionModel = new Auction($db);
        $auctionsFromApi = $this->auctionModel->fetchAuctionsFromApi(); // Fetch once
        if (!empty($auctionsFromApi)) {
            $this->auctionModel->saveAuctionsToDb($auctionsFromApi);
        } else {
            // Handle the error (log it or notify)
        }
    }

    public function getActiveAuctions($category = null) {
        return $this->auctionModel->getActiveAuctions($category);
    }

    public function getCategories() {
        return $this->auctionModel->getCategories();
    }
}
?>
