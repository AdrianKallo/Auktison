<?php

require_once __DIR__ . '/../../vendor/autoload.php';

class Bid {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function placeBid($productId, $fullName, $amount, $productName) {
        // Find the current highest bid for the product
        $currentBid = $this->db->bids->findOne(['productId' => $productId], ['sort' => ['amount' => -1]]);

        if ($currentBid) {
            // If there is a current bid and the new amount is higher
            if ($amount > $currentBid['amount']) {
                // Update the current bid with new bidder details
                $this->db->bids->updateOne(
                    ['productId' => $productId],
                    ['$set' => [
                        'fullName' => $fullName,
                        'amount' => $amount,
                        'timestamp' => new MongoDB\BSON\UTCDateTime()
                    ]]
                );
            } else {
                // If the new bid is not higher, throw an exception
                throw new Exception("Bid amount must be higher than the current highest bid.");
            }
        } else {
            // If there's no existing bid for the product, insert a new bid
            $this->db->bids->insertOne([
                'productId' => $productId,
                'productName' => $productName,
                'fullName' => $fullName,
                'amount' => $amount,
                'timestamp' => new MongoDB\BSON\UTCDateTime()
            ]);
        }
    }
}
?>
