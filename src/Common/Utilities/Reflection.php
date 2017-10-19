<?php

/**
 * (c) Meritoo.pl, http://www.meritoo.pl
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Meritoo\Common\Utilities;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Util\Inflector;
use Meritoo\Common\Collection\Collection;
use Meritoo\Common\Exception\Reflection\CannotResolveClassNameException;
use Meritoo\Common\Exception\Reflection\MissingChildClassesException;
use Meritoo\Common\Exception\Reflection\TooManyChildClassesException;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use ReflectionObject;
use ReflectionProperty;

/**
 * Useful reflection methods
 *
 * @author    Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
 * @copyright Meritoo.pl
 */
class Reflection
{
    /**
     * Returns names of methods for given class / object
     *
     * @param object|string $class              The object or name of object's class
     * @param bool          $withoutInheritance (optional) If is set to true, only methods for given class are returned.
     *                                          Otherwise - all methods, with inherited methods too.
     * @return array
     */
    public static function getMethods($class, $withoutInheritance = false)
    {
        $effect = [];

        $reflection = new ReflectionClass($class);
        $methods = $reflection->getMethods();

        if (!empty($methods)) {
            $className = self::getClassName($class);

            foreach ($methods as $method) {
                if ($method instanceof ReflectionMethod) {
                    if ($withoutInheritance && $className !== $method->class) {
                        continue;
                    }

                    $effect[] = $method->name;
                }
            }
        }

        return $effect;
    }

    /**
     * Returns constants of given class / object
     *
     * @param object|string $class The object or name of object's class
     * @return array
     */
    public static function getConstants($class)
    {
        $reflection = new ReflectionClass($class);

        return $reflection->getConstants();
    }

    /**
     * Returns maximum constant from all constants of given class / object.
     * Values of constants should be integers.
     *
     * @param object|string $class The object or name of object's class
     * @return int|null
     */
    public static function getMaxNumberConstant($class)
    {
        $constants = self::getConstants($class);

        if (empty($constants)) {
            return null;
        }

        $maxNumber = 0;

        foreach ($constants as $constant) {
            if (is_numeric($constant) && $constant > $maxNumber) {
                $maxNumber = $constant;
            }
        }

        return $maxNumber;
    }

    /**
     * Returns information if given class / object has given method
     *
     * @param object|string $class  The object or name of object's class
     * @param string        $method Name of the method to find
     * @return bool
     */
    public static function hasMethod($class, $method)
    {
        $reflection = new ReflectionClass($class);

        return $reflection->hasMethod($method);
    }

    /**
     * Returns information if given class / object has given property
     *
     * @param object|string $class    The object or name of object's class
     * @param string        $property Name of the property to find
     * @return bool
     */
    public static function hasProperty($class, $property)
    {
        $reflection = new ReflectionClass($class);

        return $reflection->hasProperty($property);
    }

    /**
     * Returns information if given class / object has given constant
     *
     * @param object|string $class    The object or name of object's class
     * @param string        $constant Name of the constant to find
     * @return bool
     */
    public static function hasConstant($class, $constant)
    {
        $reflection = new ReflectionClass($class);

        return $reflection->hasConstant($constant);
    }

    /**
     * Returns value of given constant
     *
     * @param object|string $class    The object or name of object's class
     * @param string        $constant Name of the constant that contains a value
     * @return mixed
     */
    public static function getConstantValue($class, $constant)
    {
        $reflection = new ReflectionClass($class);

        if (self::hasConstant($class, $constant)) {
            return $reflection->getConstant($constant);
        }

        return null;
    }

