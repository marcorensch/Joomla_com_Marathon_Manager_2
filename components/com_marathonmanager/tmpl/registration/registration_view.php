<?php


/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

$app = Factory::getApplication();
$params = $app->getParams();
$tableHeaderCellClasses = 'uk-table-shrink uk-text-nowrap';

// Mail us
$subject = str_replace(' ', "%20", Text::sprintf("COM_MARATHONMANAGER_REGISTRATION_REQ_EMAIL_SUBJECT",$this->registration->team_name, $this->event->title ));
$contactUsMail = $params->get('registration_contact_email', null) ?? $params->get('contact_email', null);
$mailto = "mailto:" . $contactUsMail  . "?subject=" . $subject;

$event = $this->event;

$eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
echo $eventHeader->render(compact('event'));

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
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_TEAM_NAME_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->team_name; ?></td>
                    </tr>

                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_ARRIVAL_DATE_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo HTMLHelper::date($this->registration->arrival_date, 'DATE_FORMAT_LC5'); ?></td>
                    </tr>

                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_PARCOURS_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->course->title; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_CATEGORY_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->group->title; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_CONTACT_PHONE_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->contact_phone; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_CONTACT_EMAIL_LABEL"); ?></th>
                        <td class="uk-width-expand"><?php echo $this->registration->contact_email; ?></td>
                    </tr>
                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_PARTICIPANTS_LABEL"); ?></th>
                        <td class="uk-width-expand">
                            <?php foreach ($this->registration->participants as $runner): ?>
                                <?php echo $runner->first_name . ' ' . $runner->last_name . '<br>'; ?>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <tr>
                        <th class="<?php echo $$tableHeaderCellClasses; ?>"><?php echo Text::_("COM_MARATHONMANAGER_FEE_PAYED_LABEL"); ?></th>
                        <td class="uk-width-expand">
                            <?php
                            $icon = $this->registration->payment_status ? 'fa-check-square' : 'fa-times-circle' ;
                            $color = $this->registration->payment_status ? 'green' : '#47070c' ;
                            ?>

                            <i class="fas <?php echo $icon;?>" style="color:<?php echo $color;?>"></i>

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
    <div class="uk-card uk-card-default">

        <div class="uk-grid uk-flex-middle">
            <div class="uk-width-1-2@s uk-width-1-4@m">
                <div>
                    <?php echo HTMLHelper::image($this->registration->paymentInformation->qr_bank, 'QR Code Bank', array('class' => 'uk-width-1-1 uk-padding-small')); ?>
                </div>
            </div>
            <div class="uk-width-expand">
                <div class="uk-card-body">
                    <h3 class="uk-card-title"><?php echo Text::_("COM_MARATHONMANAGER_BANK_PAYMENT_INFO_TITLE"); ?></h3>
                    <table class="uk-table uk-table-small uk-table-divider">
                        <?php if (!empty($this->registration->paymentInformation->bankingInformation->recipient)) : ?>
                            <tr>
                                <th class="<?php echo $tableHeaderCellClasses; ?>">
                                    <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_RECIPIENT_LABEL"); ?>
                                </th>
                                <td class="uk-width-expand">
                                <span>
                                    <?php echo $this->registration->paymentInformation->bankingInformation->recipient ?? ""; ?>
                                </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($this->registration->paymentInformation->bankingInformation->bank_name)) : ?>
                            <tr>
                                <th class="<?php echo $tableHeaderCellClasses; ?>">
                                    <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_BANK_NAME_LABEL"); ?>
                                </th>
                                <td>
                                <span>
                                    <?php echo $this->registration->paymentInformation->bankingInformation->bank_name ?? ""; ?>
                                </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($this->registration->paymentInformation->bankingInformation->account_number)) : ?>
                            <tr>
                                <th class="<?php echo $tableHeaderCellClasses; ?>">
                                    <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_ACC_NUM_LABEL"); ?>
                                </th>
                                <td>
                                <span>
                                    <?php echo $this->registration->paymentInformation->bankingInformation->account_number ?? ""; ?>
                                </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($this->registration->paymentInformation->bankingInformation->iban)) : ?>
                            <tr>
                                <th class="<?php echo $tableHeaderCellClasses; ?>">
                                    <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_IBAN_LABEL"); ?>
                                </th>
                                <td>
                                <span>
                                    <?php echo $this->registration->paymentInformation->bankingInformation->iban ?? ""; ?>
                                </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php if (!empty($this->registration->paymentInformation->bankingInformation->bic)) : ?>
                            <tr>
                                <th class="<?php echo $tableHeaderCellClasses; ?>">
                                    <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_BIC_LABEL"); ?>
                                </th>
                                <td>
                                <span>
                                    <?php echo $this->registration->paymentInformation->bankingInformation->bic ?? ""; ?>
                                </span>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_REFERENCE_LABEL"); ?>
                            </th>
                            <td>
                                <span>
                                    <?php echo $this->registration->reference ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FEE_LABEL"); ?>
                            </th>
                            <td>
                                <span class="uk-text-bold">
                                    <?php echo Text::sprintf("COM_MARATHONMANAGER_REGISTRATION_FEE_PRICE", number_format((float)$this->registration->registration_fee, 2, '.')) ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <div class="uk-padding">
        <div class="uk-grid-small uk-child-width-expand uk-flex-middle" uk-grid>
            <div>
                <hr>
            </div>
            <div class="uk-width-auto">
                <div class="uk-heading-medium uk-text-uppercase uk-padding uk-padding-remove-vertical uk-margin-remove">
                    <?php echo Text::_("COM_MARATHONMANAGER_OR"); ?>
                </div>
            </div>
            <div>
                <hr>
            </div>
        </div>
    </div>
    <div class="uk-card uk-card-default">
        <div class="uk-grid uk-flex-middle">
            <div class="uk-width-1-2@s uk-width-1-4@m">
                <div>
                    <?php echo HTMLHelper::image($this->registration->paymentInformation->qr_twint, 'QR Code Twint', array('class' => 'uk-width-1-1 uk-padding-small')); ?>
                </div>
            </div>
            <div class="uk-width-expand">
                <div class="uk-card-body">
                    <h3 class="uk-card-title"><?php echo Text::_("COM_MARATHONMANAGER_TWINT_PAYMENT_INFO_TITLE"); ?></h3>
                    <div><?php echo Text::_('COM_MARATHONMANAGER_TWINT_PAYMENT_DESCRIPTION'); ?></div>
                    <table class="uk-table uk-table-small uk-table-divider">
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_REFERENCE_MSG_LABEL"); ?>
                            </th>
                            <td>
                                <span>
                                    <?php echo $this->registration->reference ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FEE_LABEL"); ?>
                            </th>
                            <td>
                                <span class="uk-text-bold">
                                    <?php echo Text::sprintf("COM_MARATHONMANAGER_REGISTRATION_FEE_PRICE", number_format((float)$this->registration->registration_fee, 2, '.')) ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

</section>
