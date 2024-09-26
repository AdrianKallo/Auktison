<?php

require_once __DIR__ . '/../vendor/autoload.php'; // MongoDB client

// Load the DDL JSON file
$ddlFile = __DIR__ . '/../db/mongodb_collections.json';
$ddlData = json_decode(file_get_contents($ddlFile), true);

// Database connection
$client = new MongoDB\Client("mongodb://localhost:27017");
$db = $client->selectDatabase("auctionDB");

// Create collections and indexes
foreach ($ddlData['collections'] as $collection) {
    $collectionName = $collection['name'];

    // Check if collection exists
    if (!$db->listCollections(['filter' => ['name' => $collectionName]])) {
        $db->createCollection($collectionName);
        echo "Collection '$collectionName' created.\n";
    } else {
        echo "Collection '$collectionName' already exists.\n";
    }

    // Create indexes
    if (isset($collection['indexes'])) {
        foreach ($collection['indexes'] as $index) {
            $db->$collectionName->createIndex($index['key'], ['unique' => $index['unique'] ?? false]);
            echo "Index created on '$collectionName' for " . json_encode($index['key']) . ".\n";
        }
    }
}

echo "Database setup complete.\n";
?>
