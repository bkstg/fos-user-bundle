<?php

declare(strict_types=1);

/*
 * This file is part of the BkstgFOSUserBundle package.
 * (c) Luke Bainbridge <http://www.lukebainbridge.ca/>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bkstg\FOSUserBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class BkstgFOSUserExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @param array            $configs   The processed configuration.
     * @param ContainerBuilder $container The container.
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        // If the timeline bundle is active register timeline services.
        $bundles = $container->getParameter('kernel.bundles');
        if (isset($bundles['BkstgTimelineBundle'])) {
            $loader->load('services.timeline.yml');
        }
        if (isset($bundles['BkstgSearchBundle'])) {
            $loader->load('services.search.yml');
        }
    }
}
