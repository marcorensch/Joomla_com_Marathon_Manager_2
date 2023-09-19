<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @source      https://blog.astrid-guenther.de/en/die-daten-der-datenbank-im-frontend-nutzen/
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$app = Factory::getApplication();

$wa = $app->getDocument()->getWebAssetManager();
$wa->useScript('com_marathonmanager.admin-events-modal');

$function = $app->input->getCmd('function', 'jSelectItems');
$onclick = $this->escape($function);

$action = Route::_('index.php?option=com_marathonmanager&amp;view=events&amp;layout=modal&amp;tmpl=component&amp;function=' . $function . '&amp;' . Session::getFormToken() . '=1');

?>

<div class="container-popup">
	<form action="<?php echo $action; ?>" name="adminForm" id="adminForm" class="form-inline">
		<?php if(empty($this->items)): ?>
			<div class="alert alert-info">
				<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php else: ?>
		<table class="table table-sm">
			<thead>
			<tr>
				<th scope="col" style="width: 10%" class="d-none d-md-table-cell">
				</th>
				<th scope="col" style="width: 1%">
				</th>
			</tr>
			</thead>
			<tbody>
			<?php
			$iconStates = [
				-2 => 'icon-trash',
				0 => 'icon-unpublish',
				1 => 'icon-publish',
				2 => 'icon-archive'
			]
			?>
			<?php foreach ($this->items as $i => $item): ?>
				<tr class="row<?php echo $i % 2; ?>">
					<th scope="row">
						<a href="javascript:void(0)" class="select-link"
						data-function="<?php echo $onclick;?>" data-id="<?php echo $item->id;?>" data-title="<?php echo $this->escape($item->title);?>">
							<?php echo $this->escape($item->title);?>
						</a>
					</th>
					<td class="text-center">
						<?php echo $item->id;?>
					</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
		<?php endif;?>
		<input type="hidden" name="task" value="">
		<input type="hidden" name="boxchecked" value="<?php echo $app->input->get('forcedLanguage','','CMD');?>">
		<?php echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
