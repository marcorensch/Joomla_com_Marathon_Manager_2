<?php
/**
 * @package     Joomla.Administrator
 *              Joomla.Site
 * @subpackage  com_marathonmanager
 * @author      Marco Rensch
 * @since        1.0.0
 *
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @copyright   Copyright (C) 2022 nx-designs NXD
 *
 */

namespace NXD\Component\MarathonManager\Administrator\Field;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Database\DatabaseInterface;

class AcyMailingMailSelectionField extends ListField
{

    protected $type = 'AcyMailingMailSelection';

    protected function getOptions(): array
    {
        $options = [];
        $options[] = HTMLHelper::_('select.option', '', Text::_('COM_MARATHONMANAGER_FIELD_DEFAULT_SELECT_ACYMAILING_MAIL'));

		if($this->isAcyMailingInstalled()){
			$db = Factory::getContainer()->get(DatabaseInterface::class);
			$query = $db->getQuery(true);
			$query->select('id, name');
			$query->from('#__acym_mail');
			$query->where($db->quoteName('type') . ' = ' . $db->quote('standard'));
			$query->order('id DESC');
			$db->setQuery($query);
			$mails = $db->loadObjectList();

			foreach ($mails as $mail) {
				$name = !mb_check_encoding($mail->name, 'UTF-8') ? mb_convert_encoding($mail->name, 'UTF-8', 'ISO-8859-1') : $mail->name;
				$options[] = HTMLHelper::_('select.option', $mail->id, $name);
			}
		}


        return array_merge(parent::getOptions(), $options);

    }

	/**
	 * Checks whether AcyMailing is installed and available
	 *
	 * @return bool
	 * @since 1.1.0
	 */
	private function isAcyMailingInstalled(): bool
	{
		$acyHelper = JPATH_ADMINISTRATOR . '/components/com_acym/helpers/helper.php';

		if (!file_exists($acyHelper)) {
			return false;
		}

		include_once($acyHelper);

		return class_exists('AcyMailing\Classes\ListClass');
	}

}