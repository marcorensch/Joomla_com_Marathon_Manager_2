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
use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;

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

    public function saveUser($email, $firstName, $lastName, $cmsUserId = null): newsLetterUser | false
    {
		// Check E-Mail
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			error_log('Invalid E-Mail address: ' . $email);
			return false;
	    }
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

        // If the user already exists, we need to get the User ID by EMail
        if(!$this->user->id){
            $this->user->id = $this->getAcyMailingUserIDByEmail($email);
			if(!$this->user->id){
				error_log('Could not identify AcyMailing user by email: ' . $email);
				return false;
			}
        }

        return $this->user;
    }

    private function getAcyMailingUserIDByEmail($email): int | null
    {
        $db = Factory::getContainer()->get(DatabaseDriver::class);
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__acym_user');
        $query->where($db->quoteName('email') . ' = ' . $db->quote($email));
        $db->setQuery($query);
        return $db->loadResult();
    }

    public function subscribeToList(int $listId): bool
    {
        $userClass = new UserClass();
        $subscribe = array($listId);
        if(!empty($subscribe) && $this->user->id){
            // The last two parameters are to make sure to send the welcome email
            // Fire and forget - we don't need to wait for the result - returns false if already registered
            $userClass->subscribe($this->user->id, $subscribe, false, false);
            return true;
        }else{
            if(empty($subscribe)){
                error_log('No list to subscribe user ' . $this->user->id . ' to');
            }
            if(!$this->user->id){
                error_log('No user to subscribe to list ' . $listId);
            }
            return false;
        }
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