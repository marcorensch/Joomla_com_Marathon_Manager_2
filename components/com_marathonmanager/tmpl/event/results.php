<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\Layout\FileLayout;

\defined('_JEXEC') or die;
// Create the $event object for use in the template
$event = $this->item;

$eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
$fileRootPath = '/media/com_marathonmanager/results/';

?>

<?php echo $eventHeader->render(compact('event')); ?>

<section>
    <h2>Results</h2>
    <div id="event-results">
        <hr>
        <div class="uk-position-relative uk-flex-right@m uk-grid-small uk-child-width-1-1 uk-child-width-auto@m" uk-grid style="z-index: 2; gap: 10px">
            <?php foreach ($event->result_files as $file):
                $fileExtension = pathinfo(JPATH_BASE . $fileRootPath . $file['file'], PATHINFO_EXTENSION);
                switch (strtolower($fileExtension)){
                    case 'pdf':
                        $fileIcon = 'pdf';
                        break;
                    case 'doc':
                    case 'docx':
                        $fileIcon = 'word';
                        break;
                    case 'xls':
                    case 'xlsx':
                        $fileIcon = 'excel';
                        break;
                    case 'ppt':
                    case 'pptx':
                        $fileIcon = 'powerpoint';
                        break;
                    case 'zip':
                    case 'rar':
                        $fileIcon = 'archive';
                        break;
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'webp':
                        $fileIcon = 'image';
                        break;
                    default:
                        $fileIcon = 'alt';
                }
                ?>
                <a target="_blank" href="<?php echo$fileRootPath . $file['file'];?>" class="uk-button uk-button-primary uk-button-large">
                    <i class="fas fa-file-<?php echo $fileIcon;?>"></i>&nbsp;
                    <?php echo $file['label'];?>
                </a>
            <?php endforeach; ?>
        </div>
        <hr>
    </div>
</section>


