<?php


/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

$app = Factory::getApplication();
$params = $app->getParams();

// Check access to ressource
$user = Factory::getApplication()->getIdentity();

if($this->registration->created_by != $user->id){
    $app->enqueueMessage(Text::_("COM_MARATHONMANAGER_REGISTRATION_NOT_AUTHORIZED"), 'error');
    $app->redirect(Route::_('index.php?option=com_marathonmanager&view=events', false));
}

$tableHeaderCellClasses = 'uk-table-shrink uk-text-nowrap';


// Mail us
$subject = str_replace(' ', "%20", Text::sprintf("COM_MARATHONMANAGER_REGISTRATION_REQ_EMAIL_SUBJECT",$this->registration->team_name, $this->event->title ));
$contactUsMail = $params->get('registration_contact_email', null) ?? $params->get('contact_email', null);
$mailto = "mailto:" . $contactUsMail  . "?subject=" . $subject;

$event = $this->event;

// Payment Status Icon
$icon = $this->registration->payment_status ? 'fa-check-square' : 'fa-times-circle' ;
$color = $this->registration->payment_status ? 'green' : '#47070c' ;
$paymentMsgKey = $this->registration->payment_status ? Text::_('COM_MARATHONMANAGER_REGISTRATION_PAYMENT_STATUS_PAYED') : TEXT::_('COM_MARATHONMANAGER_REGISTRATION_PAYMENT_STATUS_NOT_PAYED');

$paymentLayout = new FileLayout('event-payment-layout', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');

?>
<section id="registration-details">
    <div class="uk-margin-bottom">
        <h2><?php echo Text::_("COM_MARATHONMANAGER_YOUR_REGISTRATION_HEADER");?></h2>
        <span class="uk-text-medium"><?php echo Text::sprintf("COM_MARATHONMANAGER_REGISTERED_AT", HTMLHelper::date($this->registration->created));?> </span>
    </div>
    <div class="uk-grid uk-child-width-1-2@m uk-grid-match" uk-grid>
        <div>
            <div>
                <table class="uk-table uk-table-divider">
                    <tbody>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_TEAM_NAME_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->team_name; ?></td>
                    </tr>

                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_ARRIVAL_DATE_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo HTMLHelper::date($this->registration->arrival_date, 'DATE_FORMAT_LC5'); ?></td>
                    </tr>

                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_PARCOURS_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->course->title; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_CATEGORY_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->group->title; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_CONTACT_PHONE_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->contact_phone; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_CONTACT_EMAIL_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->contact_email; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_PARTICIPANTS_LABEL"); ?></th>
                        <td class="uk-width-expand">
                            <?php foreach ($this->registration->participants as $runner): ?>
                                <?php echo $runner->first_name . ' ' . $runner->last_name . '<br>'; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_FEE_PAYED_LABEL"); ?></th>
                        <td class="uk-width-expand">
                            <i class="fas <?php echo $icon;?>" uk-tooltip="<?php echo $paymentMsgKey;?>" title="<?php echo $paymentMsgKey; ?>" style="color:<?php echo $color;?>"></i>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-secondary">
                <div class="uk-card-header">
                    <h3 class="uk-card-title"><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_HEADER");?></h3>
                </div>
                <div class="uk-card-body">
                    <p><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_TEXT_GENERAL");?></p>
                    <p><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_TEXT_FEE");?></p>
                    <p><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_TEXT_CONTACT");?></p>
                </div>
                <div class="uk-card-footer">
                    <?php
                    if($contactUsMail):
                    ?>
                    <a href="mailto:<?php echo $contactUsMail ?>?subject=<?php echo $subject;?>" class="uk-button uk-button-primary uk-width-1-1"><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_BUTTON");?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</section>

<section id="payment-block" class="uk-margin-top" uk-margin>
    <?php

    $registration = $this->registration;
    $config = ['container_class' => 'uk-card uk-card-default'];
    echo $paymentLayout->render(compact('registration', 'config'));
    ?>

</section>
