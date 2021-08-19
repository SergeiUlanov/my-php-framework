<?php

namespace system\Patterns;

use App\Config\ConfigApp as CFG;



trait SingletonLogTrait
{
    public static int    $singletonCounter = 0;
    public static string $singletonLog     = '';


    private static function toSingletonLog(string $subclass, $creatingType, bool $isFirst) : void
    {
        if(! CFG::IS_LOG_ADVANCED) {
            return;
        }
        $first = ($isFirst) ? 'first' : '';
        self::$singletonCounter++;
        self::$singletonLog .= '<span class="singleton-log__desc">'.self::$singletonCounter.' '.$creatingType.': </span> ';
        self::$singletonLog .= self::mkFormatName($subclass).' '.$first.' '.self::mkFormatCaller().'<br>';
    }



    /**
     * Получение имени класса с форматированием
     *
     * @param string $subclass
     * @return string
     */
    private static function mkFormatName(string $subclass) : string
    {
        return "«<span class='singleton-log__class'>{$subclass}</span>»";
    }



    /**
     * Построение строки с информацией, откуда был вызов создания экземпляра объекта.
     *
     * Метод ожидает, что фабрика имеет имя файла FactorySys.php
     *
     * @return string
     */
    private static function mkFormatCaller()
    {
        $trace = debug_backtrace();

        $idx  = 2;                                      // индекс 2 указывает непосредственно на вызов getInstance.
        $file = basename($trace[$idx]['file']);         // Однако если getInstance произошёл в фабрике, мне интересно
        if($file == 'FactorySys.php') {                 // кто обратился за объектом к фабрике.
            $idx = 3;                                   // Поэтому беру следующий индекс
            $file = basename($trace[$idx]['file']);
        }
        $line = $trace[$idx]['line'];
        $call = ' '.$trace[$idx]['class'].$trace[$idx]['type'].$trace[$idx]['function'].' ('.$file.', '.$line.') '.$idx;

        return "<sub class='singleton-log__caller'>$call</sub>";
    }


}   // end trait
