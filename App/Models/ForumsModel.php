<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\Validate;
use App\Models\ForumCatsModel;
use App\Models\GroupsModel;
use App\Models\PostsModel;
use App\Models\ReadModel;
use App\Models\TopicsModel;

/*
* Forums Table Data
*/
class ForumsModel
{
    // Fetch Forums Row
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('forum_forums', $columns, $where, $values, $other);
		return $stmt;
	}
	
    // Returns the minimum read/write class levels of a forum
    public static function getForumAccessLevels($forumid)
    {
        $arr = DB::fetchRow('forum_forums', 'minclassread, minclasswrite', ['id='], [$forumid]);
        if (!$arr) {
            return false;
        }
        return array("read" => $arr["minclassread"], "write" => $arr["minclasswrite"]);
    }

    // Get Cats
    public static function getCatForums()
    {
        $list = [];
        $forum_cats = ForumCatsModel::fetchAll('*', '', 'ORDER BY sort');

        foreach ($forum_cats as $cat) {

            $country_id = $cat['id'];

            $country_info = [
                'c_id' => $country_id,
                'c_name' => $cat['name'],
                'c_sort' => $cat['sort'],
                'c_forums' => []
            ];

            $cat_forums = DB::fetchAll('forum_forums', '*', ['category='], [$country_id], 'ORDER BY sort, name');

            foreach ($cat_forums as $forum) {

                $f_id = $forum['id'];
                $count_topic = TopicsModel::fetchColumn('count(id)', ['forumid='], [$f_id]);
                $topic_id = TopicsModel::fetchColumn('count(id)', ['forumid='], [$f_id]);
                $count_post = PostsModel::fetchColumn('count(id)', ['topicid='], [$topic_id]);
                $country_info['c_forums'][$f_id] = [
                    'f_id' => $f_id,
                    'f_name' => $forum['name'],
                    'f_description' => $forum['description'],
                    'f_minclassread' => $forum['minclassread'],
                    'f_minclasswrite' => $forum['minclasswrite'],
                    'f_guest_read' => $forum['guest_read'],
                    'f_category' => $forum['category'],
                    'topic_count' => $count_topic,
                    'post_count' => $count_post
                ];
            }

            $list[$country_id] = $country_info;
        }

        return $list;
    }

    // Search Forum
    public static function search()
    {
        // Check User Input
        $keywords = (isset($_GET['keywords'])) ? trim(filter_input(INPUT_GET, 'keywords')) : '';
        $type = (isset($_GET['type'])) ? trim(filter_input(INPUT_GET, 'type')) : '';

        $data = [
            'FORUMS' => array(),
            'KEYWORDS' => $keywords,
            'TITLE' => LANG["FORUM"],
            'PAGER' => ''
        ];

        // Search Match
        if (!$keywords == '') {

        $keys = explode(" ", $keywords);
        foreach ($keys as $k) {
            $searcha[] = " forum_topics.subject LIKE '%$k%' ";
            $searchb[] = " forum_posts.body LIKE '%$k%' ";
            $searchc[] = " forum_topics.subject LIKE '%$k%' ";
            $searchd[] = " forum_posts.body LIKE '%$k%' ";
        }
        $whereasearch = '(' . implode(' OR ', $searcha) . ')';
        $wherebsearch = '(' . implode(' OR ', $searchb) . ')';
        $wherecsearch = implode(' OR ', $searchc);
        $wheredsearch = implode(' OR ', $searchd);
        $wherea = $whereasearch;
        $whereb = $wherebsearch;
        $wherec = $wherecsearch;
        $whered = $wheredsearch;
        if ($type == 'deep') {
            $res2 = DB::run("SELECT *, ( $wherea + $whereb ) as count_words
                             FROM forum_posts
                             JOIN forum_topics
                             ON forum_posts.topicid=forum_topics.id
                             JOIN forum_forums
                             ON forum_forums.id=forum_topics.forumid
                             WHERE ($whered)
                             ORDER BY count_words,added DESC");
            $count = $res2->rowCount();
            list($pager, $limit) = pagination(30, $count, URLROOT . "/forum/search?keywords=$keywords&type=deep&");
            $res = DB::run("SELECT *, ( $wherea + $whereb ) as count_words
                            FROM forum_posts
                            JOIN forum_topics
                            ON forum_posts.topicid=forum_topics.id
                            JOIN forum_forums
                            ON forum_forums.id=forum_topics.forumid
                            WHERE ($whered)
                            ORDER BY count_words,added DESC $limit")->fetchAll();
        } else {
            $res2 = DB::run("SELECT topicid, subject, forumid, forum_posts.added, forum_posts.userid, forum_forums.name, forum_forums.minclassread, forum_forums.guest_read, ( $wherea ) as count_words
                             FROM forum_topics
                             JOIN forum_posts
                             ON forum_posts.topicid=forum_topics.id
                             JOIN forum_forums
                             ON forum_forums.id=forum_topics.forumid
                             WHERE ($wherec)");
            $count = $res2->rowCount();
            list($pager, $limit) = pagination(30, $count, URLROOT . "/forum/search?keywords=$keywords&");

            $res = DB::run("SELECT distinct topicid, subject, forumid, forum_posts.added, forum_posts.userid, forum_forums.name, forum_forums.minclassread, forum_forums.guest_read, ( $wherea ) as count_words
                            FROM forum_topics
                            JOIN forum_posts
                            ON forum_posts.topicid=forum_topics.id
                            JOIN forum_forums
                            ON forum_forums.id=forum_topics.forumid
                            WHERE ($wherec)
                            ORDER BY count_words,added DESC $limit")->fetchAll();
        }

        $forums = ['res' => $res, 'pager' => $pager, 'count' => $count];
        if ($forums['count'] > 0) {   
            // Init Data
            $data['FORUMS'] = $forums['res'];
            $data['PAGER'] = $forums['pager'];
        } else {
            showMessage('forum/search', LANG["NOTHING_FOUND"]);
        }
    }

        return $data;
    }

    // Unread Topics
    public static function unread($data)
    {
        $stmt = DB::run("SELECT forum_readposts.lastpostread, forum_readposts.topicid,
                                forum_topics.id, forum_topics.forumid, forum_topics.subject, forum_topics.lastpost,
                                forum_forums.name, forum_forums.minclassread, forum_forums.guest_read
                        FROM forum_readposts 
                        JOIN forum_topics
                        ON forum_topics.id=forum_readposts.topicid
                        JOIN forum_forums
                        ON forum_forums.id=forum_topics.id
                        WHERE forum_readposts.userid=? 
                        ORDER BY forum_topics.lastpost DESC
                        LIMIT 5", [$data->id])->fetchAll();
        return $stmt;
    }

    // Admin Delete Cat
    public static function deleteCats($id, $action = 0)
    {
        if ($action == 0) {
            showMessage('adminforumcat', "Are You Sure <a href='".URLROOT."/adminforumcat/delete/$id/1'>Remove Cat $id</a>");
        }


        $v = ForumCatsModel::fetchRow('*', ['id='], [$id]);
        if (!$v) {
            showMessage('adminforumcat', LANG["FORUM_INVALID_CAT"]);
        }

        DB::delete('forumcats', ['id'=>$id]);
        $res = DB::fetchAll('forum_forums', 'id', ['category='], [$id]);
        foreach ($res as $row) {
            $res2 = TopicsModel::fetchAll('id', ['forumid='], [$row['id']]);
            foreach ($res2 as $arr) {
                PostsModel::delete(['topicid' => $arr['id']]);
                ReadModel::delete(['topicid' => $arr['id']]);
            }
            TopicsModel::delete(['forumid' => $row['id']]);
            DB::delete('forum_forums', ['id' => $row['id']]);
        }
        
        showMessage('adminforumcat', LANG["DEL_COMPLETE"]);
    }

    // Admin Get Cats
    public static function getCats()
    {
        $forumcat = ForumCatsModel::fetchAll('id, name, sort', '', 'ORDER BY sort, name');
        return $forumcat;
    }

    // Admin Reorder Cats
    public static function reorder($id, $move)
    {
        if ($move == 'up') {
            ForumCatsModel::update(['sort'], ['sort =', 'category =', 'id !='], [$_GET["sort"], $_GET["sort"] - 1, $id]);
            ForumCatsModel::update(['sort'], ['id ='], [($_GET["sort"] - 1), $id]);
        }

        if ($move == 'down') {
            ForumCatsModel::update(['sort'], ['id ='], [$_GET["sort"] + 1, $id]);
            ForumCatsModel::update(['sort'], ['sort =', 'category =', 'id !='], [$_GET["sort"], $_GET["sort"] + 1, $id]);
        }
        showMessage('adminforumcat', LANG["MOVED"]);
    }

    // Admin Reorder Forums
    public static function reorderForum($id, $forumid, $move)
    {
        if ($move == 'up') {
            DB::update('forum_forums', ['sort'], ['sort =', 'category =', 'id !='], [$_GET["sort"], $_GET["sort"] - 1, $id, $forumid]);
            DB::update('forum_forums', ['sort'], ['id ='], [($_GET["sort"] - 1), $id]);
        }

        if ($move == 'down') {
            DB::update('forum_forums', ['sort'], ['id ='], [$_GET["sort"] + 1, $id]);
            DB::update('forum_forums', ['sort'], ['sort =', 'category =', 'id !='], [$_GET["sort"], $_GET["sort"] + 1, $id, $forumid]);
        }
        showMessage(("adminforum/$id"), LANG["MOVED"]);
    }

    // Add/Edit Cat
    public static function insertOrUpdate()
    {
        $name = Validate::Input('name');
        $sort = Validate::Input('sort');
        $id = Validate::Input('id');
        
        if ($name == "") {
            showMessage('adminforumcat', LANG["MISSING_FORM_DATA"]);
        }
        
        // Admin Insert Forum Cat
        if (!$id) {
            $res = ForumCatsModel::insert(['name' => $name, 'sort' => intval($sort)]);
            if (!empty($res)) {
                showMessage('adminforumcat', LANG["SUCCESS"]);
            } else {
                showMessage('adminforumcat', LANG["ACTION_FAILED"]);
            }
        } else {
            ForumCatsModel::update(['sort','name'], ['id='], [$sort, $name, $id]);
            showMessage('adminforumcat', "<center><b>" . LANG["UPDATE_COMPLETE"] . "</b></center>");
        }
    }

    // Get User Forum Post By Id
    public static function getUsersPost($id, $current_user)
    {
        if ($current_user->view_users != "yes" && $current_user->id != $id) {
            showMessage(URLROOT, LANG["NO_USER_VIEW"]);
        }

        // Pagination
        $count = PostsModel::fetchColumn('count(id) as count', ['userid ='], [$id]);
        list($pager, $limit) = pagination(20, $count, Url("post/user/$id&"));
        $row = DB::run("SELECT
                        forum_posts.id, 
                        forum_posts.topicid, 
                        forum_posts.userid, 
                        forum_posts.added, 
                        forum_posts.body,
                        
                        users.avatar, 
                        users.signature, 
                        users.username, 
                        users.title, 
                        users.class, 
                        users.uploaded, 
                        users.downloaded, 
                        users.privacy, 
                        users.donated,
                        
                        forum_topics.subject,
                        forum_topics.id as tid,
                        forum_topics.forumid,

                        forum_forums.name
                        
                        FROM forum_posts
                        
                        LEFT JOIN users
                        ON forum_posts.userid = users.id
                        
                        LEFT JOIN forum_topics
                        ON forum_posts.topicid = forum_topics.id
            
                        LEFT JOIN forum_forums
                        ON forum_topics.forumid = forum_forums.id
                        
                        WHERE forum_posts.userid = $id 
                        ORDER BY forum_posts.added 
                        DESC $limit")->fetchAll();
        //echo '<pre>'.print_r($stmt, true).'</pre>';
        return array('row' => $row, 'pager' => $pager);
    }

    // Admin Get Forums
    public static function getForums($id)
    {
        $name = ForumCatsModel::fetchColumn('name', ['id='], [$id], 'ORDER BY sort, name');
        $query = ForumCatsModel::fetchAll('*', '', 'ORDER BY sort, name');
        $forumcat = array();
        foreach ($query as $row) {
            $forumcat[] = $row;
        }

        $query1 = DB::fetchAll('forum_forums', '*', ['sub=', 'category='], [0, $id], 'ORDER BY sort, name');
        $forumforum = array();
        foreach ($query1 as $row) {
            $forumforum[] = $row;
        }

        $query = DB::fetchAll('forum_forums', '*', ['category='], [$id], 'ORDER BY category, sub, sort');
        $groups = GroupsModel::fetchAll('*', '');
        $array = [
            'forumcat' => $forumcat,
            'forumforum' => $forumforum,
            'query' => $query,
            'name' => $name,
            'groups' => $groups
        ];
        return $array;
    }

    // Admin Add/Edit Forum
    public static function insertOrUpdateForum()
    {
        $name = Validate::Input('name');
        $description = Validate::Input('description');
        $sort = Validate::Input('sort');
        $new_forum_cat = Validate::Input('new_forum_cat');
        $minclassread = Validate::Input('minclassread');
        $minclasswrite = Validate::Input('minclasswrite');
        $guest_read = Validate::Input('guest_read');
        $sub = Validate::Input('sub');
        $id = Validate::Input('id');

        if (!$id) {
            $res = DB::insert('forum_forums', ['name' => $name, 'description' => $description, 'sort' => $sort, 'category' => $new_forum_cat, 'minclassread' => $minclassread, 'minclasswrite' => $minclasswrite, 'guest_read' => $guest_read, 'sub' => $sub]);
            $id = DB::lastInsertId();
            if ($res) {
                showMessage(("adminforum/$new_forum_cat"), LANG["SUCCESS"]);
            } else {
                showMessage(("adminforum/$new_forum_cat"), LANG["ACTION_FAILED"]);
            }
        } else {
            DB::update('forum_forums', ['sort','name','description','category','minclassread','minclasswrite','guest_read','sub'], ['id='], [$sort, $name, $description, $new_forum_cat, $minclassread, $minclasswrite, $guest_read, $sub, $id]);
            showMessage(("adminforum/$new_forum_cat"), "<center><b>" . LANG["UPDATE_COMPLETE"] . "</b></center>");
        }
    }

    // Admin Delete Forum
    public static function delete($id, $sure = 0)
    {
        if ($sure != 'sure') {
            showMessage('adminforum', "Are You Sure <a href='".URLROOT."/adminforum/delete/$id/sure'>Remove Forum $id</a>");
        }

        $v = DB::fetchRow('forum_forums', '*', ['id='], [$id]);
        if (!$v) {
            showMessage('adminforum', LANG["FORUM_INVALID"]);
        }

        DB::delete('forum_forums', ['id' => $id]);
        TopicsModel::delete(['forumid' => $id]);
        PostsModel::delete(['topicid' => $id]);
        ReadModel::delete(['topicid' => $id]);
        showMessage('adminforumcat', LANG["DEL_COMPLETE"]);
    }
}