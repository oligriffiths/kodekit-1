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
 * Trim Filter.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Filter
 */
class FilterTrim extends FilterAbstract implements FilterTraversable
{
    /**
     * List of characters provided to the trim() function
     *
     * If this is null, then trim() is called with no specific character list, and its default behavior will be invoked,
     * trimming whitespace.
     *
     * @var string|null
     */
    protected $_charList = null;

    /**
     * Constructor
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct(ObjectConfig $config)
    {
        parent::__construct($config);

        // List of user-defined tags
        if(isset($config->char_list)) {
            $this->_charList = $config->char_list;
        }
    }

    /**
     * Returns the charList option
     *
     * @return string|null
     */
    public function getCharList()
    {
        return $this->_charList;
    }

    /**
     * Sets the charList option
     *
     * @param  string|null $charList
     * @return FilterTrim
     */
    public function setCharList($charList)
    {
        $this->_charList = $charList;
        return $this;
    }

    /**
     * Validate a value
     *
     * @param   mixed  $value Value to be validated
     * @return  bool    True when the variable is valid
     */
    public function validate($value)
    {
        return (is_string($value));
    }

    /**
     * Sanitize a value
     *
     * Returns the variable with characters stripped from the beginning and end
     *
     * @param   mixed   $value Value to be sanitized
     * @return  string
     */
    public function sanitize($value)
    {
        if (null === $this->_charList) {
            return trim((string) $value);
        } else {
            return trim((string) $value, $this->_charList);
        }
    }
}
