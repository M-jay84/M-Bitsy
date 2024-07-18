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


      <div class="col  portal_sidebar">
      </div>

      <div class="col portalmain">
      <?php block_begin("Info");
        echo "\n<meta http-equiv=\"refresh\" content=\"$data[V_time]; url=$data[V_url]\">\n"; ?>
        <div class='text-center'>
            <b><?= $data['V_msg']; 
            ?></b><br>
            <b>Redirecting ...</b>&nbsp; <b>[ <a href='<?= $data['V_url']; ?>'>link</a> ]</b>&nbsp;
        </div>
      <?php block_end(); ?>
      </div>
      <div class="col  portal_sidebar">
      </div>

    </div>
  </div>
  <!-- Content End -->

  <!-- Footer Start -->
  <footer class="mt-auto">
    <div class="tt_block rounded p-4">
      <div class="row">
        <div class="col-12 col-sm-6 text-center text-sm-start"><br>
          &copy; <a href="<?= Url() ?>"><?= CONFIG['SITENAME'] ?></a><br>
          RSS <a href="<?= Url('rss') ?>"><i class="fa fa-rss" aria-hidden="true" title="RSS Feed" style="color:#ff9900;"></i></a>
          <a href="<?= Url('rss/custom') ?>"><i class="fa fa-rss-square" aria-hidden="true" title="Custom RSS" style="color:#ffff00;"></i></a>
        </div>
        <div class="col-12 col-sm-6 text-center text-sm-end">
          M-BiTsy By <a href="https://github.com/M-jay84/M-BiTsy">M-jay</a>
          <br>Support @ <a href="https://torrenttrader.uk" target="_blank">Torrent Trader</a><br>
          <?php $totaltime = array_sum(explode(" ", microtime())) - $GLOBALS['tstart']; ?>
          <?php printf(Txt("PAGE_GENERATED_IN"), $totaltime); ?>
        </div>
      </div>
    </div>
  </footer>
  <!-- Footer End -->

  <!-- Jquery/Bootstrap Libraries -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<?php ob_end_flush(); ?>