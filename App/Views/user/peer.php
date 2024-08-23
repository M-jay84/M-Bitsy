<?php block_begin($page_title) ?>
<?php usermenu($V_id);
if ($V_user['privacy'] != "strong" || ($current_user->control_panel == "yes") || ($current_user->id == $V_user['id'])) {
    echo peerstable($V_peers);
}
block_end();