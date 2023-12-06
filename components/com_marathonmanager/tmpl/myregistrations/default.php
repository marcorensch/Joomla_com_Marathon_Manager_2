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
use Joomla\CMS\Router\Route;

\defined('_JEXEC') or die;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_marathonmanager.my-registrations-css');
$wa->useScript('com_marathonmanager.my-registrations-js');

$dateLayout = new FileLayout('event-date-layout', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
$paymentLayout = new FileLayout('event-payment-layout', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');


echo '<pre>' . var_export($this->items, true) . '</pre>';
?>
<div class="uk-container" uk-margin uk-grid uk-scrollspy="target:>div; cls: uk-animation-slide-bottom; delay: 50;">
    <?php
    foreach ($this->items as $item):
        ?>
        <div class="uk-width-1-1">
            <div class="uk-card uk-card-default registration-card">
                <div class="uk-card-header nxd-registration-header">
                    <div class="uk-grid-small uk-flex uk-flex-middle">
                        <div class="uk-width-auto">
                            <img class="uk-border-circle nxd-event-registration-image" src="https://picsum.photos/60/60"
                                 alt="">
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
                        <div class="uk-child-width-1-1 uk-child-width-1-2@m" uk-grid>
                            <div class="uk-flex-last@m">
                                <h4 class="">Related Articles</h4>
                            </div>
                            <div>
                                <h4 class="">Registration Details</h4>
                                <table class="uk-table uk-table-striped uk-table-small nxd-table-top">
                                    <tbody>
                                    <tr>
                                        <th>Team Name</th>
                                        <td><?php echo $item->team_name; ?></td>
                                    </tr>
                                    <tr>
                                        <th>Registration Date</th>
                                        <td><?php echo Text::sprintf("COM_MARATHONMANAGER_EVENT_DATE_AT", HTMLHelper::date($item->created, 'DATE_FORMAT_LC2')); ?></td>
                                    </tr>
                                    <tr>
                                        <th>Payment Status</th>
                                        <td>
                                            <?php if ($item->payment_status): ?>
                                                <span class="uk-text-success"><?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_REGISTERED"); ?></span>
                                            <?php else: ?>
                                                <span class="uk-text-warning"><?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_NOT_REGISTERED"); ?></span>
                                                <a href="#" uk-toggle="target: #pay-<?php echo $item->id;?>" class="uk-button uk-button-small uk-button-primary uk-margin-small-left">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </a>
                                                <div id="pay-<?php echo $item->id;?>" class="uk-flex-top uk-modal-container" uk-modal>
                                                    <div class="uk-modal-dialog uk-margin-auto-vertical uk-modal-body">
                                                        <h2 class="uk-modal-title"><?php echo Text::_("COM_MARATHONMANAGER_INFO_PAYMENT_INFORMATION"); ?></h2>
                                                                <?php
                                                                $config = [];
                                                                $registration = $item;
                                                                echo $paymentLayout->render(compact('registration', 'config'));
                                                                ?>
                                                        <button class="uk-modal-close-default" type="button" uk-close></button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Parcours</th>
                                        <td><?php echo $item->course->title ?></td>
                                    </tr>
                                    <tr>
                                        <th>Category</th>
                                        <td><?php echo $item->group->title ?></td>
                                    </tr>
                                    <tr>
                                        <th>Arrival by</th>
                                        <td><?php echo $item->arrival_option_title ?></td>
                                    </tr>
                                    <?php if($item->arrival_date):?>
                                    <tr>
                                        <th>Arrival Date</th>
                                        <td><?php echo HTMLHelper::date($item->arrival_date, 'DATE_FORMAT_LC3') ?></td>
                                    </tr>
                                    <?php endif;?>
                                    <tr>
                                        <th>Runner</th>
                                        <td>
                                            <?php foreach ($item->participants as $runner): ?>
                                                <?php echo $runner->first_name . ' ' . $runner->last_name . '<br>'; ?>
                                            <?php endforeach; ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
