<?php

declare(strict_types=1);

namespace Libero\ViewsBundle\Views;

use ArrayAccess;
use IteratorAggregate;
use function array_replace_recursive;

final class TemplateView implements ArrayAccess, IteratorAggregate, View
{
    use HasContext;
    use IteratorArrayAccess;

    private $arguments;
    private $template;

    public function __construct(?string $template, array $arguments = [], array $context = [])
    {
        $this->template = $template;
        $this->arguments = $arguments;
        $this->context = $context;
    }

    public function hasArgument(string $key) : bool
    {
        return isset($this->arguments[$key]);
    }

    public function getArgument(string $key)
    {
        return $this->arguments[$key] ?? null;
    }

    public function getArguments() : array
    {
        return $this->arguments;
    }

    public function getTemplate() : ?string
    {
        return $this->template;
    }

    public function withArgument(string $key, $value) : TemplateView
    {
        return $this->withArguments([$key => $value]);
    }

    public function withArguments(array $arguments) : TemplateView
    {
        if ($arguments === $this->arguments || !$arguments) {
            return $this;
        }

        $view = clone $this;

        $view->arguments = array_replace_recursive($view->arguments, $arguments);

        return $view;
    }

    public function withTemplate(string $template) : TemplateView
    {
        if ($template === $this->template) {
            return $this;
        }

        $view = clone $this;

        $view->template = $template;

        return $view;
    }

    public function withContext(array $context) : TemplateView
    {
        $view = clone $this;

        $view->context = array_replace_recursive($view->context, $context);

        return $view;
    }

    public function getIterator()
    {
        yield 'template' => $this->template;
        yield 'arguments' => $this->arguments;
    }
}
