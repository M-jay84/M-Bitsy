<?php
use App\Models\MessagesModel;
use App\Models\NotificationsModel;
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
            <?php if ($data['USER']->has('id')) : ?>
                <form class="d-none d-md-flex ms-4" method='get' action='<?= Url('forum/search') ?>'>
                    <input class="form-control border-0" type="search" placeholder="Search" id="keywords" type="text" name="keywords">
                </form>
            <?php endif; ?>
        </div>

        <!-- Right Links -->
        <div class="d-flex align-items-center">
            <?php if ($data['USER']->has('id')) : ?>
                <?php
                // Querys
                $arr = MessagesModel::fetchMessages();
                $unreadmail = count($arr);
				$arr1 = NotificationsModel::fetchAll('*', ['user_id =', 'notifications.read ='], [$data['USER']->has('id'), 'no']);
                $unreadnotif = $arr1->rowCount();
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
                                    <h6 class="fw-normal mb-0"> <?= Txt('PM_BY') ?> <span style="color:<?= $message['Color'] ?>;"><?= $message['username'] ?></span></h6>
                                    <small><?= get_time_elapsed($message['added']); ?></small>
                                </div>
                            </div>
                            </a>
                            <hr class="dropdown-divider">
                        <?php endforeach; ?>
                        <?php if ($unreadmail == 0) : ?>
                           <h6 class="fw-normal mb-0"><?= Txt('NO_NEW_MESSAGES') ?></h6>
                           <hr class="dropdown-divider">
                        <?php endif; ?>
                        <a href="<?= Url('message/mailbox') ?>" class="dropdown-item text-center"><?= Txt('SEE_ALL_MESSAGES') ?></a>
                    </div>
                </div>&nbsp;

				<!-- Notififcatation -->
                <div class="dropdown m-2">
                    <a href="#" data-bs-toggle="dropdown"><i class="fa fa-bell me-lg-2">
                    <?php if ($unreadnotif !== 0) : ?>
                        <span class='d-none d-lg-inline-flex'><?= Txt('NOTIFICATION') ?> (<?= $unreadnotif ?>)</span></i></a>
                    <?php else : ?>
                        <span class='badge rounded-pill badge-notification bg-danger'></span></i></a>
                    <?php endif; ?>
                    <div class="dropdown-menu dropdown-menu-end">
                        <?php foreach ($arr1 as $message) : ?>
                            <a href="<?= Url("notification/read/$message[id]") ?>" class="dropdown-item">
                            <div class="d-flex align-items-center">
                                <div class="ms-2">
                                    <h6 class="fw-normal mb-0"><?= Cutname($message['title'], 15); ?></h6>
                                    <small><?= get_time_elapsed($message['added']); ?></small>
                                </div>
                            </div>
                            </a>
                            <hr class="dropdown-divider">
                        <?php endforeach; ?>
                        <?php if ($unreadnotif == 0) : ?>
                            <h6 class="fw-normal mb-0"><?= Txt('NO_NEW_NOTIFICATIONS') ?></h6>
                            <hr class="dropdown-divider">
                        <?php endif; ?>
                        <a href="<?= Url('notification') ?>" class="dropdown-item text-center"><?= Txt('SEE_ALL_NOTIFICATIONS') ?></a>
                    </div>
                </div>

                <!-- Avatar -->
                <?php $avatar = htmlspecialchars($data['USER']->has('avatar')); ?>
                <?php if (!$avatar) : ?>
                    <?php $avatar = Url("assets/images/misc/default_avatar.webp"); ?>
                <?php endif; ?>

                <!-- User Account -->
                <div class="dropdown m-2">
                    <a href="#" data-bs-toggle="dropdown"><img class="rounded-circle me-lg-2" src="<?= $avatar ?>" alt="" style="width: 40px; height: 40px;"></a>
                    <div class="dropdown-menu dropdown-menu-end">
                        <a href="<?= Url("profile/" . $data['USER']->has('id') . "") ?>" class="dropdown-item"><?= Txt('PROFILE') ?></a>
                        <?php if ($data['USER']->has('class') > _MODERATOR) : ?>
                            <a href="<?= Url('admincp') ?>" class="dropdown-item"><?= Txt('STAFF') ?></a>
                        <?php endif; ?>
                        <?php if ($data['USER']->has('id')) : ?>
                            <a href="<?= Url('logout') ?>" class="dropdown-item"><?= Txt('LOGOUT') ?></a>
                        <?php endif; ?>
                    </div>
                </div>

        <?php else : ?>
            <a href="<?= Url('login') ?>"><?= Txt('LOGIN') ?></a>
        <?php endif; ?>

    </div>
</nav>