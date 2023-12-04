<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

\defined('_JEXEC') or die;
// Create the $event object for use in the template
$event = $this->item;
$parcours = $this->parcours;
$categories = $this->categories;

$eventHeader = new FileLayout('event-header', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
$teamMobileLayout = new FileLayout('results-table-item-mobile', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');
$teamLayout = new FileLayout('results-table-item', $basePath = JPATH_ROOT . '/components/com_marathonmanager/layouts');

$fileRootPath = '/media/com_marathonmanager/results/';


$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useStyle('com_marathonmanager.results-table');
$wa->useScript('com_marathonmanager.results-filter');

function getFileIcon($file, $fileRootPath): string
{
    $fileExtension = pathinfo(JPATH_BASE . $fileRootPath . $file['file'], PATHINFO_EXTENSION);

    switch (strtolower($fileExtension)) {
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
    return $fileIcon;
}

?>

<?php echo $eventHeader->render(compact('event')); ?>

<section>
    <h2>Results</h2>
    <div id="event-results">
        <hr>
        <div class="uk-padding-small">
            <div class="uk-position-relative uk-grid-small uk-flex uk-flex-middle uk-child-width-1-1 uk-child-width-auto@m"
                 uk-grid>
                <div class="uk-width-medium@m">
                        <span class="uk-text-lead uk-text-muted">
                            <i class="fas fa-download"></i> Downloads
                        </span>
                </div>
                <?php foreach ($event->result_files as $file):
                    $fileIcon = getFileIcon($file, $fileRootPath);
                    ?>
                    <div>
                        <a target="_blank" href="<?php echo $fileRootPath . $file['file']; ?>"
                           class="uk-button uk-button-secondary uk-width-expand">
                            <i class="fas fa-file-<?php echo $fileIcon; ?>"></i>&nbsp;
                            <?php echo $file['label']; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <hr>

        <div id="results-table-container" class="uk-margin uk-position-relative" nxd-filter="target: #results-table-body">
            <div class="uk-tile-muted uk-padding-small">
                <div class="uk-child-width-1-1 uk-child-width-expand@m uk-grid-small uk-flex uk-flex-middle" uk-grid>
                    <div class="uk-width-medium@m">
                        <span class="uk-text-lead uk-text-muted">
                            <i class="fas fa-filter"></i> Filter
                        </span>
                    </div>
                    <div class="uk-width-1-3@s uk-width-expand@m">
                        <input name="filter-team" id="filter-team" class="uk-input" placeholder="<?php echo Text::_("COM_MARATHONMANAGER_LABEL_SEARCH_TEAM");?>" />
                    </div>
                    <div class="uk-width-1-3@s uk-width-expand@m">
                        <select name="filter-parcours" id="filter-parcours" class="uk-select">
                            <option value="">Filter by Parcours</option>
                            <?php foreach ($this->parcours as $parcours) {
                                echo "<option value=\"{$parcours->id}\">{$parcours->title}</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="uk-width-1-3@s uk-width-expand@m">
                        <select name="filter-category" id="filter-category" class="uk-select">
                            <option value="">Filter by Category</option>
                            <?php foreach ($this->categories as $cat) {
                                echo "<option value=\"{$cat->id}\">{$cat->title}</option>";
                            } ?>
                        </select>
                    </div>
                    <div class="uk-width-1-1 uk-width-auto@m">
                        <button class="uk-width-1-1 uk-width-auto@m uk-button uk-button-default uk-disabled"
                                type="button" id="reset-filter-btn">
                            <i class="fas fa-times"></i> Reset
                        </button>
                    </div>
                </div>

            </div>
            <table id="results-table" class="uk-table uk-table-striped uk-table-hover uk-overflow-hidden"
                   uk-scrollspy="target: tr; delay:80; cls: uk-animation-fade">
                <thead>
                <tr>
                    <th class="uk-text-center"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_PLACE"); ?></th>
                    <th><?php echo Text::_("COM_MARATHONMANAGER_LABEL_TEAM"); ?></th>
                    <th class="uk-visible@m"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_PARCOURS"); ?></th>
                    <th class="uk-visible@m"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_CATEGORY"); ?></th>
                    <th class="uk-visible@m uk-text-center"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_PLACE"); ?></th>
                    <th class="uk-visible@m"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_POINTS_TOTAL"); ?></th>
                    <th class="uk-visible@m"><?php echo Text::_("COM_MARATHONMANAGER_LABEL_TIME_TOTAL"); ?></th>
                </tr>
                </thead>
                <tbody id="results-table-body"
                       uk-scrollspy="target: >tr>td; delay:20; cls: uk-animation-slide-left-small">
                <?php foreach ($this->results as $result):
                    // Add some extra data to the result object for use in the layout
                    $result->parcours_title = isset($this->parcours[$result->parcours_id]) ? $this->parcours[$result->parcours_id]->title : '';
                    $result->category_title = isset($this->categories[$result->group_id]) ? $this->categories[$result->group_id]->title : '';

                    ?>
                    <tr data-parcours="<?php echo $result->parcours_id; ?>"
                        data-category="<?php echo $result->group_id; ?>"
                        data-team="<?php echo $result->team_name; ?>">
                        <td class="uk-text-center">
                            <?php
                            if ($result->place) {
                                echo $result->place;
                            } else {
                                echo "<span class=\"uk-text-muted uk-text-small\">{$result->place_msg}</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <div class="uk-hidden@m">
                                <?php
                                echo $teamMobileLayout->render(compact('result'));
                                ?>
                            </div>

                            <div class="uk-visible@m">
                                <?php

                                echo $teamLayout->render(compact('result'));
                                ?>
                            </div>

                        </td>
                        <td class="uk-visible@m"><?php echo $this->parcours[$result->parcours_id]->title; ?></td>
                        <td class="uk-visible@m"><?php
                            if (isset($this->categories[$result->group_id])) echo $this->categories[$result->group_id]->title;
                            ?></td>
                        <td class="uk-visible@m uk-text-center"><?php
                            echo $result->place_in_group ?: "-";
                            ?></td>
                        <td class="uk-visible@m"><?php echo $result->points_total; ?></td>
                        <td class="uk-visible@m"><?php echo $result->time_total; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo '<pre>' . var_export($this->results, 1) . '</pre>' ?>
        </div>
    </div>
</section>


