function updateAuctionTimes() {
    const now = new Date();
    let hasEnded = false; // Flag to track if any auction has ended

    document.querySelectorAll('.auction-box').forEach(auctionBox => {
        const biddingEndDateStr = auctionBox.querySelector('p:nth-child(4)').innerText; // Adjust if needed
        const biddingEndDate = new Date(biddingEndDateStr + 'Z'); // Add 'Z' to indicate UTC

        const timeLeft = biddingEndDate - now;

        const timeLeftElement = auctionBox.querySelector('.time-left');
        if (timeLeft > 0) {
            const hoursLeft = Math.floor(timeLeft / 3600000);
            const minutesLeft = Math.floor((timeLeft % 3600000) / 60000);
            const secondsLeft = Math.floor((timeLeft % 60000) / 1000);
            timeLeftElement.innerHTML = `<strong>Time Left:</strong> ${hoursLeft} hours, ${minutesLeft} minutes, ${secondsLeft} seconds`;
        } else {
            timeLeftElement.innerHTML = `<strong>Time Left:</strong> Bidding has ended`;
            auctionBox.remove();
            hasEnded = true; // Set the flag if any auction has ended
        }
    });

    if (hasEnded) {
        // Refresh the page only if there are ended auctions
        setTimeout(() => {
            window.location.reload(); // Refresh the page after 1 second
        }, 1000);
    }
}

function refreshAuctions() {
    const auctionBoxes = document.querySelectorAll('.auction-box');

    // Create a mapping of current input values
    const inputValues = {};
    auctionBoxes.forEach(box => {
        const productId = box.dataset.id; // Assuming product ID is stored in a data attribute
        const fullNameInput = box.querySelector('input[name="fullName"]');
        const amountInput = box.querySelector('input[name="amount"]');
        
        inputValues[productId] = {
            fullName: fullNameInput.value,
            amount: amountInput.value,
        };
    });

    fetch('http://uptime-auction-api.azurewebsites.net/api/Auction') // Replace with your actual API endpoint
        .then(response => response.json())
        .then(data => {
            data.forEach(product => {
                const auctionBox = document.querySelector(`.auction-box[data-id="${product.id}"]`);
                if (auctionBox) {
                    // Update auction box details while preserving input fields
                    auctionBox.querySelector('.product-name').innerText = product.name;
                    auctionBox.querySelector('.current-bid').innerText = product.currentBid;
                    auctionBox.querySelector('p:nth-child(4)').innerText = product.biddingEndDate; // Update bidding end date

                    // Restore input values
                    const fullNameInput = auctionBox.querySelector('input[name="fullName"]');
                    const amountInput = auctionBox.querySelector('input[name="amount"]');
                    fullNameInput.value = inputValues[product.id]?.fullName || '';
                    amountInput.value = inputValues[product.id]?.amount || '';
                } else {
                    // Create a new auction box if it doesn't exist
                    const newAuctionBox = document.createElement('div');
                    newAuctionBox.className = 'auction-box';
                    newAuctionBox.setAttribute('data-id', product.id);
                    newAuctionBox.innerHTML = `
                        <p class="product-name">${product.name}</p>
                        <p class="current-bid">${product.currentBid}</p>
                        <p>${product.biddingEndDate}</p>
                        <div class="time-left"></div>
                        <input type="text" name="fullName" placeholder="Your Name">
                        <input type="number" name="amount" placeholder="Bid Amount">
                    `;
                    document.querySelector('#auctions').appendChild(newAuctionBox);
                }
            });
        })
        .catch(error => console.error('Error fetching auctions:', error));
}

// Call the refresh function periodically (e.g., every 10 seconds)
setInterval(refreshAuctions, 10000);
setInterval(updateAuctionTimes, 1000);
updateAuctionTimes();
