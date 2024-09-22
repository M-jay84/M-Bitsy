<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TagsLogic;

/*
* Tags Controller
*/
class Admintag extends Controller
{
  public $current_user;

  // Get Current User
  public function __construct()
  {
    $this->current_user = new Auth();

    if (!$this->current_user->isStaff) {
      showMessage(URLROOT, LANG["NO_PERMISSION"]);
    }
  }

  // Tags
  public function index()
  {
    if (!empty($_POST)) {
      // Add/Edit Tags
      TagsLogic::insertOrUpdate();
    }

    // Get All Tags
    $tags = TagsLogic::getAll();

    // Data For View
    $data = [
      'TITLE' => LANG['TAGS'],
      'TAGS' => $tags
    ];

      // Load View
    $this->template('admin-tag', $data, 'admin');
  }

  // Delete Tags
  public function delete()
  {
    TagsLogic::delete();
  }
}