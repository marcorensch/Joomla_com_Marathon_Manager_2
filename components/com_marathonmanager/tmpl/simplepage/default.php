<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_marathonmanager
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 *              All rights reserved
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */


\defined('_JEXEC') or die;

?>

<h1>SimplePage</h1>
<?php
$layout = $this->layout;

switch ($layout)
{
	default:
	case 'list':
		echo '<h2>List Layout</h2>';
		break;

	case 'grid':
		echo '<h2>Grid Layout</h2>';
		break;

}