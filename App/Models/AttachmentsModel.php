<?php

namespace App\Models;

use App\Libs\DB;
use App\Libs\SimpleThumbnail;

/*
* Attachments Table Data
*/
class AttachmentsModel
{
    // Get Attachment
    public static function fetchRow($columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = DB::fetchRow('attachments', $columns, $where, $values, $other);
        return $stmt;
    }

    // Delete Attachment
    public static function deleteAll($id, $post = false)
    {
        if ($post) {
            $sql = DB::fetchAll('attachments', '*', ['content_id ='], [$id]);
        } else {
            $sql = DB::fetchAll('attachments', '*', ['topicid='], [$id]);
        }
        if ($sql->rowCount() != 0) {
            foreach ($sql as $row7) {
                //print("<br>&nbsp;<b>$row7[filename]</b><br>");
                $daimage = UPLOADDIR . "/attachment/$row7[file_hash].data";
                if (file_exists($daimage)) {
                    if (unlink($daimage)) {
                        DB::delete('attachments', ['content_id' => $row7['id']]);
                    }
                }
                $extension = substr($row7['filename'], -3);
                if ($extension != 'zip') {
                    $dathumb = "uploads/thumbnail/$row7[file_hash].jpg";
                    if (!unlink($dathumb)) {
                        Redirect(Url("post/$id"), "Could not remove thumbnail = $row7[file_hash].jpg");
                    }
                }
            }
        }
    }

    // Get Attachment
    public static function get_attachment($id)
    {
        $sql = DB::fetchAll('attachments', '*', ['content_id ='], [$id]);
        if ($sql->rowCount() != 0) {
            foreach ($sql as $row7) {
                print("<br><br>&nbsp;<b>$row7[filename]</b><br>");
                $extension = substr($row7['filename'], -3);
                if ($extension == 'zip' || $extension == 'rar' || $extension == 'php') {
                    $daimage = UPLOADDIR . "/attachment/$row7[file_hash].data";
                    if (file_exists($daimage)) {
                        print(" <a class='btn btn-sm ttbtn' href=\"" . URLROOT . "/attachment/$row7[id]/" . rawurlencode($row7["file_hash"]) . "\"><i class='fa fa-file-archive-o tticon' ></i>" . Txt('DOWNLOAD') . "</a><br>");
                    } else {
                        print("no zip<br>");
                    }
                } else {
                    $daimage = "uploads/thumbnail/$row7[file_hash].jpg";
                    if (file_exists($daimage)) {
                        $switchimage = UPLOADDIR . "/attachment/$row7[file_hash].data"; ?>
                        <a href='<?php echo URLROOT ?>/gallery/images/<?php echo $row7['file_hash'] ?>'><img alt="test image" src="<?php echo data_uri($switchimage, $row7['filename']); ?>" height="100px" width="100px"></a>
                        <?php
                    } else {
                        print("<a href=\"" . URLROOT . "/attachment/$row7[id]/hash=" . rawurlencode($row7["file_hash"]) . "\"><img src='" . URLROOT . "/thumb/$row7[file_hash].jpg' height='110px' width='110px' border='0' alt='' /></a><br>");
                    }
                }
            }
        }
    }

    // Check If File Array Exist
    public static function any_uploaded($name)
    {
        foreach ($_FILES[$name]['error'] as $ferror) {
            if ($ferror != UPLOAD_ERR_NO_FILE) {
                return true;
            }
        }
        return false;
    }

    // Set Attachment
    public static function set_attachment($topicid, $postid)
    {
        if (self::any_uploaded('upfile')) :
            $array = array($_FILES['upfile']['name']);
            $result = array_filter($array);
            foreach ($result as $k => $ar) :
                // Source, Size & Name
                $sourcePath = $_FILES['upfile']['tmp_name'][$k];
                $fileSize = $_FILES['upfile']['size'][$k];
                $fileName = $_FILES['upfile']['name'][$k];
                // Extension & Hash
                $extension = substr($fileName, -3);
                $hash = bin2hex(random_bytes(25));

                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'rar', 'php', 'jpeg');
                if (!in_array($extension, $allowedfileExtensions)) {
                    Redirect(Url('forum'), "Sorry, only zip, rar, php, JPG, JPEG, PNG, GIF files are allowed.");
                }

                $targetPath = UPLOADDIR . "/attachment/" . $hash . ".data"; // Target path where file is to be stored
                $thumbPath = "uploads/thumbnail/" . $hash . ".jpg"; // Target path where attachment as jpg is to be stored

                if ($extension == 'zip' || $extension == 'rar' || $extension == 'php') {
                    move_uploaded_file($sourcePath, $targetPath); // Moving Uploaded file
                    DB::insert('attachments', [
                        'content_id' => $postid, 'user_id' => $_SESSION['id'], 'upload_date' => gmtime(), 'filename' => $fileName,
                        'file_size' => $fileSize, 'file_hash' => $hash, 'topicid' => $topicid
                    ]);
                } else {
                    if (move_uploaded_file($sourcePath, $targetPath)) { // Moving Uploaded file
                        SimpleThumbnail::create()->image($targetPath)->thumbnail(100)->to($thumbPath);
                        DB::insert('attachments', [
                            'content_id' => $postid, 'user_id' => $_SESSION['id'], 'upload_date' => gmtime(), 'filename' => $fileName,
                            'file_size' => $fileSize, 'file_hash' => $hash, 'topicid' => $topicid
                        ]);
                    }
                }
            endforeach;
        endif;
    }
}