    /**
     * Returns value of given property.
     * Looks for proper getter for the property.
     *
     * @param mixed  $object   Object that should contains given property
     * @param string $property Name of the property that contains a value. It may be also multiple properties
     *                         dot-separated, e.g. "invoice.user.email".
     * @param bool   $force    (optional) If is set to true, try to retrieve value even if the object doesn't have
     *                         property. Otherwise - not.
     * @return mixed
     */
    public static function getPropertyValue($object, $property, $force = false)
    {
        $value = null;

        /*
         * Property is a dot-separated string?
         * Let's find all values of the chain, of the dot-separated properties
         */
        if (Regex::contains($property, '.')) {
            $exploded = explode('.', $property);

            $property = $exploded[0];
            $object = self::getPropertyValue($object, $property, $force);

            /*
             * Value of processed property from the chain is not null?
             * Let's dig more and get proper value
             *
             * Required to avoid bug:
             * ReflectionObject::__construct() expects parameter 1 to be object, null given
             * (...)
             * 4. at ReflectionObject->__construct (null)
             * 5. at Reflection ::getPropertyValue (null, 'name', true)
             * 6. at ListService->getItemValue (object(Deal), 'project.name', '0')
             *
             * while using "project.name" as property - $project has $name property ($project exists in the Deal class)
             * and the $project equals null
             *
             * Krzysztof Niziol <krzysztof.niziol@meritoo.pl>
             * 2016-11-07
             */
            if (null !== $object) {
                unset($exploded[0]);

                $property = implode('.', $exploded);
                $value = self::getPropertyValue($object, $property, $force);
            }
        } else {
            $className = self::getClassName($object);
            $reflectionProperty = null;

            /*
             * 1st try:
             * Use \ReflectionObject class
             */
            try {
                $reflectionProperty = new ReflectionProperty($className, $property);
                $value = $reflectionProperty->getValue($object);
            } catch (ReflectionException $exception) {
                /*
                 * 2nd try:
                 * Look for the get / has / is methods
                 */
                $class = new ReflectionObject($object);
                $valueFound = false;

                if ($class->hasProperty($property) || $force) {
                    $property = Inflector::classify($property);

                    $getterPrefixes = [
                        'get',
                        'has',
                        'is',
                    ];

                    foreach ($getterPrefixes as $prefix) {
                        $getterName = sprintf('%s%s', $prefix, $property);

                        if ($class->hasMethod($getterName)) {
                            $method = new ReflectionMethod($object, $getterName);

                            /*
                             * Getter is not accessible publicly?
                             * I have to skip it, to avoid an error like this:
                             *
                             * Call to protected method My\ExtraClass::getExtraProperty() from context 'My\ExtraClass'
                             */
                            if ($method->isProtected() || $method->isPrivate()) {
                                continue;
                            }

                            $value = $object->{$getterName}();
                            $valueFound = true;
                            break;
                        }
                    }
                }

                if (!$valueFound && null !== $reflectionProperty) {
                    /*
                     * Oops, value of the property is still unknown
                     *
                     * 3rd try:
                     * Let's modify accessibility of the property and try again to get value
                     */
                    $reflectionProperty->setAccessible(true);
                    $value = $reflectionProperty->getValue($object);
                    $reflectionProperty->setAccessible(false);
                }
            }
        }

        return $value;
    }

    /**
     * Returns values of given property for given objects.
     * Looks for proper getter for the property.
     *
     * @param Collection|object|array $objects  The objects that should contain given property. It may be also one
     *                                          object.
     * @param string                  $property Name of the property that contains a value
     * @param bool                    $force    (optional) If is set to true, try to retrieve value even if the
     *                                          object does not have property. Otherwise - not.
     * @return array
     */
    public static function getPropertyValues($objects, $property, $force = false)
    {
        /*
         * No objects?
         * Nothing to do
         */
        if (empty($objects)) {
            return [];
        }

        if ($objects instanceof Collection) {
            $objects = $objects->toArray();
        }

        $values = [];
        $objects = Arrays::makeArray($objects);

        foreach ($objects as $entity) {
            $value = self::getPropertyValue($entity, $property, $force);

            if (null !== $value) {
                $values[] = $value;
            }
        }

        return $values;
    }

