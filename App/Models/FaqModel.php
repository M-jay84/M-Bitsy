<?php

namespace App\Models;

use App\Libs\DB;

/*
* Faq Table Data
*/
class FaqModel
{
    // Get All Faqs
    public static function select()
    {
        $res = DB::fetchAll('faq', 'id, question, answer, flag, type', ['type='], ['categ'], 'ORDER BY `order` ASC');
        foreach ($res as $arr) {
            $faq_categ[$arr['id']]['id'] = $arr['id'];
            $faq_categ[$arr['id']]['title'] = $arr['question'];
            $faq_categ[$arr['id']]['answer'] = $arr['answer'];
            $faq_categ[$arr['id']]['flag'] = $arr['flag'];
            $faq_categ[$arr['id']]['type'] = $arr['type'];
        }
        $res = DB::fetchAll('faq', 'id, type, question, answer, flag, categ', ['type='], ['item'], 'ORDER BY `order` ASC');
        foreach ($res as $arr) {
            $faq_categ[$arr['categ']]['items'][$arr['id']]['id'] = $arr['id'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['title'] = $arr['question'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['answer'] = $arr['answer'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['flag'] = $arr['flag'];
            $faq_categ[$arr['categ']]['items'][$arr['id']]['type'] = $arr['type'];
        }

        return $faq_categ;
    }

    // Delete Faq
    public static function delete($id, $sure = 0)
    {
        if ($sure) {
            DB::delete('faq', ['id' => $id], 1);
            DB::deleteByIds('faq', 'categ', $id);
            Redirect(Url('faq'), Txt("ACTION_COMPLETE"));
        } else {
            Redirect(Url('faq'),  "Please click <a href=\"" . URLROOT . "/faq/delete/$id/1\">here</a> to confirm.");
        }
    }

    // Add/Edit Section
    public static function insertOrUpdateSection()
    {
        $id = Input('id');
        $title = Input('title');
        $flag = Input('flag');
        // Add New Else Edit todo
        if (empty($id)) {
            if ($title != null) {
                $res = DB::fetchColumn('faq', 'COUNT(`order`)', ['type='], ['categ']);
                $order = (int) $res + 1;
                DB::insert('faq', ['type'=>'categ', 'question'=>$title, 'answer'=>'', 'flag'=>$flag, 'order'=>$order, 'categ'=>0]); // todo , 'order'=>$order
            }
        } else {
            DB::update('faq', ['question','answer','flag','categ'], ['id='], [$title, '', $flag, 0, $id]);
        }
        Redirect(Url('faq'), Txt("ACTION_COMPLETE"));
    }

    // Add/Edit Item
    public static function insertOrUpdateItem()
    {
        // Check User Input
        $id = Input('id');
        $flag = Input('flag');
        $question = Input('title');
        $answer = Input('answer');
        $type = Input('type');

        // Add New Else Edit
        if ($type == 'new') { // error if id we edit and select
            $res = DB::fetchColumn('faq', 'COUNT(`order`)', ['type=','categ='], ['item', $id]);
            $order = $res + 1;
            DB::insert('faq', ['type'=>'item', 'question'=>$question, 'answer'=>$answer, 'flag'=>$flag, 'categ'=>$id]); // todo , 'order'=>$order
            Redirect(Url('faq'), Txt("SUCCESS"));
        } else {
            DB::update('faq', ['question', 'answer', 'flag'], ['id='], [$question, $answer, $flag, $id]);
            Redirect(Url('faq'), Txt("UPDATE_COMPLETE"));
        }
    }
}