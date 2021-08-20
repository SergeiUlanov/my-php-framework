<?php

namespace system\Router;

use system\Interfaces\IRouter;
use system\Interfaces\IView;
use system\Exceptions\PageError;
use Psr\Http\Message\ServerRequestInterface as Request;



class RouterRequest extends RouterAbstract implements IRouter
{
    /**
     * Вариант проверки маршрутов на основе самостоятельного разбора
     * адресной строки и с заменой шаблонов именами параметров.
     *
     * Возвращаемый тип зависит от объекта создаваемого на основе маршрута,
     * поэтому использую интерфейс.
     * @throws PageError
     */
    public function routerRun(Request $request) : IView
    {
        $queryUrl = $request->getUri()->getPath();

        foreach($this->routes as $route)
        {
            $regexpUrl = $this->makeRegexpTemplate($route->urlTemplate, $route->paramTemplates);

            if($this->compareTemplate($regexpUrl, $queryUrl  /*,   $route->paramTemplates */ )) {
                $pathParams    = $this->extractParamValues($route->paramTemplates, $queryUrl);
                $requestParams = $this->addParamsToRequest($request, $pathParams);
                //
                return $this->routCall($route, $requestParams);
            }
        }
        throw new PageError('Маршрут не найден', PageError::ERROR_404);
    }



    /**
     * Заменяю имена параметров их шаблонами для регулярных выражений.
     *
     * @param  $routeUrl    - проверяемый маршрут с именами параметров
     * @param  $routeParams - массив с именами параметров и шаблонами регулярных выражений
     * @return string       - на выходе имею строку маршрута в виде для регулярных выражений
     */
    private function makeRegexpTemplate(string $routeUrl, array $routeParams) : string
    {
        $regexpUrl = $routeUrl;        // Проверяемый шаблон с именами параметров:  #^/(.*)-{cat_id}{pg}$#

        foreach($routeParams as $paramName => $paramTemplate) {
            $regexpUrl = str_replace('{'.$paramName.'}', $paramTemplate, $regexpUrl, $count);
        }
        return '#'.$regexpUrl.'#';     // Построенный шаблон для регулярных выражений: #^/(.*)-r([0-9]*)p([0-9]*)$#
    }



    /**
     * Сравнение шаблона проверяемого маршрута со строкой запроса
     */                                                                     /* Для оптимизации получения параметров */
    private function compareTemplate(string $regexpQuery, string $queryUrl  /*, array $paramTemplates */) : bool
    {
        // Сейчас значения параметров извлекаются в цикле по одному, см метод extractParamValues().
        // Имеется возможность получить сразу все значения параметров непосредственно после сравнения:
        // test.localhost/about-r60p1.html
        // #^/(.*)-r([0-9]*)p([0-9]*).html$#  =->  результат:
        //   0 => 'about-r60p1.html'
        //   1 => 'about'
        //   2 => '60'              <-= значения параметров начинаются с индекса 2,
        //   3 => '1'                   полная запись индекса: $arMatches[2+i][0]

        $count = preg_match_all($regexpQuery, $queryUrl, $arMatches);

        // TODO o Работу роутера можно оптимизировать. Значения параметром можно получить
        //        непосредственно из массива $arMatches здесь при удачной проверки маршрута.

        // Для оптимизации получения параметров:
        // if($count > 0) {
        //     echo "<p>{$regexpQuery}</p>";
        //     echo "<p>{$queryUrl}</p>";
        //     var_dump($arMatches);
        //
        //     $paramValues = array();
        //     $i = 0;
        //     foreach($paramTemplates as $paramName => $paramTpl) {
        //         $paramValues[$paramName] = $arMatches[2 + $i][0];
        //         $i++;
        //     }
        //     var_dump($paramValues);
        // }

        return ($count > 0);
    }



    /**
     * Извлекает значения параметров из URL-запроса и возвращение их в массиве.
     *
     * Перебираю массив параметров, создаю для каждого их них регулярное выражение,
     * и получаю по нему значение параметра.
     *
     * todo: ~ Получение параметров может быть оптимизировано. Их значения можно получить
     *         непосредственно из массива $arMatches при проверки маршрута, см метод compareTemplate().
     *         В таком случае этот метод будет ненужен.
     *
     * @param  array $paramTemplates - Массив ожидаемых в URL параметров. В качестве ключей используются имена
     *                                 параметров, в качестве значений шаблоны регулярных выражений.
     *                                 Пример массива: ["cat_id" => "r([0-9]*)", "pg" => "p([0-9]*)"]
     * @param  string $queryUrl      - Строка, содержащая URL запроса. Пример: "/about-r60p1.html"
     * @return array                 - Возвращаемый массив будет содержать значения параметров,
     *                                 с именами параметров в качестве ключей.
     *                                 Пример массива: ["cat_id" => 60, "pg" => 1]
     */
    private function extractParamValues(array $paramTemplates, string $queryUrl) : array
    {
        $paramValues = array();
        foreach($paramTemplates as $paramName => $paramTpl) {
            $parPat = "#(.*)$paramTpl(.*)#";
            preg_match($parPat, $queryUrl, $arMatches);
            $paramValues[$paramName] = $arMatches[2];
        }
        return $paramValues;
    }



    /**
     * Добавляю параметры из массива в request
     */
    private function addParamsToRequest(Request $request, array $paramValues) : Request
    {
        $arRequestCur = $request->getQueryParams();
        $arRequestNew = array_merge($arRequestCur, $paramValues);

        return $request->withQueryParams($arRequestNew);
    }


}   // end class