    /**
     * Returns a class name for given source
     *
     * @param array|object|string $source           An array of objects, namespaces, object or namespace
     * @param bool                $withoutNamespace (optional) If is set to true, namespace is omitted. Otherwise -
     *                                              not, full name of class is returned, with namespace.
     * @return string|null
     */
    public static function getClassName($source, $withoutNamespace = false)
    {
        /*
         * First argument is not proper source of class?
         * Nothing to do
         */
        if (empty($source) || (!is_array($source) && !is_object($source) && !is_string($source))) {
            return null;
        }

        $name = '';

        /*
         * An array of objects was provided?
         * Let's use first of them
         */
        if (is_array($source)) {
            $source = Arrays::getFirstElement($source);
        }

        /*
         * Let's prepare name of class
         */
        if (is_object($source)) {
            $name = get_class($source);
        } elseif (is_string($source) && (class_exists($source) || trait_exists($source))) {
            $name = $source;
        }

        /*
         * Name of class is still unknown?
         * Nothing to do
         */
        if (empty($name)) {
            return null;
        }

        /*
         * Namespace is not required?
         * Let's return name of class only
         */
        if ($withoutNamespace) {
            $classOnly = Miscellaneous::getLastElementOfString($name, '\\');

            if (null !== $classOnly) {
                $name = $classOnly;
            }

            return $name;
        }

        return ClassUtils::getRealClass($name);
    }

    /**
     * Returns namespace of class for given source
     *
     * @param array|object|string $source An array of objects, namespaces, object or namespace
     * @return string
     */
    public static function getClassNamespace($source)
    {
        $fullClassName = self::getClassName($source);

        if (empty($fullClassName)) {
            return '';
        }

        $className = self::getClassName($source, true);

        if ($className == $fullClassName) {
            return $className;
        }

        return Miscellaneous::getStringWithoutLastElement($fullClassName, '\\');
    }

    /**
     * Returns information if given interface is implemented by given class / object
     *
     * @param array|object|string $source    An array of objects, namespaces, object or namespace
     * @param string              $interface The interface that should be implemented
     * @return bool
     */
    public static function isInterfaceImplemented($source, $interface)
    {
        $className = self::getClassName($source);
        $interfaces = class_implements($className);

        return in_array($interface, $interfaces);
    }

    /**
     * Returns information if given child class is a subclass of given parent class
     *
     * @param array|object|string $childClass  The child class. An array of objects, namespaces, object or namespace.
     * @param array|object|string $parentClass The parent class. An array of objects, namespaces, object or namespace.
     * @return bool
     */
    public static function isChildOfClass($childClass, $parentClass)
    {
        $childClassName = self::getClassName($childClass);
        $parentClassName = self::getClassName($parentClass);

        $parents = class_parents($childClassName);

        if (is_array($parents)) {
            return in_array($parentClassName, $parents);
        }

        return false;
    }

    /**
     * Returns given object properties
     *
     * @param array|object|string $source         An array of objects, namespaces, object or namespace
     * @param int                 $filter         (optional) Filter of properties. Uses ReflectionProperty class
     *                                            constants. By default all properties are returned.
     * @param bool                $includeParents (optional) If is set to true, properties of parent classes are
     *                                            included (recursively). Otherwise - not.
     * @return array|ReflectionProperty
     */
    public static function getProperties($source, $filter = null, $includeParents = false)
    {
        $className = self::getClassName($source);
        $reflection = new ReflectionClass($className);

        if (null === $filter) {
            $filter = ReflectionProperty::IS_PRIVATE
                + ReflectionProperty::IS_PROTECTED
                + ReflectionProperty::IS_PUBLIC
                + ReflectionProperty::IS_STATIC;
        }

        $properties = $reflection->getProperties($filter);
        $parentProperties = [];

        if ($includeParents) {
            $parent = self::getParentClass($source);

            if (false !== $parent) {
                $parentClass = $parent->getName();
                $parentProperties = self::getProperties($parentClass, $filter, $includeParents);
            }
        }

        return array_merge($properties, $parentProperties);
    }

    /**
     * Returns a parent class or false if there is no parent class
     *
     * @param array|object|string $source An array of objects, namespaces, object or namespace
     * @return ReflectionClass|bool
     */
    public static function getParentClass($source)
    {
        $className = self::getClassName($source);
        $reflection = new ReflectionClass($className);

        return $reflection->getParentClass();
    }

