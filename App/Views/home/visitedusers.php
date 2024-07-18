<?php

use App\Models\UsersModel;

$stmt = UsersModel::fetchAll(
    'id,username,class,donated,warned,avatar', 
    ['enabled =', 'status =', 'privacy !=', "UNIX_TIMESTAMP(?) - UNIX_TIMESTAMP(users.last_access) <="],
    ['yes', 'confirmed', 'strong', get_date_time(), 86400]
);

block_begin(Txt('VISITED_USERS'));
foreach ($stmt as $row) {
    $avatar = htmlspecialchars($row["avatar"]);
    if (!$avatar) {
        $avatar = Url('assets/images/misc/default_avatar.webp');
    } ?>
    <img src='<?= $avatar; ?>' width='15px' height='15px'>
    <a href="<?= Url("profile/$row[id]") ?>">
    <small><b><?= coloredname($row["id"], $row) ?></b></small></a>&nbsp; 
<?php
}
block_end();