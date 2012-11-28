<?php
    /**
     * [FormNew.class.php]
     * @name FormNew.class.php
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
     * @todo Bootstrap: Klassen jederzeit nachladen lassen
     * @todo JavaScript-Überprüfung
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
    class FormNew {
        private $Bootstrap;
        private $Template;
        private $form;
        private $allowedInputs = array(
            'text',
            'hidden',
            'checkbox',
            'radio',
            'submit'
            );
        private $formId = null;

        public function __construct(Bootstrap $Bootstrap) {
            $this->Bootstrap = $Bootstrap;
            if (!is_object($this->Bootstrap->getApplication('Template'))) {
                throw new Mexception('I need the class template');
            } else {
                $this->Template = $this->Bootstrap->getApplication('Template');
            }
            $this->Session = $this->Bootstrap->getApplication('Session');
        }

        public function openForm($identifier = null, $action = 'index.php', $method = 'POST') {
            if ($identifier == null) {
                $identifier = rand(1000, 5000);
            }
            $identifier = substr(sha1($identifier), 0, 6);
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
            $this->form[$identifier]['header'] = $this->Template->parseSimple('form/formHeader.tpl', $vars);
            $this->form[$identifier]['footer'] = $this->Template->parseSimple('form/formFooter.tpl');

            return $identifier;
        }

        public function setFormId($formId) {
            if (isset($this->form[$formId])) {
                $this->formId = $formId;
                return true;
            } else {
                return false;
            }
        }

        public function getFormId($formId) {
            return $this->formId;
        }

        public function getElement($elementId, $formId = null) {
            if ($formId == null) {
                $formId = $this->formId;
            } else {
                $formId = substr(sha1($formId), 0, 6);
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
                return $this->form[$formId]['fields'][$elementId];
            } else {
                return null;
            }
        }

        public function getLabel($elementId, $formId = null) {
            if ($formId == null) {
                $formId = $this->formId;
            } else {
                $formId = substr(sha1($formId), 0, 6);
            }
            if (isset($this->form[$formId]['labels'][$elementId])) {
                return $this->form[$formId]['labels'][$elementId];
            } else {
                return null;
            }
        }

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
                                $return['wrong'][] = $name;
                            } else {
                                $return['ok'][] = $name;
                            }
                        } else {
                            $return['ok'][] = $name;
                        }
                    }
                }
                return $return;
            }
        }

        public function storeForm() {
            if (!is_object($this->Session)) {
                throw new Mexception('Unable to store Session');
            } else {
                foreach ($this->form[$this->formId]['fields'] AS $name => $value) {
                    $this->Session->setValue($this->formId.'->'.$name, $_POST[$name]);
                }
            }
        }

        // Get all fields of the form
        public function getFormValues() {
            if (is_object($this->Session)) {
                $return = $this->Session->getValue($this->formId);
            } else {
                $return = null;
            }
            if ($return === null) {
                $return = array();
                if ($this->form['method'] == 'POST') {
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

        public function preFill() {
            $replace = '';
            foreach ($this->form[$this->formId]['fields'] AS $name => $value) {
                if (strlen(trim($_POST[$name])) > 0) {
                    $replace = 'value="'. htmlspecialchars(trim($_POST[$name])).'"';
                    $value = preg_replace('/value\=\"(.+?)\"/', $replace, $value);
                    $this->form[$this->formId]['fields'][$name] = $value;
                }
            }
            return true;
        }

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
                if (isset($_POST[$name]) AND strlen($value) == 0) {
                    $value = $_POST[$name];
                }

                $vars = array(
                    'type' => $type,
                    'name' => $name,
                    'value' => htmlspecialchars($value),
                    'checked' => $checked,
                    'disabled' => $disabled,
                    'readonly' => $readonly,
                    'maxlength' => $maxlength
                    );

                $return = $this->Template->parseSimple('form/input.tpl', $vars);
                $this->form[$this->formId]['fields'][$name] = $return;

                return true;
            } else {
                return false;
            }
        }

        public function addLabel($name, $label) {
            if (isset($this->form[$this->formId]['fields'][$name])) {
                $vars = array(
                    'for' => $name,
                    'value' => $label
                    );

                $return = $this->Template->parseSimple('form/label.tpl', $vars);
                $this->form[$this->formId]['labels'][$name] = $return;
                return true;
            } else {
                return false;
            }
        }

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

        public function __desctruct() {

        }

    }

?>
