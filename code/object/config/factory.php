<?php
/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */

namespace Kodekit\Library;

/**
 * Object Config Factory
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Object\Config
 */
final class ObjectConfigFactory extends Object implements ObjectSingleton
{
    /**
     * Config object prototypes
     *
     * @var array
     */
    private $__prototypes;

    /**
     * Registered config file formats.
     *
     * @var array
     */
    protected $_formats;

    /**
     * Constructor
     *
     * @param ObjectConfig $config An optional ObjectConfig object with configuration options.
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        $this->_formats = $config->formats;
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  ObjectConfig $config	An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'formats' => array(
                'php'  => __NAMESPACE__.'\ObjectConfigPhp',
                'ini'  => __NAMESPACE__.'\ObjectConfigIni',
                'json' => __NAMESPACE__.'\ObjectConfigJson',
                'xml'  => __NAMESPACE__.'\ObjectConfigXml',
                'yaml' => __NAMESPACE__.'\ObjectConfigYaml'
            )
        ));

        parent::_initialize($config);
    }

    /**
     * Get a registered config object.
     *
     * @param  string $format The format name
     * @param  array|ObjectConfig $options An associative array of configuration options or a ObjectConfig instance.
     * @throws \InvalidArgumentException    If the format isn't registered
     * @throws \UnexpectedValueException   If the format object doesn't implement the ObjectConfigSerializable
     * @return ObjectConfigSerializable
     */
    public function createFormat($format, $options = array())
    {
        $name = strtolower($format);

        if (!isset($this->_formats[$name])) {
            throw new \RuntimeException(sprintf('Unsupported config format: %s ', $name));
        }

        if(!isset($this->__prototypes[$name]))
        {
            $class    = $this->_formats[$name];
            $instance = new $class();

            if(!$instance instanceof ObjectConfigSerializable)
            {
                throw new \UnexpectedValueException(
                    'Format: '.get_class($instance).' does not implement ObjectConfigSerializable Interface'
                );
            }

            $this->__prototypes[$name] = $instance;
        }

        //Clone the object
        $result = clone $this->__prototypes[$name];
        $result->merge($options);

        return $result;
    }

    /**
     * Register config format
     *
     * @param string $format The name of the format
     * @param mixed  $class Class name
     * @throws \InvalidArgumentException If the class does not exist
     * @return ObjectConfigFactory
     */
    public function registerFormat($format, $class)
    {
        if(!class_exists($class, true)) {
            throw new \InvalidArgumentException('Class : '.$class.' cannot does not exist.');
        }

        $this->_formats[$format] = $class;

        //In case the format is being re-registered clear the prototype
        if(isset($this->__prototypes[$format])) {
            unset($this->__prototypes[$format]);
        }

        return $this;
    }

    /**
     * Read a config from a string
     *
     * @param  string  $format
     * @param  string  $config
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return ObjectConfigInterface|array
     */
    public function fromString($format, $config, $object = true)
    {
        $config = $this->createFormat($format)->fromString($config, $object);
        return $config;
    }

    /**
     * Read a config from a file.
     *
     * @param  string  $filename
     * @param  bool    $object  If TRUE return a ConfigObject, if FALSE return an array. Default TRUE.
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @return ObjectConfigInterface|array
     */
    public function fromFile($filename, $object = true)
    {
        $pathinfo = pathinfo($filename);

        if (!isset($pathinfo['extension']))
        {
            throw new \RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected', $filename
            ));
        }

        $config = $this->createFormat($pathinfo['extension'])->fromFile($filename, $object);
        return $config;
    }

    /**
     * Writes a config to a file
     *
     * @param string $filename
     * @param ObjectConfigInterface $config
     * @throws \RuntimeException
     * @return ObjectConfigFactory
     */
    public function toFile($filename, ObjectConfigInterface $config)
    {
        $pathinfo = pathinfo($filename);

        if (!isset($pathinfo['extension']))
        {
            throw new \RuntimeException(sprintf(
                'Filename "%s" is missing an extension and cannot be auto-detected', $filename
            ));
        }

        $this->createFormat($pathinfo['extension'])->toFile($filename, $config);
        return $this;
    }

    /**
     * Check if the format is registered
     *
     * @param string $format A config format
     * @return bool TRUE if the format is a registered, FALSE otherwise.
     */
    public function isRegistered($format)
    {
        return isset($this->_formats[$format]);
    }
}