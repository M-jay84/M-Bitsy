<?php

use App\Libs\DB;

if ($_SESSION['id']) {
    block_begin(LANG["POLL"]);
    // Submit
    if (!empty($_POST) && $_SESSION['id'] && $_POST["act"] == "takepoll") {
        $choice = Input('choice');
        if ($choice != "" && $choice < 256 && $choice == floor($choice)) {
            $pollid = DB::fetchColumn('polls', 'id', '', 'ORDER BY added DESC LIMIT 1') or print("No Poll");
            $count = DB::fetchColumn('pollanswers', '*', ['pollid=', 'userid='], [$pollid,$_SESSION['id']]);
            if ($count) {
                print("You have already voted!");
            } else {
                $ins = DB::insert('pollanswers', ['pollid' => $pollid, 'userid' => $_SESSION['id'], 'selection' => $choice]);
                if (!$ins) {
                    print("An error occured. Your vote has not been counted.");
                }
            }
        } else {
            print("Please select an option.");
        }
    }

    // Get current poll
    $arr = DB::fetchRow('polls', '*', '', 'ORDER BY added DESC LIMIT 1');

    // Check if user has already voted
    if ($arr) {
        $arr2 =  DB::fetchRow('pollanswers', '*', ['pollid=','userid='], [$arr["id"],$_SESSION['id']]);
    }

    $o = array($arr["option0"], $arr["option1"], $arr["option2"], $arr["option3"], $arr["option4"],
    $arr["option5"], $arr["option6"], $arr["option7"], $arr["option8"], $arr["option9"],
    $arr["option10"], $arr["option11"], $arr["option12"], $arr["option13"], $arr["option14"],
    $arr["option15"], $arr["option16"], $arr["option17"], $arr["option18"], $arr["option19"]);

    //Display Current Poll
    if ($arr) {
        print("<p class='text-center'><strong>$arr[question]</strong></p>");
        
        // If member has voted already show results
        if ($arr2) {
            // we reserve 255 for blank vote.
            $res = DB::fetchAll('pollanswers', 'selection', ['pollid=', 'selection <'], [$arr['id'],20]);
            $total_votes = $res->rowCount();

            $i = 0;
            while ($a = $o[$i]) {
                $count = DB::fetchRow('pollanswers', 'count(selection) as count', ['pollid=','selection='], [$arr['id'],$i]);
                if ($total_votes == 0) {
                    $percent = 0;
                } else {
                    $percent = round($count / $total_votes * 100);
                }
                ?>
                <div class="row">
                    <div>
                        <b><?= format_comment($a) ?></b>
                    </div>
                    <div>
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="<?= $percent ?>" aria-valuemin="0" aria-valuemax="100" style="min-width: 2em; width: <?= $percent ?>%;">
                                <?= $percent ?>%
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                ++$i;
            }
            ?>
            <div class="text-center">
                <b><?= LANG["VOTES"] . ": " . $total_votes ?></span>
            </b></div>
            <?php
        } else {
            //User has not voted, show options
            ?>
            <form method='post' action='<?= encodehtml($_SERVER["REQUEST_URI"]); ?>'>
                <input type='hidden' name='act' value='takepoll' />
                <?php $i = 0; ?>
                <?php while ($a = $o[$i]) { ?>
                    <div class="radio">
                        <label>
                            <input type='radio' name='choice' value='<?= $i; ?>' /><?= format_comment($a); ?>
                        </label>
                    </div>
                <?php ++$i; ?>
                <?php } ?>
                <div class="radio">
                    <label>
                        <input type='radio' name='choice' value='255' /><?= LANG["BLANK_VOTE"]; ?>
                    </label>
                </div>
                <center>
                    <button type='submit' class="btn ttbtn center-block" /><?= LANG["VOTE"]; ?></button>
                </center>
            </form><?php
        }
    } else {
        print("<p class='text-center'>No Active Polls</p>");
    }
    block_end();
}