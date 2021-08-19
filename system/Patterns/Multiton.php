<?php

namespace system\Patterns;

use Exception;
use stdClass;
use App\Interfaces\IFactory;



class Multiton
{
    private static array  $instances = array();   // Статический массив для хранения созданных экземпляров дочерних подклассов
    private        string $key;                   // Для хранения уникального идентификатора экземпляра объекта

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
     * @param  string $key
     * @return mixed
     */
    public static function getInstance(string $key)
    {
        $isFirst = false;

        if(! isset(self::$instances[$key])) {
            self::$instances[$key] = new static;
            self::$instances[$key]->setKey($key);
            $isFirst = true;
        }
        self::toSingletonLog($key, 'getInstance', $isFirst);
        return self::$instances[$key];
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
     * @param  string $key
     * @param  IFactory $factory
     * @param  stdClass|null $params
     * @return static
     */
    public static function getInitInstance(string $key, IFactory $factory, ?stdClass $params = null)
    {
        $isFirst = false;

        if(! isset(self::$instances[$key])) {
            self::$instances[$key] = new static;                            // Работа метода полностью аналогична
            self::$instances[$key]->setKey($key);                           // работе getInstance. Отличие состоит
            self::$instances[$key]->initInstance($key, $factory, $params);  // лишь в вызове метода initInstance.
            $isFirst = true;
        }
        self::toSingletonLog($key, 'getInitInstance', $isFirst);

        return self::$instances[$key];
    }



    /**
     * Ввожу этот метод для возможности инициализации в подклассах.
     * Он вызывается при первом создании экземпляра подкласса.
     *
     * @param string $key
     * @param IFactory $factory
     * @param stdClass|null $params
     */
    protected function initInstance(string $key, IFactory $factory, ?stdClass $params = null)
    {
    }



    /**
     * Запомнить уникальный идентификатор экземпляра объекта
     *
     * @param string $key
     */
    private function setKey(string $key) : void
    {
        $this->key = $key;
    }



    /**
     * Возвращает уникальный идентификатор экземпляра объекта
     *
     * @return string
     */
    public function getKey() : string
    {
        return $this->key;
    }


}   // end class
