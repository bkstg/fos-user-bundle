<?php

namespace Bkstg\FOSUserBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class ValidatorPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $validator_builder = $container->getDefinition('validator.builder');
        $validator_files = [];

        $finder = new Finder();
        foreach ($finder->files()->in(__DIR__ . '/../../Resources/config/validation') as $file) {
            $validator_files[] = $file->getRealPath();
        }

        $validator_builder->addMethodCall('addYamlMappings', [$validator_files]);
        $container->addResource(new DirectoryResource(__DIR__ . '/../../Resources/config/validation'));
    }
}
