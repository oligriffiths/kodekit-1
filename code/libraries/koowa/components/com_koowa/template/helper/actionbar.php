<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */

/**
 * Action bar Template Helper
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaTemplateHelperActionbar extends KTemplateHelperAbstract
{
    /**
     * Render the action bar commands
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function render($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
        	'toolbar' => null,
            'title'   => null,
        ))->append(array(
            'icon' => $config->toolbar->getName()
        ));

        //Set a custom title
        if ($config->title === 'false' || $config->title === false) {
            $config->toolbar->removeCommand('title');
        }
        elseif($config->title || $config->icon)
        {
            if($config->toolbar->hasCommand('title'))
            {
                $command = $config->toolbar->getCommand('title');

                if ($config->title) {
                    $command->set('title', $config->title);
                }

                if ($config->icon) {
                    $command->set('icon', $config->icon);
                }
            }
            else $config->toolbar->addTitle($config->title, $config->icon);
        }

        // Load the language strings for toolbar button labels
        JFactory::getLanguage()->load('joomla', JPATH_ADMINISTRATOR);

        //Render the buttons
        if ($this->_useBootstrap())
        {
        	$html = '<div class="btn-toolbar koowa-toolbar" id="toolbar-'.$config->toolbar->getName().'">';
        	$html .= '%s';
		    $html .= '</div>';
        }
        else
        {
		    $html  = '<div class="toolbar-list koowa-toolbar" id="toolbar-'.$config->toolbar->getName().'">';
		    $html .= '<ul>';
		    $html .= '%s';
		    $html .= '</ul>';
		    $html .= '<div class="clr"></div>';
		    $html .= '</div>';
        }

        $buttons = '';
        foreach ($config->toolbar->getCommands() as $command)
        {
            $name = $command->getName();

            if(method_exists($this, $name)) {
                $buttons .= $this->$name(array('command' => $command));
            } else {
                $buttons .= $this->command(array('command' => $command));
            }
        }

       	$html = sprintf($html, $buttons);

		return $html;
    }

    /**
     * Render a action bar command
     *
     * @param   array|KObjectConfig   $config An optional array with configuration options
     * @return  string  Html
     */
    public function command($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
        	'command' => NULL
        ));

        $command = $config->command;

        if ($command->allowed === false)
        {
            $command->attribs->title = $this->translate('You are not allowed to perform this action');
            $command->attribs->class->append(array('disabled', 'unauthorized'));
        }

         //Add a toolbar class	
        $command->attribs->class->append(array('toolbar'));

        //Create the href
        $command->attribs->append(array('href' => '#'));
        if(!empty($command->href))
        {
            $href = $command->href;

            if (substr($href, 0, 1) === '&') {
                $href = $this->getTemplate()->getView()->getRoute($href);
            }

            $command->attribs['href'] = $href;
        }

        //Create the id
        $id = 'toolbar-'.$command->id;

        if ($this->_useBootstrap())
        {
        	$command->attribs->class->append(array('btn', 'btn-small'));
			
        	$icon = $this->_getIconClass($command->icon);
        	if ($command->id === 'new' || $command->id === 'apply') {
        		$command->attribs->class->append(array('btn-success'));
        		$icon .= ' icon-white';
        	}

            $attribs = clone $command->attribs;
            $attribs->class = implode(" ", KObjectConfig::unbox($attribs->class));

        	$html = '<div class="btn-group" id="'.$id.'">';
        	$html .= '<a '.$this->buildAttributes($attribs).'>';

            if ($this->_useIcons()) {
                $html .= '<i class="'.$icon.'"></i> ';
            }

        	$html .= $this->translate($command->label);
        	$html .= '</a>';
        	$html .= '</div>';
        }
        else
        {
            $attribs = clone $command->attribs;
            $attribs->class = implode(" ", KObjectConfig::unbox($attribs->class));

            $html = '<li class="button" id="'.$id.'">';

            $html .= '<a '.$this->buildAttributes($attribs).'>';
            $html .= '<span class="'.$command->icon.'" title="'.$this->translate($command->title).'"></span>';
            $html .= $this->translate($command->label);
            $html .= '</a>';

            $html .= '</li>';
        }

    	return $html;
    }

    /**
     * Render the action bar title
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function title($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
            'command' => NULL,
        ));

        $title = $this->translate($config->command->title);
        $icon  = $config->command->icon;
        $html  = '';

        if (!empty($title))
        {
            if (version_compare(JVERSION, '3.2', 'ge'))
            {

                $layout = new JLayoutFile('joomla.toolbar.title');
                $html = $layout->render(array('title' => $title, 'icon' => $icon));
            }
            elseif ($this->_useBootstrap())
            {
                // Strip the extension.
                $icons = explode(' ', $icon);
                foreach ($icons as &$icon) {
                    $icon = 'pagetitle--' . preg_replace('#\.[^.]*$#', '', $icon);
                }

                $html = '<div class="pagetitle ' . htmlspecialchars(implode(' ', $icons)) . '"><h2>' . $title . '</h2></div>';
            }
            else
            {
                $html = '<div class="header pagetitle icon-48-'.$icon.'">';
                $html .= '<h2>'.$title.'</h2>';
                $html .= '</div>';
            }

            if (JFactory::getApplication()->isAdmin())
            {
                $app = JFactory::getApplication();
                $app->JComponentTitle = $html;

                $html = '';

                JFactory::getDocument()->setTitle($app->getCfg('sitename') . ' - ' . JText::_('JADMINISTRATION') . ' - ' . $config->title);
            }
        }

        return $html;
    }

	/**
     * Render a separator
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function separator($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
        	'command' => NULL
        ));
        
    	if ($this->_useBootstrap()) {
            $html = '<div class="btn-group"></div>';
        } else {
            $html = '<li class="divider"></li>';
        }

    	return $html;
    }

	/**
     * Render a modal button
     *
     * @param   array   $config An optional array with configuration options
     * @return  string  Html
     */
    public function modal($config = array())
    {
        $config = new KObjectConfigJson($config);
        $config->append(array(
        	'command' => NULL
        ));

        JHtml::_('behavior.modal');

        $html = $this->command($config);

    	return $html;
    }

    /**
     * Render an options button
     *
     * @param array|KObjectConfig $config
     * @return string
     */
    public function options($config = array())
    {
        return $this->modal($config);
    }

    /**
     * Decides if the renderers should use Bootstrap markup or not
     *
     * @return bool
     */
    protected function _useBootstrap()
    {
        return version_compare(JVERSION, '3.0', '>=') || JFactory::getApplication()->isSite();
    }

    /**
     * Decides if Bootstrap buttons should use icons
     *
     * @return bool
     */
    protected function _useIcons()
    {
        return JFactory::getApplication()->isAdmin();
    }

    /**
     * Converts Joomla 3.0+ custom icons back to Glyphicons ones used in Joomla 2.5
     *
     * @param  string $icon Action bar icon
     * @return string Icon class
     */
    protected function _getIconClass($icon)
    {
        static $map = array(
            'icon-save'   => 'icon-ok',
            'icon-cancel' => 'icon-remove-sign',
            'icon-apply'  => 'icon-edit'
        );

        if (version_compare(JVERSION, '3.0', '>=') || $this->getIdentifier()->domain == 'site') {
            $icon = str_replace('icon-32-', 'icon-', $icon);
        }

        if (version_compare(JVERSION, '3.0', '<') && array_key_exists($icon, $map)) {
            $icon = $map[$icon];
        }

        return $icon;
    }
}
