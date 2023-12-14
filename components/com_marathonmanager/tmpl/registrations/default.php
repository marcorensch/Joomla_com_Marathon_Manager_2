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
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;

\defined('_JEXEC') or die;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_marathonmanager.my-registrations-css');
$wa->useScript('com_marathonmanager.my-registrations-js');

$dateLayout = new FileLayout('event-date-layout', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
$paymentLayout = new FileLayout('event-payment-layout', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
$headerLayout = new FileLayout('marathon-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
echo $headerLayout->render();

?>
<div class="uk-section">
    <div class="uk-container">
        <div uk-margin uk-scrollspy="target:>div; cls: uk-animation-slide-bottom; delay: 50;">
            <?php
            foreach ($this->items as $item):
                ?>
                <div class="uk-width-1-1">
                    <div class="uk-card uk-card-default uk-card-hover registration-card">
                        <div class="uk-card-header nxd-registration-header">
                            <div class="uk-grid-small uk-flex uk-flex-middle">
                                <div class="uk-width-auto">
                                    <div class="uk-border-circle uk-cover-container" style="width:60px; height:60px;">
                                        <?php if ($item->event_image): ?>
                                            <?php echo LayoutHelper::render('joomla.html.image', ['src' => $item->event_image, 'alt' => $item->event_title, 'uk-cover' => 'true']); ?>
                                        <?php else: ?>
                                            <div class="uk-background-muted">
                                            <span class="uk-position-center" uk-icon="icon: image; ratio: 2"
                                                  style="opacity: .1"></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="uk-width-expand">
                                    <h3 class="uk-margin-remove-bottom"><?php echo $item->event_title; ?></h3>
                                    <div class="uk-text-meta"><?php echo $dateLayout->render(compact('item')); ?></div>
                                </div>
                                <div class="uk-width-auto">
                                    <?php if ($item->payment_status): ?>
                                        <span uk-icon="icon: check; ratio:2" class="uk-text-success"
                                              uk-tooltip="<?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_REGISTERED"); ?>"></span>
                                    <?php else: ?>
                                        <span class="uk-text-warning pulsing" uk-icon="icon: clock; ratio:2"
                                              uk-tooltip="<?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_NOT_REGISTERED"); ?>"></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="nxd-registration-details uk-overflow-hidden">
                            <div class="uk-card-body ">
                                <div class="uk-child-width-1-1 uk-child-width-1-2@m uk-flex uk-flex-bottom" uk-grid>
                                    <div>
                                        <h4 class=""><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_DETAILS_TITLE");?></h4>
                                        <table class="uk-table uk-table-striped uk-table-small nxd-table-top">
                                            <tbody>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_TEAM_NAME");?></th>
                                                <td><?php echo $item->team_name; ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_REG_DATE");?></th>
                                                <td><?php echo Text::sprintf("COM_MARATHONMANAGER_EVENT_DATE_AT", HTMLHelper::date($item->created, 'DATE_FORMAT_LC2')); ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_PAYMENT_STATUS");?></th>
                                                <td>
                                                    <?php if ($item->payment_status): ?>
                                                        <span class="uk-text-success"><?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_REGISTERED"); ?></span>
                                                    <?php else: ?>
                                                        <span class="uk-text-warning"><?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_NOT_REGISTERED"); ?></span>
                                                        <a href="#" uk-toggle="target: #pay-<?php echo $item->id; ?>"
                                                           class="uk-button uk-button-small uk-button-primary uk-margin-small-left">
                                                            <i class="fas fa-file-invoice-dollar"></i>
                                                        </a>
                                                        <div id="pay-<?php echo $item->id; ?>"
                                                             class="uk-flex-top uk-modal-container" uk-modal>
                                                            <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
                                                                <h2 class="uk-modal-title"><?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_INFORMATION"); ?></h2>
                                                                <?php
                                                                $config = [];
                                                                $registration = $item;
                                                                echo $paymentLayout->render(compact('registration', 'config'));
                                                                ?>
                                                                <button class="uk-modal-close-default" type="button"
                                                                        uk-close></button>
                                                            </div>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_PARCOURS");?></th>
                                                <td><?php echo $item->course->title ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_CATEGORY");?></th>
                                                <td><?php echo $item->group->title ?></td>
                                            </tr>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_ARRIVAL_OPTION");?></th>
                                                <td><?php echo $item->arrival_option_title ?></td>
                                            </tr>
                                            <?php if ($item->arrival_date): ?>
                                                <tr>
                                                    <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_ARRIVAL_DATE");?></th>
                                                    <td><?php echo HTMLHelper::date($item->arrival_date, 'DATE_FORMAT_LC3') ?></td>
                                                </tr>
                                            <?php endif; ?>
                                            <tr>
                                                <th><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_PARTICIPANTS");?></th>
                                                <td>
                                                    <?php foreach ($item->participants as $runner): ?>
                                                        <?php echo $runner->first_name . ' ' . $runner->last_name . '<br>'; ?>
                                                    <?php endforeach; ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div>
                                        <div class="uk-card uk-card-secondary">
                                            <div class="uk-card-body">
                                                <h4>Information</h4>
                                                <p><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_TEXT_GENERAL");?></p>
                                                <p><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_TEXT_FEE");?></p>
                                                <div class="contact-buttons">
                                                    <?php
                                                    if($registration_contact = Factory::getApplication()->getParams()->get('registration_contact_email')){
                                                        $contactUsMail = $registration_contact;
                                                    }else{
                                                        $contactUsMail = Factory::getApplication()->getParams()->get('contact_email');
                                                    }
                                                    if($contactUsMail){
                                                        $subject = Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_BUTTON_SUBJECT");
                                                    }
                                                    ?>
                                                    <?php if($contactUsMail): ?>
                                                        <a href="mailto:<?php echo $contactUsMail ?>?subject=<?php echo $subject;?>" class="uk-button uk-button-primary uk-width-1-1"><?php echo Text::_("COM_MARATHONMANAGER_REGISTRATION_INFO_CARD_BUTTON");?></a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
