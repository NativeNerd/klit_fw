<?php
    /**
     * [Template.class.php]
     * @version 1.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * @desc Handles Templates
     *
     * previous     now     what changed
     *              1.0.0   -
     *              1.0.0   fixed a bug with arrays in parseForeach()
     *
     * @todo Scopes der Variablen verbessern (declared sollen überall verfügbar sein)
     *  => Zieht ein line-by-line-parsing nach sich
     * @todo Bootstrap: Nachladen jederzeit ermöglichen
     *
     * Scope of variables:
     *  $variable       assigned    Over all templates, also includes
     *  $.variable      internal    Only in the own file
     *  $:variable      declared    Only in the own file
     *
     */
    namespace Lib;
    class Template {
        private $Bootstrap;
        private $dir_templates_origin;
        private $dir_templates;
        private $tpl_main;
        private $tpl_vars = array('internal' => array(), 'assigned' => array(), 'declared' => array());
        private $tpl_contents = array();
        private $allowedRelationsIf = array('<=', '>=', '==');
        private $allowedFunctionsIf = array('is_numeric');

        public $return_unsetValue = '(null)';

        public function __construct(\Core\Bootstrap $Bootstrap) {
            if (($this->dir_templates = Helper::buildPath(\Config\Template::DIR)) !== false) {
                $this->Bootstrap = $Bootstrap;
                $this->dir_templates_origin = \Config\Template::DIR;
                return ;
            } else {
                throw new \Core\Mexception('Unknown directory given');
            }
        }

        public function open($template) {
            if (file_exists($this->dir_templates.$template)) {
                $this->tpl_main = $this->dir_templates.$template;
                return true;
            } elseif(file_exists(Helper::buildPath($template))) {
                $this->tpl_main = Helper::buildPath($template);
                return true;
            } else {
                throw new \Core\Mexception('Unknown template');
            }
            return true;
        }

        public function assign($name, $value) {
            $this->assignWithScope($name, $value);
            return true;
        }

        private function assignWithScope($name, $value, $scope = 'assigned') {
            if ($scope !== 'assigned' AND $scope != 'declared' AND $scope != 'internal') {
                throw new \Core\Mexception('Unknown variable scope');
            } else {
                $this->tpl_vars[$scope][$name] = $value;
                return true;
            }
        }

        private function getVariable($name, $scope = 'assigned') {
            if ($scope == -1) {
                if (substr($name, 0, 1) == '.') {
                    $scope = 'internal';
                    $name = substr($name, 1);
                } elseif (substr($name, 0, 1) == ':') {
                    $scope = 'declared';
                    $name = substr($name, 1);
                } else {
                    $scope = 'assigned';
                }
            }
            if ($scope !== 'assigned' AND $scope != 'declared' AND $scope != 'internal') {
                throw new \Core\Mexception('Unknown variable scope');
            } elseif (strstr($name, ':')) {
                // Explode by delimiter
                $name = explode(':', $name);
                // Make it easier
                $return = $this->tpl_vars[$scope][$name[0]];
                // Unset the base value
                unset($name[0]);
                // Go trough each step
                foreach ($name AS $value) {
                    $return = $return[$value];
                }
                return $return;
            } elseif (isset($this->tpl_vars[$scope][$name])) {
                return $this->tpl_vars[$scope][$name];
            } else {
                return null;
            }
        }

        public function parseSimple($file, array $assign = array()) {
            /**
             * Steps:
             *
             * 1. Create a new Template-object
             * 2. Open the file
             * 3. Assign actually assigned variables into object
             * 4. Assign the given variables
             * 5. Parse the file
             * 6. Return the content
             */
            $object = new Template($this->Bootstrap, $this->dir_templates_origin);
            $object->return_unsetValue = '';
            $object->open($file);
            $object->tpl_vars = $this->tpl_vars;
            foreach ($assign AS $key=>$value) {
                $object->assign($key, $value);
            }
            $return = $object->parse();
            return $return;
        }

        public function show() {
            echo $this->parse();
            return true;
        }

        public function parse() {
            /**
             * Structure of parsing
             *
             * 0. Parse {ignore}-Tag
             * 1. Get declarations
             * 2. Variables outside control structure
             * 3. Control structure
             * 4. Includes
             * 5. Forms
             * 6. Loops
             *
             * We've token this order instead of recursive handling because we liked to keep it simply.
             * And we've though most user will never use all the provided functions.
             */

            // I'm lazy, make $this->tpl_contents[$this->tpl_main] a bit shorter please....
            $this->tpl_contents[$this->tpl_main] = file_get_contents($this->tpl_main);
            $main = &$this->tpl_contents[$this->tpl_main];

            // Search for ignore-Tags
            $main = preg_replace_callback(\Config\Template::REGEXP_IGNORE,
                array($this, 'parseIgnore'), $main);

            // Search for declarations
            $main = preg_replace_callback(\Config\Template::REGEXP_DECLARE,
                array($this, 'parseDeclare'), $main);

            // Search for variables
            $main = preg_replace_callback(\Config\Template::REGEXP_VARIABLE,
                array($this, 'parseVariables'), $main);

            // Create Regex for control structure
            $regex_cs_mid = \Config\Template::REGEXP_CS_MID;
            $regex_cs_start = \Config\Template::REGEXP_CS_START;
            $regex_cs_start_short = \Config\Template::REGEXP_CS_STARTS;
            $regex_cs_end = \Config\Template::REGEXP_CS_END;
            $regex_cs_end_short = \Config\Template::REGEXP_CS_ENDS;
            $regex_cs = $regex_cs_start . $regex_cs_mid . $regex_cs_end;
            $regex_cs_short = $regex_cs_start_short . $regex_cs_mid . $regex_cs_end_short;

            $count = 0; $count_short = 0;
            do {
                // Search for control structure (multiline)
                $main = preg_replace_callback($regex_cs,
                    array($this, 'parseIfelse'), $main, -1, $count);
                // Search for control structure (singleline)
                $main = preg_replace_callback($regex_cs_short,
                    array($this, 'parseIfelse'), $main, -1, $count_short);
            } while ($count > 0 OR $count_short >0);

            // Search for includes
            $main = preg_replace_callback(\Config\Template::REGEXP_INCLUDE,
                array($this, 'parseInclude'), $main);

            // Search for forms
            $main = preg_replace_callback(\Config\Template::REGEXP_FORM,
                array($this, 'parseForm'), $main);
            $main = preg_replace_callback(\Config\Template::REGEXP_LABEL,
                array($this, 'parseLabel'), $main);

            // Search for loops
            $main = preg_replace_callback(\Config\Template::REGEXP_FOREACH,
                array($this, 'parseForeach'), $main);

            return $main;
        }

        // Done
        private function parseIgnore($match) {
            /**
             * The match array arrives as followed:
             *
             * 0. Full match
             * 1. Content of tag
             */
            return str_replace(array('{', '}'), array('&#123;', '&#125;'), $match[1]);
        }

        // Done
        private function parseDeclare($match) {
            /**
             * The match array arrives as followed:
             *
             * 0. Full match
             * 1. Whitespaces
             * 2. Name of variable
             * 3. Whitespaces
             * 4. Value of the variable
             */

            $this->assignWithScope($match[2], $match[4], 'declared');
            return null;
        }

        // Done
        private function parseVariables($match) {
            /**
             * The match array arrives as followed:
             *
             * 0. Full match
             * 1. Name of variable inclusive scope
             */
            if (substr($match[1], 0, 1) == '.') {
                // Scope is internal
                $return = $this->getVariable(substr($match[1], 1), 'internal');
            } elseif (substr($match[1], 0, 1) == ':') {
                // Scope is declared
                $return = $this->getVariable(substr($match[1], 1), 'declared');
            } else {
                // Scope is assigned
                $return = $this->getVariable($match[1]);
            }

            if ($return === null) {
                return $this->return_unsetValue;
            } else {
                return $return;
            }
        }

        // Done
        private function parseInclude($include) {
            $include = $include[1];
            if (is_file($this->dir_templates.$include)) {
                /**
                 * The step to write the template variables into $object and get it back to $this is actually unneded
                 * in case that we parse not line-by-line...
                 *
                 * But it could be a step into future
                 */
                $object = new Template($this->Bootstrap, $this->dir_templates_origin);
                $object->open($include);
                $object->tpl_vars = $this->tpl_vars;
                $return = $object->parse();
                $this->tpl_vars = $object->tpl_vars;
                return $return;
            } else {
                throw new \Core\Mexception('Unknown include');
            }
        }

        // Done
        private function parseIfelse($match) {
            /**
             * Parsing If-Else is really hard... So here's the array given by the regex
             *
             * If regullary (multiline)
             * 0.   Full match
             * 1.   Condition type (mostly if)
             * 2.   Whitespaces
             * -----
             *   On comparison
             *   3. Full comparison match
             *   4. Left value
             *   5. Relation
             *   6. Right value
             *   7. (empty)
             *   8. (empty)
             *   9. (empty)
             *   On function
             *   3. Full function
             *   4. (empty)
             *   5. (empty)
             *   6. (empty)
             *   7. Exclamation mark
             *   8. Function
             *   9. Parameter
             * -----
             * 10.   Content
             * 11.  End of condition
             *
             * The whole matching is really strict. So be attended to create correct code!
             */
            if ($match[1] == 'if') {
                if (strlen($match[4]) > 0 AND strlen($match[8]) == 0) {
                    // Match a comparison
                    if ($match[6] == 'true') {
                        $match[6] = true;
                    } elseif ($match[6] == 'false') {
                        $match[6] = false;
                    }
                    if (!in_array($match[5], $this->allowedRelationsIf)) {
                        throw new \Core\Mexception('Unknown relation');
                    }
                    $match[4] = $this->getVariable(substr($match[4], 1), -1);
                    $match[6] = $this->getVariable(substr($match[6], 1), -1);
                    $code = 'return ( "'
                        .addslashes($match[4])
                        .'" '
                        .$match[5]
                        .' "'
                        .addslashes($match[6])
                        .'" '
                        .') ? true : false;';
                    if (eval($code)) {
                        return $match[10];
                    } else {
                        return null;
                    }
                } elseif (strlen($match[4]) == 0 AND strlen($match[8]) > 0) {
                    // Match a function
                    if (!in_array($match[8], $this->allowedFunctionsIf)) {
                        throw new \Core\Mexception('Unknown function');
                    }
                    $name = '_'.$match[8];
                    $if = $this->$name($this->getVariable(substr($match[9], 1), -1));
                    if ($match[7] == '!') $if = !$if;
                    if ($if) {
                        return $match[10];
                    } else {
                        return null;
                    }
                } else {
                    throw new \Core\Mexception('Bad match array');
                }
            } else {
                throw new \Core\Mexception('Unknown condition type');
            }
        }

        // Done
        private function parseForeach($match) {
            /**
             * The match array arrives as followed
             *
             * 0. Full match
             * 1. Array
             * 2. Content to repeat
             */
            $array = $this->getVariable(substr($match[1], 1), -1);
            if (!is_array($array)) {
                return null;
            } else {
                $return = array();
                foreach ($array AS $key=>$value) {
                    $this->assignWithScope('key', $key, 'internal');
                    $this->assignWithScope('value', $value, 'internal');
                    $return[] = trim(preg_replace_callback(\Config\Template::REGEXP_INTERNAL,
                        array($this, 'parseVariables'), $match[2], -1), "\n\r");
                }
                return implode("\n", $return);
            }
        }

        private function parseForm($match) {
            /**
             * The match array arrives as followed
             *
             * 0. Full match
             * If we've an formId
             *  1. formId
             *  2. Whitespaces
             *  3. (ignore)
             *  4. (ignore)
             *  5. elementId
             * If we've only an elementId
             *  1. elementId
             *  2. (ignore)
             *  3. (ignore)
             */
            if (($Form = $this->Bootstrap->getApplication('Form')) === false) {
                return null;
            }
            if (isset($match[5])) {
                return $Form->getElement($match[5], $match[1]);
            } else {
                return $Form->getElement($match[1]);
            }
        }

        private function parseLabel($match) {
            /**
             * The match array arrives as followed
             *
             * 0. Full match
             * If we've an formId
             *  1. formId
             *  2. Whitespaces
             *  3. (ignore)
             *  4. (ignore)
             *  5. labelName
             * If we've only an elementId
             *  1. labelName
             *  2. (ignore)
             *  3. (ignore)
             *  4. (ignore)
             *  5. (ignore)
             */
            if (($Form = $this->Bootstrap->getApplication('Form')) === false) {
                return null;
            }
            if (isset($match[5])) {
                return $Form->getLabel($match[5], $match[1]);
            } else {
                return $Form->getLabel($match[1]);
            }
        }

        private function _is_numeric($value) {
            return is_numeric($value);
        }
    }
?>
