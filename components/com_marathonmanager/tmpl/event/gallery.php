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
use Joomla\CMS\Layout\FileLayout;
use NXD\Component\MarathonManager\Site\Helper\EventGalleryHelper;

\defined('_JEXEC') or die;
// Create the $event object for use in the template
$event = $this->item;

$eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');

$picturesTree = EventGalleryHelper::getPictures($this->item->gallery_content);

$document = Factory::getApplication()->getDocument();
$wa = $document->getWebAssetManager();
$wa->registerAndUseStyle('com_marathonmanager.gallery-css');

?>

<?php echo $eventHeader->render(compact('event')); ?>
<section>
    <h2>Gallery</h2>
    <div id="event-gallery">
        <ul class="uk-subnav uk-subnav-pill uk-flex uk-flex-center" uk-switcher>
            <?php
            foreach ($picturesTree as $pictureFolder):
                if(empty($pictureFolder['images'])) continue;
                ?>
                <li><a href="#"><?php echo $pictureFolder['label'];?></a></li>
            <?php endforeach; ?>
        </ul>
        <ul class="uk-switcher">
        <?php
        foreach ($picturesTree as $pictureFolder) :
            if(empty($pictureFolder['images'])) continue;
            ?>
        <li>
            <div class="uk-child-width-1-6 uk-grid-small" uk-grid uk-lightbox>
            <?php
            $i = 0;
            foreach ($pictureFolder['images'] as $picture) {
                $i++;
                $picture_item = new stdClass();
                $picture_item->image = $picture;
                $picture_item->title = $pictureFolder['label'] . " - " . $i;
                $imageLayout = new FileLayout('event-gallery-item', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
                echo $imageLayout->render(compact('picture_item'));
            } ?>
            </div>
        </li>
        <?php endforeach; ?>
        </ul>
    </div>
</section>


