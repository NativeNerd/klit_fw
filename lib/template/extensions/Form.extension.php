<?php
    namespace Lib\Template\Extensions;
    /**
     * [File.php]
     * @name File.php
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     */
    class Form extends \Twig_Extension {
        public function getName() {
            return 'form';
        }

        public function getFunctions() {
            return array(
                'field' => array($this, 'getField'),
                'label' => array($this, 'getLabel')
            );
        }

        public function getField($param) {
            var_dump($param);
        }

        public function getLabel($param) {

        }
    }
?>
