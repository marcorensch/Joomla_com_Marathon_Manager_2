<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Model;

\defined('_JEXEC') or die;

use AcyMailing\Classes\UserClass;

/**
 * NewsletterModel
 * @description This class is used to subscribe a user to a newsletter list based on AcyMailing Component
 *
 * @since  1.0.0
 */

class NewsletterModel
{

    private newsLetterUser $user;

    public function __construct()
    {

    }

    public function getUser(): newsLetterUser
    {
        return $this->user;
    }

    public function saveUser($email, $firstName, $lastName, $cmsUserId = null): newsLetterUser
    {
        $this->user = new newsLetterUser();
        $this->user->email = $email;
        $this->user->name = $firstName . ' ' . $lastName;
        $this->user->confirmed = 1;
        if($cmsUserId){
            $this->user->cms_id = $cmsUserId;
        }

        $userClass = new UserClass();
        $userClass->sendConf = true;
        $this->user->id = $userClass->save($this->user);

        return $this->user;
    }

    public function subscribe(int $listId): bool
    {
        $userClass = new UserClass();
        $subscribe = array($listId);
        if(!empty($subscribe) && $this->user->id){
            // The last two parameters are to make sure to send the welcome email
            return $userClass->subscribe($this->user->id, $subscribe, true, true);
        }
        return false;
    }
}

class newsLetterUser
{
    public $email;
    public $name;
    public $confirmed;
    public $id;
    public $active;
    public $key;
    public $creation_date;
    public $cms_id;

    public function __construct()
    {
    }

}