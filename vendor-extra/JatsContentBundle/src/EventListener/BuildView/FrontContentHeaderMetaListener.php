<?php

declare(strict_types=1);

namespace Libero\JatsContentBundle\EventListener\BuildView;

use FluentDOM\DOM\Element;
use Libero\ViewsBundle\Views\ContextAwareTranslation;
use Libero\ViewsBundle\Views\SimplifiedViewConverterListener;
use Libero\ViewsBundle\Views\TemplateView;
use Libero\ViewsBundle\Views\View;
use Libero\ViewsBundle\Views\ViewConverter;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Libero\ViewsBundle\array_has_key;

final class FrontContentHeaderMetaListener
{
    use ContextAwareTranslation;
    use SimplifiedViewConverterListener;

    private $converter;

    public function __construct(ViewConverter $converter, TranslatorInterface $translator)
    {
        $this->converter = $converter;
        $this->translator = $translator;
    }

    protected function handle(Element $object, TemplateView $view) : View
    {
        $meta = $this->converter
            ->convert($object, '@LiberoPatterns/content-meta.html.twig', $view->getContext())
            ->getArguments();

        if (empty($meta)) {
            return $view;
        }

        if (!isset($meta['attributes']['aria-label'])) {
            $meta['attributes']['aria-label'] = $this->translate(
                'libero.patterns.content_header.meta.label',
                $view->getContext()
            );
        }

        return $view->withArgument('meta', $meta);
    }

    protected function canHandleTemplate(?string $template) : bool
    {
        return '@LiberoPatterns/content-header.html.twig' === $template;
    }

    protected function canHandleElement(string $element) : bool
    {
        return '{http://jats.nlm.nih.gov}front' === $element;
    }

    protected function canHandleArguments(array $arguments) : bool
    {
        return !array_has_key($arguments, 'meta');
    }
}
