<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

$registration = $displayData['registration'];
$config = $displayData['config'];

$tableHeaderCellClasses = 'uk-table-shrink uk-text-nowrap';

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle('
    .uk-table th {
        vertical-align: top;
    }
    ');

?>

<div class="<?php echo array_key_exists('container_class', $config) ? $config['container_class'] : ''?>">
    <div class="uk-grid uk-flex-middle">
        <?php if($registration->paymentInformation->qr_bank):?>
        <div class="uk-width-1-2@s uk-width-1-4@m">
            <div>
                <?php echo HTMLHelper::image($registration->paymentInformation->qr_bank, 'QR Code Bank', array('class' => 'uk-width-1-1 uk-padding-small')); ?>
            </div>
        </div>
        <?php endif;?>
        <div class="uk-width-expand">
            <div class="uk-card-body">
                <h3 class="uk-card-title"><?php echo Text::_("COM_MARATHONMANAGER_BANK_PAYMENT_INFO_TITLE"); ?></h3>
                <table class="uk-table uk-table-small uk-table-divider">
                    <?php if (!empty($registration->paymentInformation->bankingInformation->recipient)) : ?>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_RECIPIENT_LABEL"); ?>
                            </th>
                            <td class="uk-width-expand">
                                <p>
                                    <?php echo nl2br($registration->paymentInformation->bankingInformation->recipient) ?? ""; ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($registration->paymentInformation->bankingInformation->bank_name)) : ?>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_BANK_NAME_LABEL"); ?>
                            </th>
                            <td>
                                <span>
                                    <?php echo $registration->paymentInformation->bankingInformation->bank_name ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($registration->paymentInformation->bankingInformation->account_number)) : ?>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_ACC_NUM_LABEL"); ?>
                            </th>
                            <td>
                                <span>
                                    <?php echo $registration->paymentInformation->bankingInformation->account_number ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($registration->paymentInformation->bankingInformation->iban)) : ?>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_IBAN_LABEL"); ?>
                            </th>
                            <td>
                                <span>
                                    <?php echo $registration->paymentInformation->bankingInformation->iban ?? ""; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($registration->paymentInformation->bankingInformation->bic)) : ?>
                        <tr>
                            <th class="<?php echo $tableHeaderCellClasses; ?>">
                                <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_BANKING_BIC_LABEL"); ?>
                            </th>
                            <td>
                                <span>
                                    <?php echo $registration->paymentInformation->bankingInformation->bic ?? ""; ?>
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
                                    <?php echo $registration->reference ?? ""; ?>
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>">
                            <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FEE_LABEL"); ?>
                        </th>
                        <td>
                                <span class="uk-text-bold">
                                    <?php echo Text::sprintf("COM_MARATHONMANAGER_REGISTRATION_FEE_PRICE", number_format((float)$registration->registration_fee, 2, '.')) ?? ""; ?>
                                </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

    </div>
</div>
<?php if($registration->paymentInformation->qr_twint):?>
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
<div class="<?php echo array_key_exists('container_class', $config) ? $config['container_class'] : ''?>">
    <div class="uk-grid uk-flex-middle">
        <div class="uk-width-1-2@s uk-width-1-4@m">
            <div>
                <?php echo HTMLHelper::image($registration->paymentInformation->qr_twint, 'QR Code Twint', array('class' => 'uk-width-1-1 uk-padding-small')); ?>
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
                                    <?php echo $registration->reference ?? ""; ?>
                                </span>
                        </td>
                    </tr>
                    <tr>
                        <th class="<?php echo $tableHeaderCellClasses; ?>">
                            <?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_FEE_LABEL"); ?>
                        </th>
                        <td>
                                <span class="uk-text-bold">
                                    <?php echo Text::sprintf("COM_MARATHONMANAGER_REGISTRATION_FEE_PRICE", number_format((float)$registration->registration_fee, 2, '.')) ?? ""; ?>
                                </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif;?>

