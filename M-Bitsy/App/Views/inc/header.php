<?php $GLOBALS['tstart'] = array_sum(explode(" ", microtime())); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo CONFIG['CHARSET'] ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="M-jay">
  <meta name="description" content="M-BiTsy is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker." />
  <meta name="keywords" content="https://github.com/M-jay84/M-BiTsy" />
  <title><?= $data['page_title'] ?></title>
  <!-- Bootstrap & Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/2da665f0cd.js" crossorigin="anonymous"></script>
  <!-- Custom CSS -->
  <link href="<?= URLROOT ?>/assets/css/default_style.css" rel="stylesheet">
  <!-- Highlight CSS -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/atom-one-dark.min.css">
  <!-- Ajax -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body class='d-flex flex-column min-vh-100'>
  <!-- Navbar -->
   <?php require VIEW_PATH . "/inc/navbar.php"; ?>
   <!-- Content Start -->
    <div class="container-fluid" style="margin-top:5px;">
      <div class="row">
        <div class="col  portal_sidebar">
        <?php
        if (!in_array($_GET['url'], CONFIG['ISURL'])) {
          include VIEW_PATH . "/inc/block/login_block.php";
          include VIEW_PATH . "/inc/block/maincats_block.php";
          include VIEW_PATH . "/inc/block/popular_block.php";
          include VIEW_PATH . "/inc/block/latestuploads_block.php";
          include VIEW_PATH . "/inc/block/mostactivetorrents_block.php";
          include VIEW_PATH . "/inc/block/latestimages_block.php";
        }

        ?>
        </div>
        <!-- Main Page Start -->
         <div class="col portalmain">