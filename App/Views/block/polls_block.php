<?php

use App\Models\PollsAnswerModel;
use App\Models\PollsModel;

if ($USER->has('id')) {
    block_begin(Txt("POLL"));
    // Submit
    if (!empty($_POST) && $USER->has('id') && $_POST["act"] == "takepoll") {
        $choice = Input('choice');
        if ($choice != "" && $choice < 256 && $choice == floor($choice)) {
            $pollid = PollsModel::fetchColumn('id', '', 'ORDER BY added DESC LIMIT 1') or print("No Poll");
            $count = PollsAnswerModel::fetchColumn('*', ['pollid=', 'userid='], [$pollid,$USER->has('id')]);
            if ($count) {
                print("You have already voted!");
            } else {
                $ins = PollsAnswerModel::insert(['pollid' => $pollid, 'userid' => $USER->has('id'), 'selection' => $choice]);
                if (!$ins) {
                    print("An error occured. Your vote has not been counted.");
                }
            }
        } else {
            print("Please select an option.");
        }
    }

    // Get current poll
    $arr = PollsModel::fetchRow('*', '', 'ORDER BY added DESC LIMIT 1');

    // Check if user has already voted
    if ($arr) {
        $arr2 =  PollsAnswerModel::fetchRow('*', ['pollid=','userid='], [$arr["id"],$USER->has('id')]);
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
            $res = PollsAnswerModel::fetchAll('selection', ['pollid=', 'selection <'], [$arr['id'],20]);
            $total_votes = $res->rowCount();

            $i = 0;
            while ($a = $o[$i]) {
                $count = PollsAnswerModel::fetchColumn('count(selection) as count', ['pollid=','selection='], [$arr['id'],$i]);
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
                <b><?= Txt("VOTES") . ": " . $total_votes ?></span>
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
                        <input type='radio' name='choice' value='255' /><?= Txt("BLANK_VOTE"); ?>
                    </label>
                </div>
                <center>
                    <button type='submit' class="btn ttbtn center-block" /><?= Txt("VOTE"); ?></button>
                </center>
            </form><?php
        }
    } else {
        print("<p class='text-center'>No Active Polls</p>");
    }
    block_end();
}