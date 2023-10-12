<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @var         $event object the current event object
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

?>

<section class="uk-section marathonmanager-event-description-text-section">
    <div class="uk-container marathonmanager-event-description-text-container">
        <div class="uk-margin marathonmanager-event-description-text">
            <?php echo $event->gallery_content; ?>
        </div>
    </div>
</section>
