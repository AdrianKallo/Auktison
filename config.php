<?php
require_once 'vendor/autoload.php';

use MongoDB\Client;
use MongoDB\BSON\UTCDateTime;


$client = new Client("mongodb://localhost:27017");
$db = $client->auction_db; // Your MongoDB database name
