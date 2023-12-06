<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Date\Date;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;

$item = $displayData['item'];

if ($item->event_duration > 1):
    $languageConstant = ($item->event_duration > 2) ? "COM_MARATHONMANAGER_EVENT_DATE_FROM_TO_MULTIPLE" : "COM_MARATHONMANAGER_EVENT_DATE_FROM_TO";
    $startDate = new Date($item->event_date);
    $endDate = new Date($startDate . '+' . $item->event_duration - 1 . ' day');
    $dateString = Text::sprintf($languageConstant, HTMLHelper::date($startDate, 'd.'), HTMLHelper::date($endDate, 'DATE_FORMAT_LC3'));
    ?>
    <span class="nxd-event-date nxd-multiple-days-event"><?php echo $dateString ?></span>
<?php else: ?>
    <span class="nxd-event-date"><?php
        echo Text::sprintf("COM_MARATHONMANAGER_EVENT_DATE_AT", HTMLHelper::date($item->event_date, 'DATE_FORMAT_LC3'));
        ?>
                </span>
<?php endif; ?>
