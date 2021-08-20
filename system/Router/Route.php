<?php

namespace system\Router;


/**
 * Просто структура, описывающая маршрут. Введена для замены массива.
 */
class Route
{
    public string $method;
    public string $urlTemplate;
    public array  $paramTemplates;
    public string $controllerClass;
    public string $actionMethod;


    public function __construct(
        string $method,
        string $urlTemplate,
        string $controllerClass,
        string $actionMethod,
        array  $paramTemplates = [])
    {
        $this->method          = $method;
        $this->urlTemplate     = $urlTemplate;
        $this->paramTemplates  = $paramTemplates;
        $this->controllerClass = $controllerClass;
        $this->actionMethod    = $actionMethod;
    }
}
