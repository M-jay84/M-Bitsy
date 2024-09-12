<?php

use App\Libs\Cache;

 $TTCache = new Cache(); ?>
          
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
    <nav class="navbar navbar-expand-lg tt_navbar" style="height:58px;">
        <!-- Container wrapper -->
        <div class="container-fluid">
            <!-- Toggle button -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fa fa-bars fa-1x tticon"></i>
            </button>
            <!-- Collapsible wrapper -->
            <div class="collapse navbar-collapse" id="navbarText">
                <!-- Navbar brand -->
                <a class="navbar-brand mt-2 mt-lg-0" href="<?= Url() ?>">
                    <img src="<?= Url('assets/images/misc/logo.webp') ?>" height="45" alt="" loading="lazy" />
                </a>
            </div>
            <!-- Right Links -->
            <div class="d-flex align-items-center">
                <!-- Avatar -->
                <?php $avatar = htmlspecialchars($_SESSION['avatar']); ?>
                <?php if (!$avatar) : ?>
                    <?php $avatar = Url("assets/images/misc/default_avatar.webp"); ?>
                <?php endif; ?>
                <!-- User Account -->
                <div class="dropdown m-2">
                    <a href="#" data-bs-toggle="dropdown"><img class="rounded-circle me-lg-2" src="<?= $avatar ?>" alt="" style="width: 40px; height: 40px;"></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="<?= Url("profile/" . $current_user->id . "") ?>" class="dropdown-item"><?= LANG['PROFILE'] ?></a>
                        <a href="<?= Url('admincp') ?>" class="dropdown-item"><?= LANG['STAFF'] ?></a>
                        <a href="<?= Url('logout') ?>" class="dropdown-item"><?= LANG['LOGOUT'] ?></a>
                </div>
            </div>
    </nav>
    <!-- Content Start -->
    <div class="container-fluid" style="margin-top:5px;">
        <div class="row">
            <div class="col  portal_sidebar">
                <!-- Left Column Start -->
                <?php block_begin(); ?>
                <p class="text-center">
                <a href="<?= Url() ?>"><b><?= LANG["HOME"] ?></b></a><br>
                <a href="<?= Url('adminban') ?>"><b><?= LANG["BANNED"] ?></b></a><br>
                <a href="<?= Url('admincategorie') ?>"><b><?= LANG["TORRENT_CATEGORIES"] ?></b></a><br>
                <a href="<?= Url('adminclient/peers') ?>"><b><?= LANG["CLIENT_BAN"] ?></b></a><br>
                <a href="<?= Url('admincomment') ?>"><b><?= LANG["COMMENT"] ?></b></a><br>
                <a href="<?= Url('adminemail') ?>"><b><?= LANG["MASS_EMAIL"] ?></b></a><br>
                <a href="<?= Url('adminforumcat') ?>"><b><?= LANG["FORUM"] ?></b></a><br>
                <a href="<?= Url('admingroup') ?>"><b><?= LANG["GROUPS"] ?></b></a><br>
                <a href="<?= Url('admininactive') ?>"><b><?= LANG["INACTIVE"] ?></b></a><br>
                <a href="<?= Url('admininvite') ?>"><b><?= LANG["INVITED_USER"] ?></b></a><br>
                <a href="<?= Url('admininvite/pending') ?>"><b><?= LANG["PENDING_INVITE"] ?></b></a><br>
                <a href="<?= Url('adminlog') ?>"><b><?= LANG["SITE_LOG"]; ?></b></a><br>
                </p>
                <?php block_end(); ?>
                <?php require_once VIEW_PATH . "/inc/block/statistics_block.php"; ?>
            </div>
            <!-- Main Page Start -->
            <div class="col portalmain">
                <?php block_begin(); ?>
                <?php require_once VIEW_PATH . "/admin/admin-panel.php"; ?>
                <?php block_end(); ?>
                <?php require_once VIEW_PATH . "/admin/$file.php"; ?>
            </div>
            <!-- Right Column Start -->
            <div class="col  portal_sidebar">
                <?php block_begin('STAFF'); ?>
                <p class="text-center">
                <a href="<?= Url('adminmessage') ?>"><b><?= LANG["MESSAGES"] ?></b></a><br>
                <a href="<?= Url('adminnews') ?>"><b><?= LANG["NEWS"] ?></b></a><br>
                <a href="<?= Url('adminpeer') ?>"><b><?= LANG["PEERS"] ?></b></a><br>
                <a href="<?= Url('adminreport') ?>"><b><?= LANG["REPORT"] ?></b></a><br>
                <a href="<?= Url('admintask/clear') ?>"><b><?= LANG["CLEAR_SHOUTBOX"] ?></b></a><br>
                <a href="<?= Url('adminsnatch') ?>"><b><?= LANG["HNR"] ?></b></a><br>
                <a href="<?= Url('admintag') ?>"><b><?= LANG["TAGS"] ?></b></a><br>
                <a href="<?= Url('admintorrent') ?>"><b><?= LANG["TORRENT"] ?></b></a><br>
                <a href="<?= Url('adminusersearch') ?>"><b><?= LANG["USER_SEARCH"] ?></b></a><br>
                <a href="<?= Url('adminwarning') ?>"><b><?= LANG["WARNINGS"] ?></b></a><br>
                </p>
                <?php block_end(); ?>
                <?php require_once VIEW_PATH . "/inc/block/usersonline_block.php"; ?>
                <?php require_once VIEW_PATH . "/inc/block/newestmember_block.php"; ?>
                <?php require_once VIEW_PATH . "/inc/block/serverload_block.php"; ?>
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
                    <?php printf(LANG["PAGE_GENERATED_IN"], $totaltime); ?>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer End -->

    <!-- Jquery/Bootstrap Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Smileys/CheckAll JS  -->
    <script src="<?= Url('assets/js/smileIT_checkall.js') ?>"></script>
    <!-- Google Captcha JS  -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Highlight Code JS  -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.15.6/highlight.min.js"></script>
    <script>
        hljs.initHighlightingOnLoad();
    </script>
    <!-- Tags JS  -->
    <script type="module">
        import Tags from "<?= Url('assets/js/tags.js') ?>";
        Tags.init("select[multiple]");
    </script>

</body>

</html>
<?php ob_end_flush(); ?>