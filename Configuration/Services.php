<?php

declare(strict_types=1);

namespace DanielSiepmann\Tracking;

use DanielSiepmann\Tracking\Domain\Extractors\PageviewExtractor;
use DanielSiepmann\Tracking\Domain\Extractors\RecordviewExtractor;
use DanielSiepmann\Tracking\Domain\Extractors\Registry;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfiguration, ContainerBuilder $containerBuilder) {
    $containerBuilder->registerForAutoconfiguration(PageviewExtractor::class)->addTag('tracking.extractor.pageview');
    $containerBuilder->registerForAutoconfiguration(RecordviewExtractor::class)->addTag('tracking.extractor.recordview');
    $containerBuilder->addCompilerPass(new class() implements CompilerPassInterface {
        public function process(ContainerBuilder $containerBuilder): void
        {
            $registry = $containerBuilder->findDefinition(Registry::class);
            foreach ($containerBuilder->findTaggedServiceIds('tracking.extractor.pageview') as $id => $tags) {
                $definition = $containerBuilder->findDefinition($id);
                if (!$definition->isAutoconfigured() || $definition->isAbstract()) {
                    continue;
                }

                $registry->addMethodCall('addPageviewExtractor', [$definition]);
            }
            foreach ($containerBuilder->findTaggedServiceIds('tracking.extractor.recordview') as $id => $tags) {
                $definition = $containerBuilder->findDefinition($id);
                if (!$definition->isAutoconfigured() || $definition->isAbstract()) {
                    continue;
                }

                $registry->addMethodCall('addRecordviewExtractor', [$definition]);
            }
        }
    });
};

