<?php

    /**
     * [Template.class.php]
     * @name Template.class.php
     * @version 1.0.0
     * @revision 01
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
            // Initialize Loader & Set Options
            $loader = new \Twig_Loader_Filesystem(\Lib\Path\Path::buildPath(\Config\Template::DIR));
            $options = array();
            if (\Config\Template::USE_CACHE) {
                $options['cache'] = \Lib\Path\Path::buildPath(\Config\Template::DIR_CACHE);
            }
            parent::__construct($loader, $options);

            // Do other work
            $this->loadFunctions();

            return ;
        }

        /**
         * Loads defined functions
         * @return boolean
         */
        protected function loadFunctions() {
            $json = file_get_contents(\Lib\Path\Path::buildPath('lib/template/functions.json'));
            if (!$json) return false;
            $list = json_decode($json);
            if (!is_array($list)) return false;
            foreach ($list AS $item) {
                if ($item->default == true) {
                    $namespace = '\Lib\Template\Extensions\\' . $item->name;
                    $this->addFunction(new \Twig_SimpleFunction($item->templateFunction,
                        array($namespace, $item->classFunction)));
                }
            }
            return true;
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
