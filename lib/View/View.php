<?php

namespace Netgen\BlockManager\View;

abstract class View implements ViewInterface
{
    /**
     * @var string
     */
    protected $context;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * Returns the view context.
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the view context.
     *
     * @param string $context
     */
    public function setContext($context)
    {
        $this->context = $context;
    }

    /**
     * Returns the view template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets the view template.
     *
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Returns the view parameters.
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets the view parameters.
     *
     * @param array $parameters
     */
    public function setParameters(array $parameters = array())
    {
        $this->parameters = $parameters;
    }
}
