<?php

namespace system\Router;

use system\Interfaces\IRouter;
use system\Interfaces\IView;
use system\Exceptions\PageError;
use Psr\Http\Message\ServerRequestInterface as Request;



class RouterHtaccess extends RouterAbstract implements IRouter
{

    /**
     * Обычная обработка списка маршрутов, основанная на ожидании имени одного
     * из get- или post-параметров (сформированных в htaccess) в массиве маршрутов
     *
     * @param  Request $request
     * @return IView
     * @throws PageError
     */
    public function routerRun(Request $request) : IView
    {
        foreach($this->routes as $route)
        {
            switch($route->method)
            {
                case 'get':
                    $arReq = $request->getQueryParams();
                    break;

                case 'post':
                    $arReq = $request->getParsedBody();
                    break;

                default:
                    $arReq = array();
            }
            $routeQueryKey = $route->urlTemplate;

            if(isset($arReq[$routeQueryKey])) {            // Не требуется добавлять параметры в запрос как это было
                return $this->routCall($route, $request);  // в случае с классом RouterRequest. Тут параметры формируются
            }                                              // через htaccess и уже присутствуют в request.
        }
        throw new PageError('Маршрут не найден', PageError::ERROR_404);
    }


}   // end class