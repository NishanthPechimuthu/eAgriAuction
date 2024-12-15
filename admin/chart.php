<?php
session_start();
ob_start();

include "header.php";
include "navbar.php";

// Fetch data for all three charts
$data = getBidData();
$userData = getUserRegistrationData();

// Set limit for auction pagination
$limit = 6;

// Fetch paginated auction data
$auctionPage = isset($_GET['auctionPage']) ? intval($_GET['auctionPage']) : 1;
$auctionOffset = ($auctionPage - 1) * $limit;
$auctionDataPaginated = getAuctionData($auctionOffset, $limit);
$totalAuctions = getAuctionCount();
$auctionTotalPages = ceil($totalAuctions / $limit);

// Prepare Bid Chart data (Last 7 days)
$bidData = array_reverse($data); // Reversing the data for recent to old
$recentBidData = array_slice($bidData, 0, 7);
[$bidLabels, $bidDatasets] = prepareChartData($recentBidData, 'bidDate', ['maxBid', 'totalBid']);

$totalBidAmount = array_sum(array_column($recentBidData, 'totalBid')); // Sum of all total bids for last 7 days
$maxBidSum = array_sum(array_column($recentBidData, 'maxBid')); // Sum of max bids for last 7 days

// Prepare User Registration Chart data (Last 6 months)
$userData = array_reverse($userData); // Reversing to show most recent first
$recentUserData = array_slice($userData, 0, 6);
[$userLabels, $userDatasets] = prepareChartData($recentUserData, 'registrationMonth', ['userCount']);

// Prepare Auction Chart data (Last 6 Auctions)
[$auctionLabels, $auctionDatasets] = prepareChartData($auctionDataPaginated, 'auctionTitle', ['auctionStartPrice', 'highestBid'], true, 8);

// Fetch user status data for the pie chart
$userStatusData = getUserStatusData();
$userStatusLabels = [];
$userStatusCounts = [];

foreach ($userStatusData as $status) {
    $userStatusLabels[] = ucfirst($status['userStatus']); // Capitalize the first letter
    $userStatusCounts[] = $status['statusCount'];
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Agri-Themed Charts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    <style>
        .pagination {
            margin: 0;
        }
        .pagination .page-item.active .page-link {
            background-color: #28a745;
            border-color: #28a745;
        }
        .pagination .page-link {
            color: #28a745;
        }
        .pagination .page-link:hover {
            background-color: #d4edda;
            border-color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Agri-Themed Charts</h1>

        <!-- Bid Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Bids (Last 7 Days)
                </div>
                <div class="card-body">
                    <canvas id="bidChart" width="100%" height="50"></canvas>
                    <p>Total Bid Amount (Last 7 Days): ₹<?php echo number_format($totalBidAmount); ?></p>
                    <p>Sum of Max Bids (Last 7 Days): ₹<?php echo number_format($maxBidSum); ?></p>
                </div>
                <div class="card-footer small text-muted">
                    Updated on <?php echo getLastUpdateLabel(); ?>
                </div>
            </div>
        </div>

        <!-- User Registration Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users me-1"></i>
                    User Registrations (Last 6 Months)
                </div>
                <div class="card-body"><canvas id="registrationChart" width="100%" height="50"></canvas></div>
                <div class="card-footer small text-muted">
                    Updated on <?php echo getLastUpdateLabel(); ?>
                </div>
            </div>
        </div>

        <!-- Auction Prices Chart -->
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-gavel me-1"></i>
                    Auction Prices (Base vs. Highest)
                </div>
                <div class="card-body"><canvas id="auctionChart" width="100%" height="50"></canvas></div>
                <div class="card-footer small text-muted">
                    Updated on <?php echo getLastUpdateLabel(); ?>
                </div>
            </div>
            <nav aria-label="Auction Chart Pagination">
                <ul class="pagination justify-content-center mb-4">
                    <?php for ($i = 1; $i <= $auctionTotalPages; $i++) : ?>
                        <li class="page-item <?php echo $i == $auctionPage ? 'active' : ''; ?>">
                            <a class="page-link <?php echo $i == $auctionPage ? 'text-white' : ''; ?>" href="?auctionPage=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

        <!-- User Status Pie Chart -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    User Status Distribution
                </div>
                <div class="card-body">
                    <canvas id="userStatusChart" width="100%" height="50"></canvas>
                </div>
                <div class="card-footer small text-muted">
                    Updated on <?php echo getLastUpdateLabel(); ?>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Bid Chart (Last 7 Days)
            new Chart(document.getElementById("bidChart").getContext("2d"), {
                type: "line",
                data: {
                    labels: <?php echo json_encode($bidLabels); ?>,
                    datasets: [
                        {
                            label: "Max Bid",  // Swapped labels
                            data: <?php echo json_encode($bidDatasets['maxBid']); ?>,
                            borderColor: "rgba(34, 139, 34, 1)",
                            backgroundColor: "rgba(0,0,0,0)",
                            borderWidth: 2,
                            fill: true,
                        },
                        {
                            label: "Total Bids",  // Swapped labels
                            data: <?php echo json_encode($bidDatasets['totalBid']); ?>,
                            borderColor: "rgba(160, 82, 45, 1)",
                            backgroundColor: "rgba(160, 82, 45, 0.8)",
                            borderWidth: 2,
                            fill: true,
                        },
                    ],
                },
            });

            // User Registration Chart (Last 6 Months)
            new Chart(document.getElementById("registrationChart").getContext("2d"), {
                type: "bar",
                data: {
                    labels: <?php echo json_encode($userLabels); ?>,
                    datasets: [
                        {
                            label: "User Registrations",
                            data: <?php echo json_encode($userDatasets['userCount']); ?>,
                            backgroundColor: "rgba(34, 139, 34, 0.8)",
                            borderColor: "rgba(34, 139, 34, 1)",
                            borderWidth: 2,
                        },
                    ],
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                            },
                        }],
                    },
                },
            });

            // Auction Chart (Last 6 Auctions)
            new Chart(document.getElementById("auctionChart").getContext("2d"), {
                type: "bar",
                data: {
                    labels: <?php echo json_encode($auctionLabels); ?>,
                    datasets: [
                        {
                            label: "Base Price",
                            data: <?php echo json_encode($auctionDatasets['auctionStartPrice']); ?>,
                            backgroundColor: "rgba(34, 139, 34, 0.8)",
                            borderColor: "rgba(34, 139, 34, 1)",
                            borderWidth: 2,
                        },
                        {
                            label: "Highest Bid",
                            data: <?php echo json_encode($auctionDatasets['highestBid']); ?>,
                            backgroundColor: "rgba(160, 82, 45, 0.8)",
                            borderColor: "rgba(160, 82, 45, 1)",
                            borderWidth: 2,
                        },
                    ],
                },
            });

            // User Status Pie Chart
            new Chart(document.getElementById("userStatusChart").getContext("2d"), {
                type: "pie",
                data: {
                    labels: <?php echo json_encode($userStatusLabels); ?>,
                    datasets: [{
                        data: <?php echo json_encode($userStatusCounts); ?>,
                        backgroundColor: [
                            "rgba(34, 139, 34, 0.8)",   // Green for 'activate'
                            "rgba(160,82,45,0.8)", // Brown for 'deactivate'
                            "rgb(106,9,255,0.8)"     // Violate for 'suspend'
                        ],
                        borderColor: [
                            "rgba(34, 139, 34, 1)",
                            "rgba(160, 82, 45, 1)",
                            "rgb(106,9,255,1)"
                        ],
                        borderWidth: 2,
                    }]
                },
            });
        });
    </script>
    
</body>
</html>