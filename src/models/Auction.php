<?php
class Auction
{
    private $db;
    private $apiUrl = "http://uptime-auction-api.azurewebsites.net/api/Auction";

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function fetchAuctionsFromApi()
    {
        $json = @file_get_contents($this->apiUrl);
        if ($json === false) {
            // Handle the error, maybe log it or throw an exception
            return [];
        }
        return json_decode($json, true);
    }

    public function saveAuctionsToDb($auctions)
    {
        foreach ($auctions as $auction) {
            // Convert biddingEndDate to UTCDateTime
            $auction['biddingEndDate'] = new MongoDB\BSON\UTCDateTime(strtotime($auction['biddingEndDate']) * 1000);

            // Check if the auction already exists to avoid duplicates
            $exists = $this->db->auctions->findOne(['productId' => $auction['productId']]);
            if (!$exists) {
                $this->db->auctions->insertOne($auction);
            }
        }
    }

    public function getActiveAuctions($category = null)
    {
        $filter = ['biddingEndDate' => ['$gte' => new MongoDB\BSON\UTCDateTime()]];
        if ($category) {
            $filter['productCategory'] = $category; // Use productCategory here
        }
        return $this->db->auctions->find($filter, ['sort' => ['biddingEndDate' => 1]])->toArray();
    }

    public function getCategories()
    {
        return $this->db->auctions->distinct('productCategory'); // Use productCategory here
    }
}
