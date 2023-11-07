<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_marathonmanager
 *
 * @copyright   Copyright (c) 2023 NXD | nx-designs
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use NXD\Component\MarathonManager\Administrator\Extension\MarathonManagerComponent;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;

// Autoload dependencies with Composer
require_once __DIR__ . '/../vendor/autoload.php';

return new class implements ServiceProviderInterface {
	public function register(Container $container): void
	{
		$container->registerServiceProvider(new CategoryFactory('\\NXD\\Component\\MarathonManager'));
		$container->registerServiceProvider(new MVCFactory('\\NXD\\Component\\MarathonManager'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\NXD\\Component\\MarathonManager'));
        $container->registerServiceProvider(new RouterFactory('\\NXD\\Component\\MarathonManager'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new MarathonManagerComponent(
					$container->get(ComponentDispatcherFactoryInterface::class),
				);

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			});
	}
};