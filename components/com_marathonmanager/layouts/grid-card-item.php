<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;
$event = $displayData['event'];
$params = $displayData['params'];
?>

<div>
    <div class="uk-card uk-card-default">
        <?php echo $event->title; ?>
    </div>
</div>

