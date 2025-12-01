<?php

namespace NXD\Component\MarathonManager\Site\Helper;

// phpcs:disable PSR1.Files.SideEffects
use Joomla\CMS\Log\Log;

\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AcyMailingHelper
{
    public static function sendConfirmationMail($data, $emailId): void
    {
		error_log('AcyMailingHelper::sendConfirmationMail');
		error_log(print_r($data, true));
		error_log(print_r($emailId, true));
		error_log('----------------------------------------');

	    $ds = DIRECTORY_SEPARATOR;
	    $initFile = rtrim(JPATH_ADMINISTRATOR, $ds).$ds.'components'.$ds.'com_acym'.$ds.'Core'.$ds.'init.php';
	    if (!include_once($initFile)) {
		    error_log('This code can not work without the AcyMailing Component');
		    return;
	    }

        if(!$emailId){
			Log::add('AcyMailingHelper::sendConfirmationMail - no emailId', Log::ERROR, 'com_marathonmanager.registration');
            error_log('AcyMailingHelper::sendConfirmationMail - no emailId');
            return;
        }

	    if(empty($data['contact_email']) || !is_string($data['contact_email'])){
			Log::add('AcyMailingHelper::sendConfirmationMail - no contact_email', Log::ERROR, 'com_marathonmanager.registration');
		    error_log('AcyMailingHelper::sendConfirmationMail - no contact_email');
		    return;
	    }

	    // Extract the first email address if multiple emails are provided (semicolon separated)
	    $emailAddress = trim($data['contact_email']);
	    if(str_contains($emailAddress, ';')) {
		    $emails = array_map('trim', explode(';', $emailAddress));
		    $emailAddress = !empty($emails[0]) ? $emails[0] : '';
	    }

	    // Validate email format
	    if(empty($emailAddress) || !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
			Log::add('AcyMailingHelper::sendConfirmationMail - invalid email format: ' . $emailAddress, Log::ERROR, 'com_marathonmanager.registration');
		    error_log('AcyMailingHelper::sendConfirmationMail - invalid email format: ' . $emailAddress);
		    return;
	    }

        $mailer = new \AcyMailing\Helpers\MailerHelper();
		// $mailer->setFrom('', 'AcyMailing'); // @ToDo: set from address and name
        $mailer->report = false; // set it to true or false if you want Acy to display a confirmation message or not (message successfully sent to...)
        $mailer->trackEmail = true; // set it to true or false if you want Acy to track the message or not (it will be inserted in the statistics table)
        $mailer->autoAddUser = false; // set it to true if you want Acy to automatically create the user if it does not exist in AcyMailing
//        $mailer->addParam('team_name', $data['team_name']); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
//        $mailer->addParam('reference', $data['reference']);
//        $mailer->addParam('registration_fee', $data['registration_fee']);
	    try
	    {
		    if (!$mailer->sendOne($emailId, $data['contact_email']))
		    {
			    Log::add('AcyMailingHelper::sendConfirmationMail - sending failed: ' . $mailer->reportMessage, Log::ERROR, 'com_marathonmanager.registration');
			    error_log("----------------------------------------------------------------------------------------");
			    error_log('AcyMailingHelper::sendConfirmationMail - sending failed');
			    error_log($mailer->reportMessage);
			    error_log("----------------------------------------------------------------------------------------");
		    }
	    }catch (\Exception $e){
			Log::add('AcyMailingHelper::sendConfirmationMail - sending failed: ' . $e->getMessage() . "\n" .
				"E-Mail ID: " . $emailId . "\n" .
				"Contact E-Mail: " . $data['contact_email'] . "\n" .
				"---------------------\n" .
				"Data: " . print_r($data, true),
				Log::ERROR,
				'com_marathonmanager.registration'
			);
	    }

    }

    private function getEventPaymentInformation($eventId)
    {

    }

    public static function registerForNewsletter()
    {

    }
}