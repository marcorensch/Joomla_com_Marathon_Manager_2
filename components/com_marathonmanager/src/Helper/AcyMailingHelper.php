<?php

namespace NXD\Component\MarathonManager\Site\Helper;

use AcyMailing\Helpers\MailerHelper;


class AcyMailingHelper
{
    public static function sendConfirmationMail($data, $emailId): void
    {
        error_log('AcyMailingHelper::sendConfirmationMail');
        error_log(print_r($data, true));

        if(!$emailId){
            error_log('AcyMailingHelper::sendConfirmationMail - no emailId');
            return;
        }

        if(!$data['contact_email']){
            error_log('AcyMailingHelper::sendConfirmationMail - no contact_email');
            return;
        }

        $mailer = new MailerHelper();
        $mailer->report = false; // set it to true or false if you want Acy to display a confirmation message or not (message successfully sent to...)
        $mailer->trackEmail = true; // set it to true or false if you want Acy to track the message or not (it will be inserted in the statistics table)
        $mailer->autoAddUser = false; // set it to true if you want Acy to automatically create the user if it does not exist in AcyMailing
        $mailer->addParam('team_name', $data['team_name']); // Acy will automatically replace the tag {var1} by the value specified in the second parameter... you can use this function several times to replace tags in your email

        if(!$mailer->sendOne($emailId, $data['contact_email'])){
            error_log('AcyMailingHelper::sendConfirmationMail - sending failed');
        }

    }

    public static function registerForNewsletter()
    {

    }
}