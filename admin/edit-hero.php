<?php
ob_start();
session_start();
include("header.php");
include("navbar.php");

$heroId = $_GET['heroId'] ?? null;
if ($heroId === null) {
    // Redirect if heroId is not passed
    header("Location: manage-hero.php");
    exit();
}

$hero = getHeroById($heroId); // Function to fetch hero details by ID
$hero = $hero[0];
if (!$hero) {
    // If hero not found, redirect
    header("Location: manage-hero.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $title = htmlspecialchars(trim($_POST['title']));
    $message = htmlspecialchars(trim($_POST['message']));
    $content = htmlspecialchars($_POST['content']);
    $status = $_POST["status"];

    // Handle image upload
    $uploadDir = '../images/heroes/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $oldImage = $hero['heroImg'];
    $imgHero = $oldImage; // Default to the old image if no new image is uploaded

    if (!empty($_POST['cropped_image'])) {
        // Handle cropped image (base64)
        $croppedImageData = $_POST['cropped_image'];
        list(, $croppedImageData) = explode(',', $croppedImageData);
        $croppedImageData = base64_decode($croppedImageData);

        // Generate a unique name for the new image
        $uniqueName = 'hero_' . uniqid() . '.webp';
        $targetFile = $uploadDir . $uniqueName;

        if (file_put_contents($targetFile, $croppedImageData)) {
            // Remove the old image if it exists
            if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                unlink($uploadDir . $oldImage);
            }

            $imgHero = $uniqueName;
        } else {
            echo '<p class="alert alert-warning">Error: Failed to save the cropped image.</p>';
        }
    } elseif (!empty($_FILES['heroImage']['name'])) {
        // Handle file upload via $_FILES
        $fileName = $_FILES['heroImage']['name'];
        $fileTmp = $_FILES['heroImage']['tmp_name'];
        $fileError = $_FILES['heroImage']['error'];
        $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);

        if ($fileError === 0) {
            $uniqueName = 'hero_' . uniqid() . '.' . $fileExt;
            $targetFile = $uploadDir . $uniqueName;

            if (move_uploaded_file($fileTmp, $targetFile)) {
                // Remove the old image if it exists
                if (!empty($oldImage) && file_exists($uploadDir . $oldImage)) {
                    unlink($uploadDir . $oldImage);
                }

                $imgHero = $uniqueName;
            } else {
                echo '<p class="alert alert-danger">Error: Failed to upload the new image.</p>';
            }
        } else {
            echo '<p class="alert alert-danger">Error: Image upload failed with error code ' . $fileError . '.</p>';
        }
    }

    // Update the hero details in the database
    $result = updateHero($heroId, $title, $message, $content, $status, $imgHero);

    // Show success or error message
    if ($result) {
        echo '<p class="alert alert-success">Hero updated successfully.</p>';
    } else {
        echo '<p class="alert alert-danger">Error updating hero.</p>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hero</title>
    <? include_once("../assets/link.html"); ?>
</head>
<body>
<div class="container py-5 mt-5">
    <div class="card mb-4">
        <div class="card-header">
            <i class="bi bi-pencil-square"></i>&nbsp; Edit Hero
        </div>
        <div class="card-body">
            <form id="heroForm" action="edit-hero.php?heroId=<?= $heroId ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($hero['heroTitle']) ?>" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <input type="text" id="message" name="message" value="<?= htmlspecialchars($hero['heroMessage']) ?>" required class="form-control">
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Content</label>
                    <textarea id="content" name="content" required class="form-control"><?= htmlspecialchars($hero['heroContent']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Hero Status</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="activate" <?= $hero['heroStatus'] === 'activate' ? 'selected' : '' ?>>Activate</option>
                        <option value="deactivate" <?= $hero['heroStatus'] === 'deactivate' ? 'selected' : '' ?>>Deactivate</option>
                        <option value="suspend" <?= $hero['heroStatus'] === 'suspend' ? 'selected' : '' ?>>Suspend</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="heroImage" class="form-label">Hero Image</label>
                    <input type="file" name="heroImage" class="form-control" id="heroImage" accept="image/*">
                </div>

                <!-- Image preview -->
                <div class="mb-3">
                    <label for="imagePreview" class="form-label">Preview Image</label>
                    <img id="imagePreview" src="<?= isset($hero['heroImg']) ? '../images/heroes/' . $hero['heroImg'] : '' ?>" alt="Image Preview" class="img-fluid rounded-1 border border-2 border-dark" />
                </div>

                <!-- Hidden input for cropped image -->
                <input type="hidden" name="cropped_image" id="cropped_image">

                <button type="submit" class="btn btn-primary">Update Hero</button>
            </form>
        </div>
    </div>
</div>

<script>
  // Initialize the cropper.js for the hero image
  let cropper;
  document.getElementById('heroImage').addEventListener('change', function (event) {
      const file = event.target.files[0];
      const reader = new FileReader();

      reader.onload = function (e) {
          const image = document.getElementById('imagePreview');
          image.src = e.target.result;

          // Initialize Cropper.js
          if (cropper) {
              cropper.destroy();
          }
          cropper = new Cropper(image, {
              aspectRatio: 1,
              viewMode: 1,
              autoCropArea: 0.8
          });
      };

      if (file) {
          reader.readAsDataURL(file);
      }
  });

  // Handle cropping
  document.getElementById('cropButton').addEventListener('click', function () {
      const canvas = cropper.getCroppedCanvas({
          width: 500,
          height: 500
      });
      const croppedImage = canvas.toDataURL('image/webp');
      document.getElementById('cropped_image').value = croppedImage;
  });
</script>
</body>
</html>
<?php
include_once("./footer.php");
ob_end_flush();
?>