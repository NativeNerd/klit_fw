<?php

    /**
     * [Template.class.php]
     * @name Template.class.php
     * @version 1.0.0
     * @revision 00
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     */
    namespace Lib\Template;
    class Template extends \Twig_Environment {
        protected $actualTemplate;

        public function __construct() {
            $loader = new \Twig_Loader_Filesystem(\Lib\Path\Path::buildPath(\Config\Template::DIR));
            $options = array();
            if (\Config\Template::USE_CACHE) $options['cache'] = \Lib\Path\Path::buildPath(\Config\Template::DIR_CACHE);
            parent::__construct($loader, $options);
        }

        public function loadTemplate($name, $index = null) {
            $this->actualTemplate = $name;
            return parent::loadTemplate($name, $index);
        }

        public function display() {
            return parent::display($this->actualTemplate);
        }
    }
?>
