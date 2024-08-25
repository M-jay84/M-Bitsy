<?php
 use App\Libs\DB;
?>
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
            <!-- Left links -->
            <?php if ($_SESSION['id']) : ?>
                <form class="d-none d-md-flex ms-4" method='get' action='<?= Url('forum/search') ?>'>
                    <input class="form-control border-0" type="search" placeholder="Search" id="keywords" type="text" name="keywords">
                </form>
            <?php endif; ?>
        </div>

        <!-- Right Links -->
        <div class="d-flex align-items-center">
            <?php if ($_SESSION['id']) : ?>
                <?php
                // Querys
                $arr = DB::run("SELECT messages.*, users.avatar, users.username, users.class, `groups`.Color
                FROM messages 
                LEFT JOIN users ON users.id=messages.sender 
                LEFT JOIN `groups` ON groups.group_id=users.class
                WHERE receiver=" . $_SESSION['id'] . " and unread='yes' AND location IN ('in','both')"
                )->fetchAll();
                $unreadmail = count($arr);
                ?>

                <!-- messages -->
                <div class="dropdown m-2">
                    <a href="#" data-bs-toggle="dropdown"><i class="fa fa-envelope me-lg-2">
                    <?php if ($unreadmail !== 0) : ?>
                        <span class='badge rounded-pill badge-notification bg-danger'>(<?= $unreadmail ?>)</span></i></a>
                    <?php else : ?>
                        <span class='badge rounded-pill badge-notification bg-danger'></span></i></a>
                    <?php endif; ?>
                    <div class="dropdown-menu dropdown-menu-end">
                        <?php foreach ($arr as $message) : ?>
                            <?php $avatar = $message['avatar'] == '' ? Url('assets/images/misc/default_avatar.webp"')  : $message['avatar']; ?>
                            <a href="<?= Url("message/$message[id]/inbox") ?>" class="dropdown-item">
                            <div class="d-flex align-items-center">
                                <img class="rounded-circle" src="<?= $avatar ?>" alt="" style="width: 40px; height: 40px;">
                                <div class="ms-2">
                                    <h6 class="fw-normal mb-0"> <?= LANG['PM_BY'] ?> <span style="color:<?= $message['Color'] ?>;"><?= $message['username'] ?></span></h6>
                                    <small><?= $message['added']; ?></small>
                                </div>
                            </div>
                            </a>
                            <hr class="dropdown-divider">
                        <?php endforeach; ?>
                        <?php if ($unreadmail == 0) : ?>
                           <h6 class="fw-normal mb-0"><?= LANG['NO_NEW_MESSAGES'] ?></h6>
                           <hr class="dropdown-divider">
                        <?php endif; ?>
                        <a href="<?= Url('message/mailbox') ?>" class="dropdown-item text-center"><?= LANG['SEE_ALL_MESSAGES'] ?></a>
                    </div>
                </div>&nbsp;

                <!-- Avatar -->
                <?php $avatar = htmlspecialchars($_SESSION['avatar']); ?>
                <?php if (!$avatar) : ?>
                    <?php $avatar = Url("assets/images/misc/default_avatar.webp"); ?>
                <?php endif; ?>

                <!-- User Account -->
                <div class="dropdown m-2">
                    <a href="#" data-bs-toggle="dropdown"><img class="rounded-circle me-lg-2" src="<?= $avatar ?>" alt="" style="width: 40px; height: 40px;"></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="<?= Url("profile/" . $_SESSION['id'] . "") ?>" class="dropdown-item"><?= LANG['PROFILE'] ?></a>
                        <?php if ($_SESSION['class'] >=  CONFIG['_MODERATOR']) : ?>
                            <a href="<?= Url('admincp') ?>" class="dropdown-item"><?= LANG['STAFF'] ?></a>
                        <?php endif; ?>
                        <?php if ($_SESSION['id']) : ?>
                            <a href="<?= Url('logout') ?>" class="dropdown-item"><?= LANG['LOGOUT'] ?></a>
                        <?php endif; ?>
                    </div>
                </div>

        <?php else : ?>
            <a href="<?= Url('login') ?>"><?= LANG['LOGIN'] ?></a>
        <?php endif; ?>

    </div>
</nav>