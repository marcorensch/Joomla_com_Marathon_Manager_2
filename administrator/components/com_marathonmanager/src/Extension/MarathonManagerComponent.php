<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace NXD\Component\MarathonManager\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Categories\CategoryInterface;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use NXD\Component\Hello\Administrator\Service\HTML\AdministratorService;
use Psr\Container\ContainerInterface;

class MarathonManagerComponent extends MVCComponent implements BootableExtensionInterface, CategoryServiceInterface
{
    use CategoryServiceTrait;
    use HTMLRegistryAwareTrait;

    public function boot(ContainerInterface $container): void
    {
        $this->getRegistry()->register('marathonmanageradministrator', new AdministratorService );
    }

	// Required for Catergory Support (count of elements)
	public function countItems(array $items, string $section): void
	{
		try{
			$config = (object) array(
				'related_tbl' => $this->getTableNameForSection($section),
				'state_col' => 'published',
				'group_col' => 'catid',
				'relation_type' => 'category_or_group',
			);

			ContentHelper::countRelations($items, $config);
		}
		catch (\Exception $e)
		{
			// Ignore errors
		}
	}

	protected function getTableNameForSection(string $section): string
	{
		// could be used to return a different table name for a section
		return ($section === 'category') ? 'categories' : '#__com_marathonmanager_events';
	}

	protected function getStateColumnForSection(string $section = null): string
	{
		// could be used to return a different state column for a section
		return 'published';
	}

	public function prepareForm(Form $form, $data)
	{
		// TODO: Implement prepareForm() method.
	}
}