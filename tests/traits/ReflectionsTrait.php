<?php

namespace PHPUnitTests\Traits;

use ReflectionClass;

trait ReflectionsTrait
{
    /**
     * Call protected/private method of a class.
     *
     * @param object &$object    Instantiated object that we will run method on.
     * @param string $methodName Method name to call
     * @param array  $parameters Array of parameters to pass into method.
     *
     * @return mixed Method return.
     */
    public function invoke_nonpublic_method(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    /**
     * init_static_properties re-initialize static properties within
     *                        the model classes (like SwitchObject)
     *                        where table name should have a prefix
     *
     * @access protected
     *
     * @param  string $class
     * @param  string $property
     * @param  string $value
     *
     * @return void / string
     *
     */
    protected function init_static_properties($class, $property, $value)
    {
        $reflectedClass = new \ReflectionClass($class);
        $reflectedClass->setStaticPropertyValue($property, $value);
        $reflectedProperty = $reflectedClass->getProperty($property);
        $reflectedProperty->setAccessible(true);
        return $reflectedProperty;
    }

    /**
     * Get protected/private property value of a class.
     *
     * @param object &$object  Instantiated object that we will run method on.
     * @param string $property protected/private property name
     *
     * @return $value
     */
    protected function get_nonpublic_property(&$object, $property)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $value = $reflectionProperty->getValue($object);

        return $value;
    }

    /**
     * Set the value for protected/private property of a class.
     *
     * @param object &$object  Instantiated object that we will run method on.
     * @param string $property protected/private property name
     * @param mixed $value     value that should be assigned to the property
     */
    protected function set_nonpublic_property(&$object, $property, $value)
    {
        $reflectionClass = new ReflectionClass(get_class($object));
        $reflectionProperty = $reflectionClass->getProperty($property);
        $reflectionProperty->setAccessible(true);
        $value = $reflectionProperty->setValue($object, $value);
    }


}
