<?php
ob_start(); // Start output buffering
session_start(); // Start the session
include("header.php");
include("navbar.php");

// Call the authentication function
isAuthenticated();
$UPIDetail = getUserUpiId($_SESSION["user_id"]);
if ($UPIDetail === NULL) {
    header("Location: update-profile.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user inputs
    $title = htmlspecialchars(trim($_POST['title']));
    $start_price = filter_var($_POST['start_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $start_time = $_POST['start_time'];
    $end_date = $_POST['end_date'];
    $created_by = getUserFromSession(); // Get user ID from session

    // Handle the cropped image data
    if (!empty($_POST['cropped_image'])) {
        $croppedImageData = $_POST['cropped_image'];
        $uploadDir = '../images/products/';

        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate a unique name
        $uniqueName = 'img_' . uniqid() . '.webp';
        $targetFile = $uploadDir . $uniqueName;

        // Decode base64 and save the image
        list(, $croppedImageData) = explode(',', $croppedImageData);
        $croppedImageData = base64_decode($croppedImageData);

        if (file_put_contents($targetFile, $croppedImageData)) {
            // If save successful, call function to add auction
            if (addAuction($title, $start_price, $start_time, $end_date, $uniqueName)) {
                header("Location: manage-auction.php");
                exit();
            } else {
                echo "Error adding auction.";
            }
        } else {
            echo "Error: Failed to save the cropped image.";
        }
    } else {
        echo "Error: No cropped image data received.";
    }
}

ob_end_flush(); // Flush the output buffer
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Auction</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
</head>
<body>
    <div class="container py-5">
        <h2 class="h4 mb-4">Add New Auction <?php echo " ".$_SESSION["user_id"]." ".$_SESSION["user_name"]." ".$_SESSION["role"]; ?></h2>
        <form id="auctionForm" action="add-auction.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" id="title" name="title" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="start_price" class="form-label">Starting Price</label>
                <input type="number" id="start_price" name="start_price" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="start_time" class="form-label">Start Time</label>
                <input type="datetime-local" id="start_time" name="start_time" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="end_date" class="form-label">End Date</label>
                <input type="datetime-local" id="end_date" name="end_date" required class="form-control">
            </div>
            <div class="mb-3">
                <label for="productImage" class="form-label">Product Image</label>
                <input type="file" id="productImage" name="productImage" accept="image/jpeg, image/png, image/webp" required class="form-control">
                <input type="hidden" name="cropped_image" id="croppedImage"> <!-- Hidden field for cropped image data -->
            </div>

            <!-- Cropper Modal -->
            <div id="cropperModal" class="modal fade" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg"> <!-- Larger modal -->
                    <div class="modal-content">
                        <div class="modal-body">
                            <img id="cropperImage" class="img-fluid">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" id="cropButton" class="btn btn-primary">Crop</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Add Auction</button>
        </form>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const productImageInput = document.getElementById('productImage');
    const cropperModal = document.getElementById('cropperModal');
    const cropperImage = document.getElementById('cropperImage');
    const cropButton = document.getElementById('cropButton');
    const croppedImageInput = document.getElementById('croppedImage');

    let cropper;
    let modal; // To store the modal instance

    // Show the Cropper Modal on image selection
    productImageInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                cropperImage.src = e.target.result;
                modal = new bootstrap.Modal(cropperModal); // Initialize the modal
                modal.show();

                // Initialize Cropper.js with better settings for a more user-friendly experience
                cropper = new Cropper(cropperImage, {
                    aspectRatio: 16 / 9,  // Set aspect ratio to 16:9 (you can change this to suit your needs)
                    viewMode: 2,  // Allows free movement of the crop box
                    autoCropArea: 0.8,  // 80% of the image can be cropped
                    responsive: true,
                    zoomable: true,  // Allow zooming
                    scalable: true,  // Allow scaling of the cropped area
                    movable: true,   // Allow movement of the cropped area
                    minContainerWidth: 500,  // Minimum width of the crop container
                    minContainerHeight: 500, // Minimum height of the crop container
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // Crop the image on button click
    cropButton.addEventListener('click', function () {
        const canvas = cropper.getCroppedCanvas({
            width: 512,
            height: 512,
        });

        // Get the cropped image as a Data URL
        canvas.toBlob((blob) => {
            // Convert blob to base64 and assign it to the hidden input
            const reader = new FileReader();
            reader.onloadend = function () {
                croppedImageInput.value = reader.result; // Assign the base64 data to the hidden input
                modal.hide(); // Close the modal after cropping
                
                cropper.destroy(); // Destroy the cropper instance after cropping is done
            };
            reader.readAsDataURL(blob);
        });
    });
});
</script>

</body>
</html>
