<?php

namespace system\Interfaces;

use Psr\Http\Message\ServerRequestInterface as Request;


interface IRouter
{
    public function get(string $urlTemplate, string $controllerClass, string $actionMethod, array $paramTemplates = []) : IRouter;
    public function post(string $urlTemplate, string $controllerClass, string $actionMethod, array $paramTemplates = []) : IRouter;
    public function where(array $paramTemplates) : IRouter;
    public function routerRun(Request $request)  : IView;
}
