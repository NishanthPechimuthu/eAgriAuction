<?php
  include("header.php");
  include("navbar.php");
  $id = base64_decode($_GET["id"]);
  $hero = getHeroById($id);
  $hero = $hero[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?=$hero["heroTitle"]?></title>
  <?php include("./assets/link.html"); ?>
</head>
<body>
  <div class="container mt-5">
    <div class="content mt-5">
      <div class="row">
        <!-- Image Section with rounded corners -->
        <div class="col-md-6 center">
          <img src="./images/<?=$hero['heroImg']?>" class="img-fluid rounded-1" alt="Hero Image">
        </div>
        <!-- Content Section -->
        <div class="col-md-6 mb-1">
          <h2><?=$hero["heroTitle"]?></h2>
          <p><?=$hero["heroContent"]?></p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
<?php include("footer.php"); ?>