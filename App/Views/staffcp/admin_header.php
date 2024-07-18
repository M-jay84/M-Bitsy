<?php
// Micro Time
$GLOBALS['tstart'] = array_sum(explode(" ", microtime()));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=<?= CHARSET ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="author" content="M-jay" />
  <meta name="generator" content="M-BiTsy" />
  <meta name="description" content="M-BiTsy is a feature packed and highly customisable PHP/PDO/MVC Based BitTorrent tracker." />
  <meta name="keywords" content="https://github.com/M-jay84/M-BiTsy" />
  <title><?= $data['V_title'] ?></title>

  <!-- Bootstrap & Core CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/6f36cea5db.js" crossorigin="anonymous"></script>
  <!-- Custom CSS -->
  <link href="<?= URLROOT ?>/assets/themes/default/customstyle.css" rel="stylesheet">
  <!-- Highlight CSS -->
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/styles/atom-one-dark.min.css">
  <!-- Ajax -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>

<body class='d-flex flex-column min-vh-100'>

<!-- Navbar -->
<?php require APPROOT."/Views/staffcp/admin_navbar.php"; ?>

<!-- Content Start -->
<div class="container-fluid">
<div class="content">

<!-- Sidebar -->
<?php require APPROOT."/Views/staffcp/admin_sidebar.php"; ?>

<!-- Admin Panel -->
<?php require APPROOT."/Views/staffcp/admin_panel.php"; ?>