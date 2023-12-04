<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;
$result = $displayData['result'];
?>


<div>
    <div class="uk-grid-small" uk-grid uk-toggle="<?php echo "#modal-{$result->id}"; ?>">
        <div class="uk-width-expand"><span class="nxd-team-name"><?php echo $result->team_name; ?></span></div>
        <div class="uk-width-auto"><i class="fas fa-plus uk-text-muted"></i></div>
    </div>

    <div id="<?php echo "modal-{$result->id}"; ?>" class="uk-flex-top" uk-modal>
        <div class="uk-modal-dialog uk-margin-auto-vertical">
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <div class="uk-modal-header">
                <?php echo $result->team_name; ?>
            </div>
            <div class="uk-modal-body">
                <?php if ($result->participants): ?>
                    <?php foreach ($result->participants as $participant): ?>
                        <div>
                            <i class="fas fa-walking"></i>
                            <span class="nxd-participant-firstname">
                                <?php echo $participant['first_name']; ?>
                            </span>
                            <span class="nxd-participant-lastname">
                                <?php echo $participant['last_name']; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div class="uk-margin-small-top">
                    <ul class="uk-list uk-list-divider uk-list-small">
                        <li>
                            <?php if ($result->place): ?>

                                <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                                    <div class="">
                                        <span class="nxd-result-detail-title"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_PLACE"); ?></span>
                                    </div>
                                    <div class="">
                                        <span class="nxd-result-detail-value"><?php echo $result->place; ?></span>
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="uk-width-1-1 uk-text-center uk-padding-small uk-padding-remove-horizontal uk-tile-muted uk-margin-bottom">
                                <span class="nxd-result-detail-value nxd-not-classified">
                                    <?php echo Text::sprintf('COM_MARATHONMANAGER_NOT_CLASSIFIED', strtoupper($result->place_msg)); ?>
                                </span>
                                </div>

                            <?php endif; ?>
                        </li>
                        <li>
                            <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                                <div class="">
                                    <span class="nxd-result-detail-title"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_POINTS_TOTAL"); ?></span>
                                </div>
                                <div class="">
                                    <span class="nxd-result-detail-value"><?php echo $result->points_total; ?></span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                                <div class="">
                                    <span class="nxd-result-detail-title"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_TIME_TOTAL"); ?></span>
                                </div>
                                <div class="">
                                    <span class="nxd-result-detail-value"><?php echo $result->time_total; ?></span>
                                </div>
                            </div>
                        </li>
                        <?php if ($result->parcours_title): ?>
                            <li>
                                <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                                    <div class="">
                                        <span class="nxd-result-detail-title"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_PARCOURS"); ?></span>
                                    </div>
                                    <div class="">
                                        <span class="nxd-result-detail-value"><?php echo $result->parcours_title; ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php if ($result->category_title): ?>
                            <li>
                                <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                                    <div class="">
                                        <span class="nxd-result-detail-title"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_CATEGORY"); ?></span>
                                    </div>
                                    <div class="">
                                        <span class="nxd-result-detail-value"><?php echo $result->category_title; ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if ($result->place_in_group): ?>
                            <li>
                                <div class="uk-grid-small uk-child-width-1-2" uk-grid>
                                    <div class="">
                                        <span class="nxd-result-detail-title"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_PLACE_IN_CATEGORY"); ?></span>
                                    </div>
                                    <div class="">
                                        <span class="nxd-result-detail-value"><?php echo $result->place_in_group; ?></span>
                                    </div>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>