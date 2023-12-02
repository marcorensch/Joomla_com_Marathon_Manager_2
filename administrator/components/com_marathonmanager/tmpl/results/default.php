<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns'); // is for managing the table columns by user select which tabe has to be hidden

$route = Route::_('index.php?option=com_marathonmanager&view=results');
$canChange = true;
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder === 'a.ordering';

?>
<form action="<?php echo $route ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-warning">
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else: ?>
                    <table class="table" id="itemsList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_MARATHONMANAGER_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                        <tr>
                            <td style="width: 1%" class="text-center">
                                <?php echo HTMLHelper::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" style="width: 1%; min-width: 85px" class="text-center">
                                <?php echo TEXT::_('JSTATUS'); ?>
                            </th>
                            <th scope="col" style="width: 1%" class="text-center">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_PLACE_TITLE', 'a.place', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" style="min-width: 120px" class="">
                                <?php echo '<span class="visually-hidden">' . Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_RESULT_LINK_TITLE') . '</span>'; ?>
                            </th>
                            <th scope="col" style="min-width: 300px" class="">
                                <?php echo HTMLHelper::_('searchtools.sort', 'COM_MARATHONMANAGER_TABLE_TABLEHEAD_TEAM_TITLE', 'a.team_name', $listDirn, $listOrder); ?>
                            </th>

                            <th scope="col" style="min-width:25%; width:20%" class="">
                                <?php echo Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_EVENT_TITLE'); ?>
                            </th>

                            <th scope="col" style="min-width:15%;" class="">
                                <?php echo Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_COURSE_TITLE'); ?>
                            </th>

                            <th scope="col" style="min-width:15%;" class="">
                                <?php echo Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_GROUP_TITLE'); ?>
                            </th>

                            <th scope="col" style="width: 10%" class="d-none d-xl-table-cell">
                                <?php echo Text::_('JGRID_HEADING_ACCESS'); ?>
                            </th>
                            <th scope="col" class="d-none d-xl-table-cell text-center">
                                <?php echo Text::_('COM_MARATHONMANAGER_TABLE_TABLEHEAD_ID'); ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $n = count($this->items);
                        foreach ($this->items as $i => $item):
                            ?>
                            <tr class="row<?php echo $i % 2; ?>" data-draggable-group="listItems">
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                                </td>
                                <td class="text-center">
                                    <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'results.', true, 'cb'); ?>
                                </td>
                                <td class="text-center">
                                    <?php
                                    if($item->place_msg){
                                        echo '<span class="small">' . $item->place_msg . '</span>';
                                    }else{
                                        echo '<span>' . $item->place . '</span>';
                                    }
                                    ?>
                                </td>
                                <td class="has-context">

                                    <a class="btn btn-sm btn-outline-primary hasTooltip"
                                       href="<?php echo Route::_('index.php?option=com_marathonmanager&task=result.edit&id=' . (int)$item->id); ?>"
                                       title="<?php echo Text::_('COM_MARATHONMANAGER_EDIT_RESULT')?>">
                                        <?php echo Text::_('COM_MARATHONMANAGER_EDIT_RESULT'); ?>
                                    </a>
                                </td>
                                <td class="has-context">
                                    <?php if($item->team_id):?>
                                    <a class="hasTooltip"
                                       href="<?php echo Route::_('index.php?option=com_marathonmanager&task=registration.edit&id=' . (int)$item->team_id); ?>"
                                       target="_blank"
                                       title="<?php echo Text::_('COM_MARATHONMANAGER_EDIT_REGISTRATION')?>">
                                        <?php echo $this->escape($item->team_name); ?>
                                    </a>
                                    <?php else: ?>
                                        <span class="">
                                           <?php echo $this->escape($item->team_name); ?>
                                        </span>
                                    <?php endif;?>
                                </td>

                                <td class="">
                                    <a class="hasTooltip"
                                       href="<?php echo Route::_('index.php?option=com_marathonmanager&task=event.edit&id=' . (int)$item->event_id); ?>"
                                       target="_blank"
                                       title="<?php echo Text::_('COM_MARATHONMANAGER_EDIT_EVENT')?>">
                                        <?php echo $this->escape($item->event_name); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo $this->escape($item->parcours_title); ?>
                                </td>
                                <td>
                                    <?php echo $this->escape($item->group_title); ?>
                                </td>
                                <td class="small d-none d-xl-table-cell text-center">
                                    <?php echo $item->access_level; ?>
                                </td>
                                <td class="d-none d-xl-table-cell text-center">
                                    <?php echo $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php echo $this->pagination->getListFooter(); ?>
                <?php endif; ?>
                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="">
                <?php echo HTMLHelper::_('form.token'); ?>
            </div>
        </div>
    </div>
</form>