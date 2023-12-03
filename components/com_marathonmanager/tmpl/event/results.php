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

$wa = \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle(
    <<<CSS
        a span.nxd-team-name,
        span.nxd-team-name {
            color: rgba(0,0,0,0.7);
            font-size: 1.1rem;
            font-weight: bolder;
        }
        .uk-table-striped tbody tr{
            border-top: none !important;
            border-bottom: none !important;
        }

        .filtered-hidden{
            display: none !important;
        }

        .nxd-table-middle{
            background-color: #f8f8f8;
        }
CSS

);

$wa->addInlineScript(
<<<JS

document.addEventListener("DOMContentLoaded", ()=>{
    const parcoursFilter = document.getElementById('filter-parcours');
    const categoryFilter = document.getElementById('filter-category');
    const resultsTable = document.getElementById('results-table');
    const resultsTableBody = document.getElementById('results-table-body');
    const resultsTableRows = resultsTableBody.querySelectorAll('tr');
    
    categoryFilter.addEventListener("change", (e)=>{
        const selectedCategory = e.target.value;
        if(selectedCategory === ''){
                resultsTable.classList.add('uk-table-striped');
            }else{
                resultsTable.classList.remove('uk-table-striped');
            }
        resultsTableRows.forEach((row)=>{
            if(selectedCategory === ''){
                row.classList.remove('filtered-hidden');
            }else{
                if(row.dataset.category === selectedCategory){
                    row.classList.remove('filtered-hidden');
                }else{
                    row.classList.add('filtered-hidden');
                }
            }
        });
        resetStripedTable();
    });
    
    function resetStripedTable(){
        const visibleElements = resultsTableBody.querySelectorAll('tr:not(.filtered-hidden)');
        visibleElements.forEach((row, index)=>{
            if(index % 2 === 0){
                row.classList.add('nxd-table-middle');
            }else{
                row.classList.remove('nxd-table-middle');
            }
        });
    }
})

JS

)
?>

<?php echo $eventHeader->render(compact('event')); ?>

<section>
    <h2>Results</h2>
    <div id="event-results">
        <hr>
        <div class="uk-position-relative uk-flex-right@m uk-grid-small uk-child-width-1-1 uk-child-width-auto@m" uk-grid
             style="z-index: 2; gap: 10px">
            <?php foreach ($event->result_files as $file):
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
                ?>
                <a target="_blank" href="<?php echo $fileRootPath . $file['file']; ?>"
                   class="uk-button uk-button-primary uk-button-large">
                    <i class="fas fa-file-<?php echo $fileIcon; ?>"></i>&nbsp;
                    <?php echo $file['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>
        <hr>

        <div id="results-table-container" class="uk-margin" nxd-filter="target: #results-table-body">
            <div class="uk-tile-muted uk-padding-small">
                <div class="uk-child-width-expand uk-grid-small uk-flex uk-flex-middle" uk-grid>
                    <div class="uk-visible@s">
                        <span class="uk-text-lead uk-text-muted">
                            <i class="fas fa-filter"></i> Filter
                        </span>
                    </div>
                    <div>
                        <select name="filter-parcours" id="filter-parcours" class="uk-select">
                            <option value="">Filter by Parcours</option>
                            <option value="1">Orienteering Marathon lang</option>
                            <option value="3">Orienteering Marathon kurz</option>
                            <option value="5">Score</option>
                            <option value="7">Trail Marathon lang</option>
                            <option value="9">Trail Marathon kurz</option>
                        </select>
                    </div>
                    <div>
                        <select name="filter-category" id="filter-category" class="uk-select">
                            <option value="">Filter by Category</option>
                            <option value="1">Men</option>
                            <option value="2">Women</option>
                            <option value="3">Couple</option>
                            <option value="4">Family</option>
                        </select>
                    </div>
                </div>

            </div>
            <table id="results-table" class="uk-table uk-table-striped uk-table-hover uk-overflow-hidden" uk-scrollspy="target: tr; delay:80; cls: uk-animation-fade">
                <thead>
                <tr>
                    <th class="uk-text-center">Platz</th>
                    <th>Team</th>
                    <th>Pts</th>
                    <th>Time</th>
                </tr>
                </thead>
                <tbody id="results-table-body" uk-scrollspy="target: >tr>td; delay:50; cls: uk-animation-slide-left-small">
                <?php foreach ($this->results as $result): ?>
                    <tr data-parcours="<?php echo $result->group_id;?>" data-category="<?php echo $result->group_id;?>">
                        <td class="uk-text-center">
                            <?php
                            if($result->place){
                                echo $result->place;
                            }else{
                                echo "<span class=\"uk-text-muted uk-text-small\">{$result->place_msg}</span>";
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($result->participants): ?>
                                <ul uk-accordion>
                                    <li>
                                        <a class="uk-accordion-title" href>
                                            <span class="nxd-team-name"><?php echo $result->team_name; ?></span>
                                        </a>
                                        <div class="uk-accordion-content">
                                            <?php foreach ($result->participants as $participant): ?>
                                                <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
                                                    <div>
                                                        <span class="nxd-participant-firstname">
                                                            <?php echo $participant['first_name']; ?>
                                                        </span>
                                                        <span class="nxd-participant-lastname">
                                                            <?php echo $participant['last_name']; ?>
                                                        </span>

                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </li>
                                </ul>
                            <?php else: ?>
                                <span class="nxd-team-name"><?php echo $result->team_name; ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $result->points_total; ?></td>
                        <td><?php echo $result->time_total; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php echo '<pre>' . var_export($this->results, 1) . '</pre>' ?>
        </div>
    </div>
</section>