    /**
     * Returns child classes of given class.
     * It's an array of namespaces of the child classes or null (if given class has not child classes).
     *
     * @param array|object|string $class Class who child classes should be returned. An array of objects, strings,
     *                                   object or string.
     * @return array|null
     * @throws CannotResolveClassNameException
     */
    public static function getChildClasses($class)
    {
        $allClasses = get_declared_classes();

        /*
         * No classes?
         * Nothing to do
         */
        if (empty($allClasses)) {
            return null;
        }

        $className = self::getClassName($class);

        /*
         * Oops, cannot resolve class
         */
        if (null === $className) {
            throw new CannotResolveClassNameException($class);
        }

        $childClasses = [];

        foreach ($allClasses as $oneClass) {
            if (self::isChildOfClass($oneClass, $className)) {
                /*
                 * Attention. I have to use ClassUtils::getRealClass() method to avoid problem with the proxy / cache
                 * classes. Example:
                 * - My\ExtraBundle\Entity\MyEntity
                 * - Proxies\__CG__\My\ExtraBundle\Entity\MyEntity
                 *
                 * It's actually the same class, so I have to skip it.
                 */
                $realClass = ClassUtils::getRealClass($oneClass);

                if (in_array($realClass, $childClasses)) {
                    continue;
                }

                $childClasses[] = $realClass;
            }
        }

        return $childClasses;
    }

    /**
     * Returns namespace of one child class which extends given class.
     * Extended class should has only one child class.
     *
     * @param array|object|string $parentClass Class who child class should be returned. An array of objects,
     *                                         namespaces, object or namespace.
     * @return mixed
     *
     * @throws MissingChildClassesException
     * @throws TooManyChildClassesException
     */
    public static function getOneChildClass($parentClass)
    {
        $childClasses = self::getChildClasses($parentClass);

        /*
         * No child classes?
         * Oops, the base / parent class hasn't child class
         */
        if (empty($childClasses)) {
            throw new MissingChildClassesException($parentClass);
        }

        /*
         * More than 1 child class?
         * Oops, the base / parent class has too many child classes
         */
        if (count($childClasses) > 1) {
            throw new TooManyChildClassesException($parentClass, $childClasses);
        }

        return trim($childClasses[0]);
    }

    /**
     * Returns property, the ReflectionProperty instance, of given object
     *
     * @param array|object|string $class    An array of objects, namespaces, object or namespace
     * @param string              $property Name of the property
     * @param int                 $filter   (optional) Filter of properties. Uses ReflectionProperty class constants.
     *                                      By default all properties are allowed / processed.
     * @return null|ReflectionProperty
     */
    public static function getProperty($class, $property, $filter = null)
    {
        $className = self::getClassName($class);
        $properties = self::getProperties($className, $filter);

        if (!empty($properties)) {
            /* @var $reflectionProperty ReflectionProperty */
            foreach ($properties as $reflectionProperty) {
                if ($reflectionProperty->getName() == $property) {
                    return $reflectionProperty;
                }
            }
        }

        return null;
    }

    /**
     * Returns information if given class / object uses / implements given trait
     *
     * @param array|object|string $class         An array of objects, namespaces, object or namespace
     * @param array|string        $trait         An array of strings or string
     * @param bool                $verifyParents If is set to true, parent classes are verified if they use given
     *                                           trait. Otherwise - not.
     * @return bool|null
     * @throws CannotResolveClassNameException
     */
    public static function usesTrait($class, $trait, $verifyParents = false)
    {
        $className = self::getClassName($class);
        $traitName = self::getClassName($trait);

        /*
         * Oops, cannot resolve class
         */
        if (empty($className)) {
            throw new CannotResolveClassNameException($class);
        }

        /*
         * Oops, cannot resolve trait
         */
        if (empty($traitName)) {
            throw new CannotResolveClassNameException($class, false);
        }

        $reflection = new ReflectionClass($className);
        $traitsNames = $reflection->getTraitNames();

        $uses = in_array($traitName, $traitsNames);

        if (!$uses && $verifyParents) {
            $parentClassName = self::getParentClassName($className);

            if (null !== $parentClassName) {
                return self::usesTrait($parentClassName, $trait, true);
            }
        }

        return $uses;
    }

    /**
     * Returns name of the parent class.
     * If given class does not extend another, returns null.
     *
     * @param array|object|string $class An array of objects, namespaces, object or namespace
     * @return string|null
     */
    public static function getParentClassName($class)
    {
        $className = self::getClassName($class);
        $reflection = new ReflectionClass($className);
        $parentClass = $reflection->getParentClass();

        if (null === $parentClass || false === $parentClass) {
            return null;
        }

        return $parentClass->getName();
    }
}
