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

?>
<section>
    <h2><?php echo Text::_("COM_MARATHONMANAGER_SITE_TEAMS_LABEL") ?></h2>
    <table id="event-registered-teams" class="uk-table uk-table-striped">
        <thead class="uk-visible@m">
        <tr>
            <th><?php echo Text::_("COM_MARATHONMANAGER_SITE_TABLEHEAD_TEAM") ?></th>
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
                        <div class="uk-text-small"><?php echo $team->parcours_title; ?></div>
                        <div class="uk-text-small"><?php echo $team->category_title; ?></div>
                    </div>
                </td>
                <td class="uk-visible@m"><?php echo $team->parcours_title; ?></td>
                <td class="uk-visible@m"><?php echo $team->category_title; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>