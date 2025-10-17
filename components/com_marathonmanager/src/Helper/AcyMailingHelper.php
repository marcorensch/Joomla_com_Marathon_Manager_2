<?php

namespace NXD\Component\MarathonManager\Site\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use AcyMailing\Helpers\MailerHelper;

class AcyMailingHelper
{
    public static function sendConfirmationMail($data, $emailId): void
    {
        if(!$emailId){
            error_log('AcyMailingHelper::sendConfirmationMail - no emailId');
            return;
        }

        if(!$data['contact_email']){
            error_log('AcyMailingHelper::sendConfirmationMail - no contact_email');
            return;
        }

        $mailer = new MailerHelper();
		// $mailer->setFrom('', 'AcyMailing'); // @ToDo: set from address and name
        $mailer->report = false; // set it to true or false if you want Acy to display a confirmation message or not (message successfully sent to...)
        $mailer->trackEmail = true; // set it to true or false if you want Acy to track the message or not (it will be inserted in the statistics table)
        $mailer->autoAddUser = false; // set it to true if you want Acy to automatically create the user if it does not exist in AcyMailing
//        $mailer->addParam('team_name', $data['team_name']); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email
//        $mailer->addParam('reference', $data['reference']);
//        $mailer->addParam('registration_fee', $data['registration_fee']);
        if(!$mailer->sendOne($emailId, $data['contact_email'])){
            error_log('AcyMailingHelper::sendConfirmationMail - sending failed');
        }

    }

    private function getEventPaymentInformation($eventId)
    {

    }

    public static function registerForNewsletter()
    {

    }
}