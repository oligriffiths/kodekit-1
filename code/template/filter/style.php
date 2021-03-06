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
 * Style Template Filter
 *
 * Filter to parse style tags
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Kodekit\Library\Template\Filter
 */
class TemplateFilterStyle extends TemplateFilterTag
{
    /**
     * Parse the text for style tags
     *
     * @param string $text  The text to parse
     * @return  string
     */
    protected function _parseTags(&$text)
    {
        $tags = '';

        $matches = array();
        if(preg_match_all('#<ktml:style\s+src="([^"]+)"(.*)\/>#siU', $text, $matches))
        {
            foreach(array_unique($matches[1]) as $key => $match)
            {
                //Set required attributes
                $attribs = array(
                    'src' => $match
                );

                $attribs = array_merge($this->parseAttributes( $matches[2][$key]), $attribs);
                $tags .= $this->_renderTag($attribs);
            }

            $text = str_replace($matches[0], '', $text);
        }

        $matches = array();
        if(preg_match_all('#<style(.*)>(.*)<\/style>#siU', $text, $matches))
        {
            foreach($matches[2] as $key => $match)
            {
                $attribs = $this->parseAttributes( $matches[1][$key]);
                $tags .= $this->_renderTag($attribs, $match);
            }

            $text = str_replace($matches[0], '', $text);
        }

        return $tags;
    }

    /**
     * Render the tag
     *
     * @param   array   $attribs Associative array of attributes
     * @param   string  $content The tag content
     * @return string
     */
    protected function _renderTag($attribs = array(), $content = null)
    {
        $link = isset($attribs['src']) ? $attribs['src'] : false;
        $condition = isset($attribs['condition']) ? $attribs['condition'] : false;

        if(!$link)
        {
            $attribs = $this->buildAttributes($attribs);

            $html  = '<style type="text/css" '.$attribs.'>'."\n";
            $html .= trim($content);
            $html .= '</style>'."\n";
        }
        else
        {
            unset($attribs['src']);
            unset($attribs['condition']);
            $attribs = $this->buildAttributes($attribs);

            if($condition)
            {
                $html  = '<!--['.$condition.']>';
                $html .= '<link type="text/css" rel="stylesheet" href="'.$link.'" '.$attribs.' />'."\n";
                $html .= '<![endif]-->';
            }
            else $html  = '<link type="text/css" rel="stylesheet" href="'.$link.'" '.$attribs.' />'."\n";
        }

        return $html;
    }
}
