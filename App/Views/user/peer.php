<?php block_begin($V_title) ?>
<?php usermenu($USER, $V_id);
if ($V_user['privacy'] != "strong" || ($USER->has('control_panel') == "yes") || ($USER->has('id') == $V_user['id'])) {
    echo peerstable($V_peers);
}
block_end();