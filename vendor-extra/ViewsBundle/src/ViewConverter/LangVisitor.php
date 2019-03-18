<?php

declare(strict_types=1);

namespace Libero\ViewsBundle\ViewConverter;

use FluentDOM\DOM\Attribute;
use FluentDOM\DOM\Element;
use Libero\ViewsBundle\Views\View;
use Libero\ViewsBundle\Views\ViewConverterVisitor;
use Punic\Misc;

final class LangVisitor implements ViewConverterVisitor
{
    public function visit(Element $object, View $view) : View
    {
        if ($view->hasArgument('attributes') && !empty($view->getArgument('attributes')['lang'])) {
            return $view;
        }

        $lang = $object->ownerDocument->xpath()
            ->firstOf('ancestor-or-self::*[@xml:lang][1]/@xml:lang', $object);

        if (!$lang instanceof Attribute || $lang->nodeValue === $view->getContext('lang')) {
            return $view;
        }

        $context = ['lang' => $lang->nodeValue];
        $attributes = ['lang' => $context['lang']];
        $dir = 'right-to-left' === Misc::getCharacterOrder($context['lang']) ? 'rtl' : 'ltr';
        if ($view->getContext('dir') !== $dir) {
            $context['dir'] = $dir;
            $attributes['dir'] = $dir;
        }

        return $view->withArgument('attributes', $attributes)->withContext($context);
    }
}
