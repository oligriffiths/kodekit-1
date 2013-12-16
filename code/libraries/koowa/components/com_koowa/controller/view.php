<?php
/**
 * Koowa Framework - http://developer.joomlatools.com/koowa
 *
 * @copyright	Copyright (C) 2007 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		http://github.com/joomlatools/koowa for the canonical source repository
 */


/**
 * Resource Controller
 *
 * @author  Johan Janssens <https://github.com/johanjanssens>
 * @package Koowa\Component\Koowa
 */
class ComKoowaControllerView extends KControllerView
{
    /**
     * Constructor
     *
     * @param   KObjectConfig $config Configuration options
     */
    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->getObject('translator')->loadTranslations($this->getIdentifier());

        // Mixin the toolbar interface
        $this->mixin('koowa:controller.toolbar.mixin');

        //Attach the toolbars
        $this->registerCallback('before.render' , array($this, 'attachToolbars'), array($config->toolbars));
    }

    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param  KObjectConfig $config An optional ObjectConfig object with configuration options.
     * @return void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $toolbars = array();
        if($config->dispatched && !JFactory::getUser()->guest)
        {
            $toolbars[] = $this->getIdentifier()->name;

            if($this->getIdentifier()->application === 'admin') {
                $toolbars[] = 'menubar';
            }
        }

        $config->append(array(
            'toolbars'  => $toolbars,
            'user'      => 'com:koowa.controller.user.joomla',
        ));

        parent::_initialize($config);
    }

    /**
     * Attach the toolbars to the controller
     *
     * @param array $toolbars A list of toolbars
     * @return ComKoowaControllerView
     */
    public function attachToolbars($toolbars)
    {
        if($this->getView() instanceof KViewHtml)
        {
            foreach($toolbars as $toolbar) {
                $this->attachToolbar($toolbar);
            }

            if($toolbars = $this->getToolbars())
            {
                $this->getView()
                    ->getTemplate()
                    ->attachFilter('toolbar', array('toolbars' => $toolbars));
            };
        }

        return $this;
    }

    /**
     * Display action
     *
     * If the controller was not dispatched manually load the languages files
     *
     * @param KControllerContextInterface $context A command context object
     * @return    string|bool    The rendered output of the view or false if something went wrong
     */
    protected function _actionRender(KControllerContextInterface $context)
    {
        $this->getObject('translator')->loadTranslations($this->getIdentifier());
        return parent::_actionRender($context);
    }
}
