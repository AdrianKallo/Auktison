<?php
require_once '../src/views/header.php';
require_once '../src/controllers/AuctionController.php';
require_once '../src/config/database.php'; // Initialize database connection
$auctionController = new AuctionController($db); // Pass the DB connection

$categories = $auctionController->getCategories(); // Fetch categories
$activeAuctions = $auctionController->getActiveAuctions($_GET['category'] ?? null); // Fetch active auctions based on selected category
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Auction Site</title>
</head>

<body>
    <div class="filter-container">
        <form method="GET" action="">
            <select name="category" onchange="this.form.submit()">
                <option value="">Select a category</option>
                <?php if (isset($categories) && is_array($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <a href="index.php" class="reset-button">Show All Auctions</a>
        </form>
    </div>

    <div class="auction-container">
        <?php if (!empty($activeAuctions)): ?>
            <?php foreach ($activeAuctions as $auction): ?>
                <div class="auction-box" data-id="<?php echo htmlspecialchars($auction['productId']); ?>">
                    <h2><?php echo htmlspecialchars($auction['productName']); ?></h2>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($auction['productDescription']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($auction['productCategory']); ?></p>
                    <p><strong>Bidding Ends:</strong> <?php echo date('Y-m-d H:i:s', $auction['biddingEndDate']->toDateTime()->getTimestamp()); ?></p>
                    <p class="time-left"><strong>Time Left:</strong> <?php
                                                                        $timeLeft = $auction['biddingEndDate']->toDateTime()->getTimestamp() - time();
                                                                        if ($timeLeft > 0) {
                                                                            $hoursLeft = floor($timeLeft / 3600);
                                                                            $minutesLeft = floor(($timeLeft % 3600) / 60);
                                                                            $secondsLeft = $timeLeft % 60;
                                                                            echo "{$hoursLeft} hours, {$minutesLeft} minutes, {$secondsLeft} seconds";
                                                                        } else {
                                                                            echo "Bidding has ended";
                                                                        }
                                                                        ?></p>
                    <form action="placeBid.php" method="POST">
                        <input type="hidden" name="productId" value="<?php echo htmlspecialchars($auction['productId']); ?>">
                        <input type="hidden" name="productName" value="<?php echo htmlspecialchars($auction['productName']); ?>">
                        <input type="text" name="fullName" placeholder="Your Full Name" required>
                        <input type="number" name="amount" placeholder="Your Bid Amount (€)" required>
                        <button type="submit">Place Bid</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No active auctions available.</p>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
    <?php require_once '../src/views/footer.php'; ?>
</body>

</html>