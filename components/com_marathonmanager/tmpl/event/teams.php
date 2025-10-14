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

use Joomla\CMS\Language\Text;

?>
<section>
    <h2><?php echo Text::_("COM_MARATHONMANAGER_SITE_TEAMS_LABEL") ?></h2>
    <table id="event-registered-teams" class="uk-visible@m uk-table uk-table-striped">
        <thead class="uk-visible@m">
        <tr>
            <th><?php echo Text::_("COM_MARATHONMANAGER_SITE_TABLEHEAD_TEAM") ?></th>
            <th><?php echo Text::_("COM_MARATHONMANAGER_SITE_TABLEHEAD_PARTICIPANTS") ?></th>
            <th><?php echo Text::_("COM_MARATHONMANAGER_SITE_TABLEHEAD_PARCOURS") ?></th>
            <th><?php echo Text::_("COM_MARATHONMANAGER_SITE_TABLEHEAD_CATEGORY") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->item->teams as $team) : ?>
            <tr>
                <td>
                    <span class="uk-text-bold"><?php echo $team->team_name; ?></span>
                    <div class="uk-hidden@m">
                        <?php foreach ($team->participants as $p) : ?>
                            <div class="uk-text-small">
                                <?php echo $p['first_name'] . ' ' . $p['last_name']; ?>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <div class="uk-text-small"><?php echo $team->parcours_title; ?></div>
                        <div class="uk-text-small"><?php echo $team->category_title; ?></div>
                    </div>
                </td>
                <td class="uk-visible@m">
                    <?php foreach ($team->participants as $p) : ?>
                    <div class="uk-text-small">
                        <?php echo $p['first_name'] . ' ' . $p['last_name']; ?>
                    </div>
                    <?php endforeach; ?>
                </td>
                <td class="uk-visible@m"><?php echo $team->parcours_title; ?></td>
                <td class="uk-visible@m"><?php echo $team->category_title; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="uk-hidden@m" uk-margin>
        <?php foreach ($this->item->teams as $team) : ?>
        <div class="uk-card uk-card-default">
            <div class="uk-card-header uk-text-center uk-h4 uk-margin-remove-bottom">
                <?php echo $team->team_name; ?>
            </div>
            <div class="uk-card-body">
                <?php foreach ($team->participants as $p) : ?>
                <div class="uk-text-small">
                        <?php echo $p['first_name'] . ' ' . $p['last_name']; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="uk-card-footer">
                <div class="uk-grid uk-grid-small uk-flex uk-flex-middle">
                    <div class="uk-width-expand">
                        <span class="uk-text-small uk-text-left"><?php echo $team->parcours_title; ?></span>
                    </div>
                    <div class="uk-width-auto">
                        <span class="uk-text-small uk-text-right"><?php echo $team->category_title; ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>