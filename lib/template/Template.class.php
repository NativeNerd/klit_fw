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
        protected $variableList = array();

        /**
         * Loads twig
         */
        public function __construct() {
            $loader = new \Twig_Loader_Filesystem(\Lib\Path\Path::buildPath(\Config\Template::DIR));
            $options = array();
            if (\Config\Template::USE_CACHE) $options['cache'] = \Lib\Path\Path::buildPath(\Config\Template::DIR_CACHE);
            parent::__construct($loader, $options);
        }

        /**
         * Assigns a variable to template
         * @param mixed $variable
         * @param mixed $value
         * @return boolean
         */
        public function assign($variable, $value) {
            $this->variableList[$variable] = $value;
            return true;
        }

        /**
         * Loads a template
         * @param string $name
         * @param string $index
         * @return boolean
         */
        public function loadTemplate($name, $index = null) {
            $this->actualTemplate = $name;
            return parent::loadTemplate($name, $index);
        }

        /**
         * Render and show template
         * @return boolean
         */
        public function display() {
            echo $this->render($this->actualTemplate, $this->variableList);
            return true;
        }
    }
?>
