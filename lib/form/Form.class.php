<?php
    namespace Lib\Form;
    /**
     * [Form.class.php]
     * @name Form.class.php
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Was it does
     *
     * previous     now     what changed
     *              1.0.0   -
     *
     * @todo JavaScript-Überprüfung
     * @todo filter_var()
     *
     * Nutzung der Klasse
     * - Jede Klasse hat eine "formId"
     * - Idee ist es, jedem Teil des Formulares die gleiche formId zu geben
     * - So können immer neue Felder erstellt werden und gespeichert werden
     * - Und am Schluss können alle Werte mit getFormValues() abgerufen werden
     * - Eine Form kann man mit parseForm() überprüfen
     *  => Es wird geprüft
     *      - Ob jedes Feld gesendet wurde
     *      - Ob jedes Feld korrekte eingaben enthält
     */
    class Form implements \Core\Implement\lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;
        protected $Template;
        protected $form;
        protected $allowedInputs = array(
            'text',
            'hidden',
            'checkbox',
            'radio',
            'submit',
            'password'
            );
        protected $formId = null;
        protected $formName = null;
        protected $parsed = null;

        /**
         * Initialisiert die Klasse
         * @return
         */
        public function __construct() {
            $this->Template = \Lib\Template\Template::getInstance();
            $this->Session = \Lib\Session\Session::getInstance();
            return ;
        }

        /**
         * Holt eine neue Instanz der Klasse
         * @param \Core\Bootstrap $Bootstrap
         * @return instance Form
         */
        public static function getInstance(\Core\Bootstrap $Bootstrap = null) {
            if ($Bootstrap !== null) {
                static::$Bootstrap = $Bootstrap;
            }
            if (static::$_instance === null) {
                static::$_instance = new static();
            }
            return static::$_instance;
        }

        /**
         * Opens a new form
         * @param string $identifier
         * @param string $action
         * @param string $method
         * @return boolean
         */
        public function openForm($identifier = null, $action = 'index.php', $method = 'POST') {
            if ($identifier == null) {
                do {
                    $identifier = rand(1000, 5000);
                } while(isset($this->form[$identifier]));
            } elseif (isset($this->form[$identifier])) {
                return false;
            }
            $this->formName = $identifier;
            $identifier = $this->buildFormId($identifier);
            if ($method == 'POST') {
                $this->form[$identifier]['method'] = 'POST';
            } else {
                $this->form[$identifier]['method'] = 'GET';
            }
            $this->form[$identifier]['action'] = $action;
            $this->formId = $identifier;

            $vars = array(
                'method' => $this->form[$identifier]['method'],
                'action' => $this->form[$identifier]['action']
                );
            $this->form[$identifier]['header'] = $this->Template->parseSimple('lib/form/src/tpl/formHeader.tpl', $vars);
            $this->form[$identifier]['footer'] = $this->Template->parseSimple('lib/form/src/tpl/formFooter.tpl');

            return $identifier;
        }

        /**
         * Sets the actual Form ID
         * @param strng $formId
         * @return boolean
         */
        public function setFormId($formId) {
            if (isset($this->form[$formId])) {
                $this->formId = $formId;
                return true;
            } else {
                return false;
            }
        }

        /**
         * Gets the actual Form ID
         * @return string
         */
        public function getFormId() {
            return $this->formId;
        }

        /**
         * Builds a sha1-String out of a string
         * @param string $formName
         * @return string
         */
        public function buildFormId($formName) {
            return substr(sha1($formName), 0, 6);
        }

        /**
         * Gets the HTML-Code of an element
         * @param string $elementId
         * @param string $formId
         * @return string
         */
        public function getElement($elementId, $formId = null) {
            if ($formId == null) {
                $formId = $this->formId;
            } else {
                $formId = $this->buildFormId($formId);
            }
            if (substr($elementId, 0, 1) == ':') {
                if (substr($elementId, 1) == 'header') {
                    return $this->form[$formId]['header'];
                }
                if (substr($elementId, 1) == 'footer') {
                    return $this->form[$formId]['footer'];
                }
            }
            if (isset($this->form[$formId]['fields'][$elementId])) {
                if (isset($this->form[$formId]['fields'][$elementId]['body'])) {
                    return $this->getSelect($elementId, $formId);
                } else {
                    return $this->getInput($elementId, $formId);
                }
            } else {
                return null;
            }
        }

        /**
         * Gets the HTML of an label
         * @param string $elementId
         * @param string $formId
         * @return string
         */
        public function getLabel($elementId, $formId = null) {
            if ($formId == null) {
                $formId = $this->formId;
            } else {
                $formId = $this->buildFormId($formId);
            }
            if (isset($this->form[$formId]['labels'][$elementId])) {
                return $this->form[$formId]['labels'][$elementId];
            } else {
                return null;
            }
        }

        /**
         * Parses an Select element into HTML
         * @param string $elementId
         * @param string $formId
         * @return boolean
         */
        protected function getSelect($elementId, $formId) {
            if (isset($this->form[$formId]['fields'][$elementId]['body'])) {
                $headerVars = $this->form[$formId]['fields'][$elementId]['header'];
                $footerVars = $this->form[$formId]['fields'][$elementId]['footer'];
                $header = $this->Template->parseSimple('lib/form/src/tpl/selectHeader.tpl', $headerVars);
                $footer = $this->Template->parseSimple('lib/form/src/tpl/selectFooter.tpl', $footerVars);
                $body = '';
                foreach ($this->form[$formId]['fields'][$elementId]['body'] AS $value) {
                    $body .= $this->Template->parseSimple('lib/form/src/tpl/selectBody.tpl', $value);
                }
                return $header . $body . $footer;
            }
            return false;
        }

        /**
         * Parses an Input element into HTML
         * @param string $elementId
         * @param string $formId
         * @return boolean
         */
        protected function getInput($elementId, $formId) {
            if (isset($this->form[$formId]['fields'][$elementId]['vars'])) {
                $vars = $this->form[$formId]['fields'][$elementId]['vars'];
                $body = $this->Template->parseSimple('lib/form/src/tpl/input.tpl', $vars);
                return $body;
            }
            return false;
        }

        /**
         * Gets the value of an element
         * @param string $element
         * @return mixed
         */
        public function getValue($element) {
            if ($this->parsed === null) {
                $this->parseForm();
            }
            if ($this->form[$this->formId]['method'] == 'POST') {
                $req = $_POST;
            } else {
                $req = $_GET;
            }
            if (isset($this->form[$this->formId]['fields'][$element])) {
                return $req[$element];
            }
            return null;
        }

        /**
         * Parses a form and returns goodness of sent fields
         * @return array
         */
        public function parseForm() {
            if (count($_POST) == 0 AND count($_GET) == 0) {
                return false;
            } else {
                if ($this->form[$this->formId]['method'] == 'POST') {
                    $req = $_POST;
                } else {
                    $req = $_GET;
                }
                $return = array();
                foreach ($this->form[$this->formId]['fields'] AS $name => $value) {
                    if (empty($_POST[$name]) OR strlen($_POST[$name]) == 0) {
                        $return['empty'][] = $name;
                    } else {
                        if (isset($this->form[$this->formId]['regex'][$name])) {
                            if (!preg_match($this->form[$this->formId]['regex'][$name], $_POST[$name])) {
                                $return['false'][] = $name;
                            } else {
                                $return['true'][] = $name;
                            }
                        } else {
                            $return['unchecked'][] = $name;
                        }
                    }
                }
                $this->parsed = $return;
                return $return;
            }
        }

        /**
         * Stores form into session
         * @return boolean
         * @throws Mexception
         */
        public function storeForm() {
            if (!is_object($this->Session)) {
                throw new \Core\Mexception('Unable to store Session');
            } else {
                foreach ($this->form[$this->formId]['fields'] AS $name => $value) {
                    $this->Session->setValue($this->formId.'->'.$name, $_POST[$name]);
                }
            }
            return true;
        }

        /**
         * Gets the values of the form as an array
         * @return array
         */
        public function getFormValues() {
            if (is_object($this->Session)) {
                $return = $this->Session->getValue($this->formId);
            } else {
                $return = null;
            }
            if ($return === null) {
                $return = array();
                if ($this->form[$this->formId]['method'] == 'POST') {
                    $req = $_POST;
                } else {
                    $req = $_GET;
                }
                foreach ($this->form[$this->formId]['fields'] AS $name => $value) {
                    $return[$name] = $_POST[$name];
                }
            }
            return $return;
        }

        /**
         * Pre Fills a form
         * @return boolean
         */
        public function preFill() {
            if ($this->form[$this->formId]['method'] == 'POST') {
                $req = $_POST;
            } else {
                $req = $_GET;
            }
            foreach ($this->form[$this->formId]['fields'] AS $name => $value) {
                if (!isset($_POST[$name]))
                    continue;
                if (strlen(trim($_POST[$name])) == 0)
                    continue;
                if (!isset($value['vars']['type'])) {
                    $this->form[$this->formId]['fields'][$name]['body'][$_POST[$name]]['selected'] = 'selected="selected"';
                } elseif ($value['vars']['type'] == 'submit') {
                    continue;
                } elseif ($value['vars']['type'] == 'text') {
                    $this->form[$this->formId]['fields'][$name]['vars']['value'] = htmlspecialchars(trim($req[$name]));
                }
            }
            return true;
        }

        /**
         * Adds an Input to the form
         * @param string $type
         * @param string $name
         * @param string $value
         * @param boolean $checked
         * @param boolean $disabled
         * @param boolean $readonly
         * @param boolean $maxlength
         * @return boolean
         */
        public function addInput($type, $name, $value = '', $checked = false, $disabled = false, $readonly = false, $maxlength = false) {
            if (in_array($type, $this->allowedInputs)) {
                if ($checked == false) {
                    $checked = null;
                } else {
                    $checked = 'checked="checked"';
                }
                if ($readonly == false) {
                    $disabled = null;
                } else {
                    $disabled = 'disabled="disabled"';
                }
                if ($readonly == false) {
                    $readonly = null;
                } else {
                    $readonly = 'readonly="readonly"';
                }
                if (!is_int($maxlength)) {
                    $maxlength = null;
                } else {
                    $maxlength = $maxlength;
                }
                if (isset($_POST[$name]) AND strlen($value) == 0 AND $type !== 'password') {
                    $value = $_POST[$name];
                }
                $id = $this->formName . '_' . $name;

                $vars = array(
                    'id' => $id,
                    'type' => $type,
                    'name' => $name,
                    'value' => htmlspecialchars($value),
                    'checked' => $checked,
                    'disabled' => $disabled,
                    'readonly' => $readonly,
                    'maxlength' => $maxlength
                    );
                $this->form[$this->formId]['fields'][$name]['vars'] = $vars;
                return true;
            } else {
                return false;
            }
        }

        /**
         * Adds a select
         * @param string $name
         * @param int $size
         * @param boolean $multiple
         * @param boolean $disabled
         * @return boolean
         */
        public function addSelect($name, $size = 1, $multiple = false, $disabled = false) {
            $this->form[$this->formId]['fields'][$name] = array();
            if ($multiple == false) {
                $multiple = null;
            } else {
                $multiple = 'multiple="multiple"';
            }
            if ($disabled == false) {
                $disabled = null;
            } else {
                $disabled = 'disabled="disabled"';
            }
            if ($size != 1) {
                $size = (int)$size;
            } else {
                $size = null;
            }

            $vars = array(
                'multiple' => $multiple,
                'disabled' => $disabled,
                'size' => $size,
                'name' => $name
                );
            $this->form[$this->formId]['fields'][$name]['header'] = $vars;
            $this->form[$this->formId]['fields'][$name]['footer'] = $vars;
            return true;
        }

        /**
         * Adds an option onto a select
         * @param string $selectName
         * @param string $name
         * @param string $value
         * @param boolean $label
         * @param boolean $selected
         * @param boolean $disabled
         * @return boolean
         */
        public function addOption($selectName, $name, $value, $label = false, $selected = false, $disabled = false) {
            if (isset($this->form[$this->formId]['fields'][$selectName])) {
                if ($selected == false) {
                    $selected = null;
                } else {
                    $selected = 'selected="selected"';
                }
                if ($disabled == false) {
                    $disabled = null;
                } else {
                    $disabled = 'disabled="disabled"';
                }
                if ($label == false) {
                    $label = null;
                } else {
                    $label = 'label="'.htmlspecialchars($label).'"';
                }
                if (isset($_POST[$selectName]) AND strlen($value) == 0) {
                    if ($_POST[$selectName] == $name) {
                        $selected = 'selected="selected"';
                    }
                }

                $id = $this->formName . '_' . $selectName . '_' . $name;

                $vars = array(
                    'id' => $id,
                    'selected' => $selected,
                    'disabled' => $disabled,
                    'label' => $label,
                    'value' => $value,
                    'name' => $name,
                    'value' => $value
                    );
                $this->form[$this->formId]['fields'][$selectName]['body'][$name] = $vars;
                return true;
            } else {
                return false;
            }
        }

        /**
         * Closes the select
         * @deprecated
         * @param string $selectName
         * @return boolean
         */
        public function closeSelect($selectName) {
            return ;
            if (!isset($this->form[$this->formId]['fields'][$selectName])) {
                return false;
            }
            $return = '';
            $return .= $this->form[$this->formId]['fields'][$selectName]['header'];
            foreach ($this->form[$this->formId]['fields'][$selectName]['body'] AS $value) {
                $return .= $value;
            }
            $return .= $this->form[$this->formId]['fields'][$selectName]['footer'];
            $this->form[$this->formId]['fields'][$selectName] = $return;
            return true;
        }

        /**
         * Adds a label
         * @param string $name
         * @param string $label
         * @return boolean
         */
        public function addLabel($name, $label) {
            if (isset($this->form[$this->formId]['fields'][$name])) {
                $vars = array(
                    'for' => $name,
                    'value' => $label
                    );

                $return = $this->Template->parseSimple('lib/form/src/tpl/label.tpl', $vars);
                $this->form[$this->formId]['labels'][$name] = $return;
                return true;
            } else {
                return false;
            }
        }

        /**
         * Adds a regex onto an element (for parsing)
         * @param string $name
         * @param regex $regex
         * @return boolean
         */
        public function addRegex($name, $regex) {
            if (isset($this->form[$this->formId]['fields'][$name])) {
                if (preg_match($regex, ' ') === false) {
                    return false;
                } else {
                    $this->form[$this->formId]['regex'][$name] = $regex;
                    return true;
                }
            } else {
                return false;
            }
        }

        /**
         * Closes the class
         */
        public function __destruct() {

        }

    }

?>
