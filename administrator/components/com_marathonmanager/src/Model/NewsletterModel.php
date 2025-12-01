<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Model;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
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

	/**
	 * Check if AcyMailing is installed and available
	 *
	 * @return bool
	 * @since 2.0.1
	 */
	private function isAcyMailingAvailable(): bool
	{
		$acyHelper = JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		if (!file_exists($acyHelper)) {
			Log::add('AcyMailing helper file not found at: ' . $acyHelper, Log::ERROR, 'com_marathonmanager.newsletter');

			try {
				Factory::getApplication()->enqueueMessage(
					Text::_('COM_MARATHONMANMANAGER_FIELD_ACYM_NOT_FOUND_ERROR'),
					'error'
				);
			} catch (\Exception $e) {
				// Application not available (e.g. CLI context)
				Log::add('Could not enqueue message: ' . $e->getMessage(), Log::WARNING, 'com_marathonmanager.newsletter');
			}

			return false;
		}

		include_once($acyHelper);

		if (!class_exists('AcyMailing\Classes\UserClass')) {
			Log::add('AcyMailing UserClass not found after loading helper', Log::ERROR, 'com_marathonmanager.newsletter');
			return false;
		}

		return true;
	}

    public function getUser(): newsLetterUser
    {
        return $this->user;
    }

    public function saveUser($email, $firstName, $lastName, $cmsUserId = null): newsLetterUser | false
    {
	    // Check if AcyMailing is available forward exception
	    if (!$this->isAcyMailingAvailable())
	    {
		    return false;
	    }

		Log::add('Saving user ' . $email . ", " . $firstName . ", " . $lastName, Log::INFO, 'com_marathonmanager.newsletter');

		// Check E-Mail
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			error_log('Invalid E-Mail address: ' . $email);
			Log::add('Invalid E-Mail address: ' . $email, Log::ERROR, 'com_marathonmanager.newsletter');
			return false;
	    }
        $this->user = new newsLetterUser();
        $this->user->email = $email;
        $this->user->name = $firstName . ' ' . $lastName;
        $this->user->confirmed = 1;
        if($cmsUserId){
            $this->user->cms_id = $cmsUserId;
        }

        $userClass = new \AcyMailing\Classes\UserClass();
        $userClass->sendConf = false;
        $this->user->id = $userClass->save($this->user);

        // If the user already exists, we need to get the User ID by EMail
        if(!$this->user->id){
            $this->user->id = $this->getAcyMailingUserIDByEmail($email);
			if(!$this->user->id){
				Log::add('Could not identify AcyMailing user by email: ' . $email, Log::ERROR, 'com_marathonmanager.newsletter');
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
	    if (!$this->isAcyMailingAvailable())
	    {
		    return false;
	    }

	    $userClass = new \AcyMailing\Classes\UserClass();
        $subscribe = array($listId);
        if(!empty($subscribe) && $this->user->id){
            // The last two parameters are to make sure to send the welcome email
            // Fire and forget - we don't need to wait for the result - returns false if already registered
            $userClass->subscribe($this->user->id, $subscribe, false, false);
            return true;
        }else{
            if(empty($subscribe)){
				Log::add('No list to subscribe user ' . $this->user->id . ' to', Log::WARNING, 'com_marathonmanager.newsletter');
                error_log('No list to subscribe user ' . $this->user->id . ' to');
            }
            if(!$this->user->id){
				Log::add('No user to subscribe to list ' . $listId, Log::INFO, 'com_marathonmanager.newsletter');
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