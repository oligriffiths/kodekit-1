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
 * Form Template Filter
 *
 * Filter to handle form html elements
 *
 * For forms that use a post method this filter adds a token to prevent CSRF. For forms that use a get method this
 * filter adds the action url query params as hidden fields to comply with the html form standard.
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 * @see         http://www.w3.org/TR/html401/interact/forms.html#h-17.13.3.4
 */
class TemplateFilterForm extends TemplateFilterAbstract
{
    /**
     * The form token value
     *
     * @var string
     */
    protected $_token_value;

    /**
     * The form token name
     *
     * @var string
     */
    protected $_token_name;

    /**
     * Constructor.
     *
     * @param   ObjectConfig $config Configuration options
     */
    public function __construct( ObjectConfig $config = null)
    {
        parent::__construct($config);

        $this->_token_value = $this->getObject('user')->getSession()->getToken();
        $this->_token_name  = $config->token_name;
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   ObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(ObjectConfig $config)
    {
        $config->append(array(
            'token_value'   => '',
            'token_name'    => 'csrf_token',
        ));

        parent::_initialize($config);
    }

    /**
     * Handle form replacements
     *
     * @param string $text  The text to parse
     * @param TemplateInterface $template A template object
     * @return void
     */
    public function filter(&$text, TemplateInterface $template)
    {
        $this->_addAction($text, $template);
        $this->_addToken($text);
        $this->_addQueryParameters($text);
    }

    /**
     * Add the action if left empty
     *
     * @param string $text       Template text
     * @param TemplateInterface $template A template object
     * @return TemplateFilterForm
     */
    protected function _addAction(&$text, TemplateInterface $template)
    {
        // All: Add the action if left empty
        if (preg_match_all('#<\s*form.*?action=""#sim', $text, $matches, PREG_SET_ORDER))
        {
            foreach ($matches as $match)
            {
                $str  = str_replace('action=""', 'action="' . $template->route() . '"', $match[0]);
                $text = str_replace($match[0], $str, $text);
            }
        }

        return $this;
    }

    /**
     * Add the token to the form
     *
     * @param string $text Template text
     * @return TemplateFilterForm
     */
    protected function _addToken(&$text)
    {
        if (!empty($this->_token_value))
        {
            // POST: Add token
            $text    = preg_replace('/(<form.*method="post"[^>]*>)/si',
                '\1'.PHP_EOL.'<input type="hidden" name="'.$this->_token_name.'" value="'.$this->_token_value.'" />',
                $text
            );

            // GET: Add token to .-koowa-grid forms
            $text    = preg_replace('#(<\s*?form\s+?.*?class=(?:\'|")[^\'"]*?-koowa-grid.*?(?:\'|").*?)>#sim',
                '\1 data-token-name="'.$this->_token_name.'" data-token-value="'.$this->_token_value.'">',
                $text
            );
        }

        return $this;
    }

    /**
     * Add query parameters as hidden fields to the GET forms
     *
     * @param string $text Template text
     * @return TemplateFilterForm
     */
    protected function _addQueryParameters(&$text)
    {
        $matches = array();
        if (preg_match_all('#<form.*action="[^"]*\?(.*)".*method="get".*>(.*)</form>#siU', $text, $matches))
        {
            foreach ($matches[1] as $key => $query)
            {
                parse_str(str_replace('&amp;', '&', $query), $query);

                $input = '';
                foreach ($query as $name => $value)
                {
                    if (is_array($value)) {
                        $name = $name . '[]';
                    }

                    if (strpos($matches[2][$key], 'name="' . $name . '"') !== false) {
                        continue;
                    }

                    $name =  StringEscaper::attr($name);

                    if (is_array($value))
                    {
                        foreach ($value as $k => $v)
                        {
                            if (!is_scalar($v) || !is_numeric($k)) {
                                continue;
                            }

                            $v = StringEscaper::attr($v);

                            $input .= PHP_EOL.'<input type="hidden" name="'.$name.'" value="'.$v.'" />';
                        }
                    }
                    else
                    {
                        $value  = StringEscaper::attr($value);
                        $input .= PHP_EOL.'<input type="hidden" name="'.$name.'" value="'.$value.'" />';
                    }
                }

                $text = str_replace($matches[2][$key], $input.$matches[2][$key], $text);
            }
        }

        return $this;
    }
}
