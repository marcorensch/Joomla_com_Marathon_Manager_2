<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;
use NXD\Component\MarathonManager\Site\Helper\EventGalleryHelper;

\defined('_JEXEC') or die;
// Create the $event object for use in the template
$event = $this->item;

$eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');

$picturesTree = EventGalleryHelper::getPictures($this->item->gallery_content);

?>

<?php echo $eventHeader->render(compact('event')); ?>
<section>
    <h2>Gallery</h2>
    <div id="event-gallery">
        <?php echo $this->item->gallery_content; ?>
        <ul class="uk-subnav uk-subnav-pill" uk-switcher>
            <?php
            foreach ($picturesTree as $pictureFolder): ?>
                <li><a href="#"><?php echo $pictureFolder['name'];?></a></li>
            <?php endforeach; ?>
        </ul>
        <ul class="uk-switcher">
        <?php
        foreach ($picturesTree as $pictureFolder) :?>
        <li>
            <div class="uk-child-width-1-6 uk-grid-small" uk-grid>
            <?php
            foreach ($pictureFolder['images'] as $picture) :?>
            <div>
                <?php
                echo HTMLHelper::_('image', $picture, '', array('lazy' => 'true'));
                ?>
            </div>
            <?php endforeach; ?>
            </div>
        </li>
        <?php endforeach; ?>
        </ul>
    </div>
</section>


