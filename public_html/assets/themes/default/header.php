<?php $GLOBALS['tstart'] = array_sum(explode(" ", microtime())); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?= CHARSET ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="M-jay">
  <meta name="description" content="M-BiTsy is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker." />
  <meta name="keywords" content="https://github.com/M-jay84/M-BiTsy" />
  <title><?= $data['V_title'] ?></title>
  <!-- Bootstrap & Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/6f36cea5db.js" crossorigin="anonymous"></script>
  <!-- Custom CSS -->
  <link href="<?= URLROOT ?>/assets/themes/<?php echo isset($_SESSION['stylesheet']) ? $_SESSION['stylesheet'] : CONFIG['DEFAULTTHEME'] ?>/customstyle.css" rel="stylesheet">
  <!-- Highlight CSS -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/atom-one-dark.min.css">
  <!-- Ajax -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body class='d-flex flex-column min-vh-100'>

<!-- Navbar -->
<?php require "assets/themes/default/navbar.php"; ?>

<!-- Content Start -->
<div class="container-fluid" style="margin-top:5px;">
<div class="row">

<!-- Left Block Start -->
<?php if (CONFIG['LEFTNAV']) : ?>
<div class="col  portal_sidebar">
  <?php column('left', $data['USER']); ?>
</div>
<?php endif; ?>

<!-- Main Page Start -->
<div class="col portalmain">