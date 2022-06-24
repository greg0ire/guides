<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Setup;

use ArrayObject;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\NodeRenderers\DefaultNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\DocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\SpanNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\TableNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\InMemoryNodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\LazyNodeRendererFactory;
use phpDocumentor\Guides\NodeRenderers\TemplateNodeRenderer;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\References\ReferenceResolver;
use phpDocumentor\Guides\References\Resolver\DocResolver;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderer\OutputFormatRenderer;
use phpDocumentor\Guides\Renderer\TemplateRenderer;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Guides\UrlGenerator;
use Psr\Log\Test\TestLogger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

final class QuickStart
{
    public static function createRstParser(): Parser
    {
        return new Parser(
            new UrlGenerator(),
            [
                MarkupLanguageParser::createInstance()
            ]
        );
    }

    public static function createRenderer(): Renderer
    {
        $logger = new TestLogger();
        $nodeRenderers = new ArrayObject();
        $nodeFactoryCallback = static function () use ($nodeRenderers) {
            return new InMemoryNodeRendererFactory(
                $nodeRenderers,
                new DefaultNodeRenderer()
            );
        };

        $twigBuilder = new EnvironmentBuilder();
        $renderer = new Renderer(
            [
                new OutputFormatRenderer(
                    'html',
                    new LazyNodeRendererFactory($nodeFactoryCallback),
                    new TemplateRenderer($twigBuilder)
                ),
            ],
            $twigBuilder
        );

        $nodeRenderers[] = new DocumentNodeRenderer($renderer);
        $nodeRenderers[] = new SpanNodeRenderer(
            $renderer,
            new ReferenceResolver([new DocResolver()]),
            $logger,
            new UrlGenerator()
        );
        $nodeRenderers[] = new TableNodeRenderer($renderer);

        $config = new Configuration();
        foreach ($config->htmlNodeTemplates() as $node => $template) {
            $nodeRenderers[] = new TemplateNodeRenderer(
                $renderer,
                $template,
                $node
            );
        }

        $twigBuilder->setEnvironmentFactory(function () use ($logger, $renderer) {
            $twig = new Environment(
                new FilesystemLoader(
                    [
                        __DIR__  . '/../../resources/template'
                    ]
                )
            );
            $twig->addExtension(new AssetsExtension(
                $logger,
                $renderer,
                new UrlGenerator(),
            ));

            return $twig;
        });

        return $renderer;
    }
}