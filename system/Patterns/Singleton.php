<?php

namespace system\Patterns;

use Exception;
use stdClass;
use App\Interfaces\IFactory;



class Singleton
{
    private static array $instances = array();    // Статический массив для хранения созданных экземпляров дочерних подклассов

    protected function __construct() { }          // Непубличный конструктор предотвращает создание объектов с использованием new
    protected function __clone() { }              // Клонирование и десериализация также должны быть запрещены для одиночек


    use SingletonLogTrait;



    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }



    /**
     * Создаёт новый или возвращает уже имеющийся экземпляр подкласса
     * в простых случаях, когда инициализация не требуется.
     *
     * @return mixed
     */
    public static function getInstance()
    {
        $subclass = static::class;
        $isFirst  = false;

        if(! isset(self::$instances[$subclass])) {      // В контексте ключевое слово "static"
            self::$instances[$subclass] = new static;   // означает «имя текущего класса»
            $isFirst = true;                            // это передаёт сюда имя создаваемого подкласса
        }
        self::toSingletonLog($subclass, 'getInstance', $isFirst);
        return self::$instances[$subclass];
    }



    /**
     * Создаёт новый или возвращает уже имеющийся экземпляр подкласса
     * с возможностью инициализации. Если Вам требуется создать объект
     * singleton с параметрами, определите в нём метод initInstance
     * и выполняйте получение экземпляра объекта этим методом.
     *
     * TODO ~ Возможно стоит отказаться от типа для параметра $params.
     *        Это даст возможность передавать в параметрах явно описанные структуры.
     *
     * TODO + Подумать как устранить дублирование кода с getInstance, без сильного усложнения логики работы
     *
     * @param  IFactory $factory
     * @param  stdClass|null $params
     * @return mixed|static
     */
    public static function getInitInstance(IFactory $factory, ?stdClass $params = null)
    {
        $subclass = static::class;
        $isFirst = false;

        if(! isset(self::$instances[$subclass])) {                         // Работа метода полностью аналогична
            self::$instances[$subclass] = new static;                      // работе getInstance. Отличие состоит
            self::$instances[$subclass]->initInstance($factory, $params);  // лишь в вызове метода initInstance.
            $isFirst = true;
        }
        self::toSingletonLog($subclass, 'getInitInstance', $isFirst);

        return self::$instances[$subclass];
    }



    /**
     * Ввожу этот метод для возможности инициализации в подклассах.
     * Он вызывается при первом создании экземпляра подкласса.
     *
     * @param IFactory $factory
     * @param stdClass|null $params
     */
    protected function initInstance(IFactory $factory, ?stdClass $params = null)
    {
    }


}   // end class
