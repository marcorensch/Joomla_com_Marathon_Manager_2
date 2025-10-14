<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Site\View\Event;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use NXD\Component\MarathonManager\Site\Model\EventModel;

class HtmlView extends BaseHtmlView
{
	protected $item;

	/**
	 * The Form object
	 *
	 * @var  Form
	 */
	protected $form;

	public function display($tpl = null): void
	{
		/* @var $model  EventModel */
		$model            = $this->getModel();
		$this->item       = $model->getItem();
		$this->parcours   = $model->getCourses();
		$this->categories = $model->getGroups();
		$this->results    = $model->getResults();
		$this->params     = Factory::getApplication()->getParams();

		if ($this->item)
		{
			$eventHeader = new FileLayout('event-header', JPATH_ROOT . '/components/com_marathonmanager/layouts');
			$event       = $this->item;
			echo $eventHeader->render(compact('event'));

			parent::display($tpl);
		}
		else
		{
			throw new \Exception('Item not found', 404);
		}

	}
}