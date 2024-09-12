<?php

namespace App\Controllers\Admin;

use App\Libs\Auth;
use App\Libs\Controller;
use App\Models\TagsModel;

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
          $this->showMessage(URLROOT, LANG["NO_PERMISSION"]);
      }
  }

  // Tag Default Page
  public function index()
  {
    if (!empty($_POST)) {
      TagsModel::insertOrUpdate();
    } else {
      
      // Data For View
      $data = [
        'page_title' => LANG['TAGS'],
        'V_tags' => TagsModel::getAll()
      ];

      // Load View
      $this->view('admin-tag', $data, 'admin');
    }
  }

  public function delete()
  {
    TagsModel::delete();
  }
}