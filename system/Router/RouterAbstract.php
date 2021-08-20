<?php

namespace system\Router;

use App\Config\ConfigApp as CFG;
use App\Interfaces\IFactory;
use system\Interfaces\IRouter;
use system\Interfaces\IView;
use Psr\Http\Message\ServerRequestInterface as IRequest;



abstract class RouterAbstract implements IRouter
{
    protected IFactory $factory;
    protected array    $routes;      // Route[] $routes



    abstract public function routerRun(IRequest $request) : IView;



    /**
     * Конструктор выполняет инициализацию массива маршрутов
     * путём вызова метода initRouts из класса конфигурации.
     *
     * @param IFactory $factory - Передача фабрики для получения зависимостей
     */
    function __construct(IFactory $factory)
    {
        $this->factory = $factory;
        $this->routes  = array();

        CFG::initRouts($this);
    }



    /**
     * Если маршрут был найден, то вызывается этот метод.
     * Его задача создать класс контроллера и вызвать action-метод
     *
     * @param  Route    $route
     * @param  IRequest $request
     * @return IView
     */
    protected function routCall(Route $route, IRequest $request) : IView
    {
        $controller = new $route->controllerClass($this->factory);                     // Создание объекта для класса контроллера
        $view = call_user_func(array(&$controller, $route->actionMethod), $request);   // Вызов метода action, с передачей request

        return $view;
    }



    public function get(string $urlTemplate, string $controllerClass, string $actionMethod, array $paramTemplates = []) : IRouter
    {
        $this->routes[] = new Route('get', $urlTemplate, $controllerClass, $actionMethod, $paramTemplates);
        return $this;
    }



    public function post(string $urlTemplate, string $controllerClass, string $actionMethod, array $paramTemplates = []) : IRouter
    {
        $this->routes[] = new Route('post', $urlTemplate, $controllerClass, $actionMethod, $paramTemplates);
        return $this;
    }



    public function where(array $paramTemplates) : IRouter
    {
        $lastKey = array_key_last($this->routes);
        $this->routes[$lastKey]->paramTemplates = $paramTemplates;
        return $this;
    }


}   // end class
