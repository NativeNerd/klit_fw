<?php
    /**
     * ToDo (nach Priorität geordnet):
     *
     * @todo Statement-Array noch intensiver strukturieren
     * @todo $allowedAfterwards kontrollieren
     * @todo Diverse Erweiterungen integrieren
     */

    /**
     * [Query.class.php]
     * @version 2.0.0
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous     now     what changed
     *              1.0.0   -
     * 1.0.0        2.0.0a  komplett überarbeitete Struktur
     * 2.0.0a       2.0.0b  Beförderung in Beta-Status
     * 2.0.0b       2.0.0   Beförderung in release
     *
     * @desc See the following lines
     * Wie diese Klasse arbeitet:
     *  - Ein Statement wird eröffnet (z.B. mit select() )
     *  - Es werden an das Statement Bedingungen geknüpft (z.B. table(), fields(), ...)
     *  - Das Statement wird abgeschlossen (mit execute() )
     *  - Es wird eine Schleife eröffnet und jeder Teil des Statements geparst (mit parse*() )
     *      - Erst jetzt wird irgendein SQL-Code geschrieben
     *  - Das Statement ist fertiggestellt, wird zusammengesetzt und ausgeführt
     *  - Der Benutzer bekommt ein Result das er mit $Model->getResult() abrufen kann
     *      - Der Benutzer kann direkt mit $Model->getResult()->fetch_assoc() die Daten verarbeiten
     */
    namespace Lib;
    class Query implements \Core\Implement\lib {
        protected static $_instance = null;
        protected static $Bootstrap = null;
        /**
         * Containts Database-Object
         * @var object
         */
        protected $Database;
        /**
         * Contains the statement as array
         * @var array
         */
        protected $statement = array();
        /**
         * Contains the result object
         * @var object
         */
        protected $result = null;
        /**
         * Contains the fetched tableinfo
         * @var array
         */
        protected $info = array();
        /**
         * Contains the already called function
         * @var array
         */
        protected $marker = array();
        /**
         * Contains the actual table in the statement
         * @var string
         */
        protected $activeTable = null;

        /**
         * Contains the functions allowed to be called through __call()
         * @var array
         */
        protected $allowedCall = array(
            'select',
            'update',
            'delete',
            'insert',
            'table',
            'primary',
            'null',
            'fields',
            'values',
            'where',
            'join',
            'using',
            'order',
            'limit',
            'execute'
            );
        /**
         * Contains allowed relations in the WHERE-statement
         * @var array
         */
        protected $allowedRelations = array('not', '!', 'or', '||', 'and', '&&', 'XOR');
        /**
         * Contains the allowed operators in the WHERE-statement
         * @var array
         */
        protected $allowedOperators = array('=', '<=>', '<>', '!=', '<=', '<', '>=', '>',
            'IS', 'IS NOT', 'IS NULL', 'IS NOT NULL');
        /**
         * Contains the allowed orders in the ORDER-statement
         * @var array
         */
        protected $allowedOrder = array('ASC', 'DESC', 'asc', 'desc', 'a', 'd', '<', '>');
        /**
         * Contains arrays of afterwards callable functions
         * @var array
         */
        protected $allowedAfterward = array(
            'select' =>
                array('table'),
            'update' =>
                array('table'),
            'delete' =>
                array('table'),
            'insert' =>
                array('table'),
            'table' =>
                array('join', 'values', 'fields', 'primary', 'null'),
            'primary' =>
                array('join', 'where', 'using', 'order', 'limit', 'execute'),
            'null' =>
                array('join', 'where', 'using', 'order', 'limit', 'execute'),
            'fields' =>
                array('values', 'join', 'where', 'using', 'order', 'limit', 'execute'),
            'values' =>
                array('where', 'order', 'limit'),
            'where' =>
                array('order', 'limit'),
            'join' =>
                array('using'),
            'using' =>
                array('order', 'limit'),
            'order' =>
                array('limit'),
            );

        /**
         * Temporary: Field list
         * @var mixed
         */
        protected $tmp_fields;

        /**
         * Initializes the Query class
         *
         * @param string $host
         * @param string $user
         * @param string $pass
         * @param string $db
         * @return (null)
         * @throws \Core\Mexception
         */
        public function __construct() {
            $this->Database = new \Lib\MySQL;
            return ;
        }

        public static function getInstance(\Core\Bootstrap $Bootstrap = null) {
            if ($Bootstrap !== null) {
                static::$Bootstrap = $Bootstrap;
            }
            if (static::$_instance === null) {
                static::$_instance = new static();
            }
            return static::$_instance;
        }

        public function __destruct() {
            return ;
        }

        /**
         * Handles all access to this class
         *
         * @param function name $name
         * @param list of arguments $argv
         * @return \Model
         * @throws \Core\Mexception
         */
        public function __call($name, $argv) {
            if (in_array($name, $this->allowedCall, true)) {
                if (!in_array($name, $this->allowedAfterward[end($this->marker)])
                    AND count($this->marker) > 0) {
                    throw new \Core\Mexception('Function '.$name.' is not allowed to be called here');
                }
                if (($this->$name($argv)) === false) {
                    throw new \Core\Mexception('Function '.$name.' had an error');
                }
                $this->_setMarker($name);
                return $this;
            } else {
                throw new \Core\Mexception('Function '.$name.' is not allowed to access');
            }
        }

        /**
         *
         *
         * ************************************************
         * ************* SECURITY METHODS      ************
         * ************************************************
         *
         *
         */

        /**
         * Provides security to input value $value, based on $type
         *
         * @param mixed $value
         * @param string $type
         * @return mixed
         * @throws \Core\Mexception
         */
        protected function _ensureByType($value, $type) {
            try {
                $matches = array();
                if (!preg_match('$([a-zA-Z]+)\(([0-9]+)\)$', $type, $matches)) {
                    if (!preg_match('$([a-zA-Z]+)$', $type, $matches)) {
                        return null;
                    }
                }
                switch (strtolower($matches[1])) {
                    case 'varchar' :
                        return $this->_ensureString($value);
                        break;
                    case 'int' :
                        return $this->_ensureInt($value);
                        break;
                    case 'float' :
                        return $this->_ensureFloat($value);
                        break;
                    case 'bool' :
                        return $this->_ensureBool($value);
                        break;
                    case 'decimal' :
                        return $this->_ensureDecimal($value);
                        break;
                    case 'datetime' :
                        return $this->_ensureDatetime($value);
                    default :
                        throw new \Core\Mexception('Unknown field type');
                }
            } catch (\Core\Mexception $e) {
                $e->quit($e->getMessage());
            }
        }

        /**
         * Makes a string secure
         *
         * @param string $string
         * @return string
         */
        protected function _ensureString($string) {
            return $this->Database->real_escape_string($string);
        }

        /**
         * Makes an intval secure
         *
         * @param int $int
         * @return int
         */
        protected function _ensureInt($int) {
            return intval($int);
        }

        /**
         * Makes a float secure
         *
         * @param float $float
         * @return float
         */
        protected function _ensureFloat($float) {
            return floatval($float);
        }

        /**
         * Makes a bool secure
         *
         * @param boolean $bool
         * @return boolean
         */
        protected function _ensureBool($bool) {
            if ($bool == 1 OR $bool == '1' OR $bool == true) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Makes a decimal value secure
         *
         * @param decimal $decimal
         * @return decimal
         */
        protected function _ensureDecimal($decimal) {
            return floatval($decimal);
        }

        /**
         * Makes a datetime secure
         *
         * @param datetime $date
         * @return datetime
         * @throws \Core\Mexception
         */
        protected function _ensureDatetime($date) {
            try {
                if (is_int(strtotime($date))) {
                    return $date;
                } else {
                    throw new \Core\Mexception('Invalid date/time format');
                }
            } catch (\Core\Mexception $e) {
                $e->quit($e->getMessage());
            }
        }

        /**
         * Notates $value to go into database
         * @todo Implement into class Query
         * @param mixed $value
         * @param string $type
         * @return null|string
         */
        protected function _notationByType($value, $type) {
            $matches = array();
            if (!preg_match('$([a-zA-Z]+)\(([0-9]+)\)$', $type, $matches)) {
                if (!preg_match('$([a-zA-Z]+)$', $type, $matches)) {
                    return null;
                }
            }
            switch (strtolower($matches[1])) {
                case 'varchar' :
                    return '"'.$value.'"';
                case 'int' :
                    return $value;
                case 'float' :
                    return $value;
                case 'bool' :
                    if ($value) {
                        return 'TRUE';
                    } else {
                        return 'FALSE';
                    }
                case 'decimal' :
                    return $value;
                case 'datetime' :
                    return '"'.$value.'"';
                default :
                    return null;
            }
        }

        /**
         *
         *
         * ************************************************
         * ************* SQL METHODS           ************
         * ************************************************
         *
         *
         */

        /**
         * Adds an element to the Statement-Array
         *
         * @param string $type
         * @param mixed $value
         * @return boolean
         */
        protected function _addElement($type, $value) {
            $this->statement[] = array('type'=>$type, 'value'=>$value);
            return true;
        }

        /**
         * Fetchs the tableinfo into $this->info
         *
         * @param string $table
         * @return boolean
         * @throws \Core\Mexception
         */
        protected function _fetchTableinfo($table) {
            $result = $this->Database->query('SHOW COLUMNS FROM `'.$this->_ensureString($table).'`;');
            if (!is_object($result)) {
                throw new \Core\Mexception('Fetch tableinfo of table '.$table.' failed');
            }
            while ($row = $result->fetch_assoc()) {
                if ($row['Null'] == 'YES') {
                    $row['Null'] = true;
                } else {
                    $row['Null'] = false;
                }
                $this->info[$row['Field']] = array('type' => $row['Type'], 'null' => $row['Null']);
            }
            return true;
        }

        /**
         * Fetchs a field name to the active table
         *
         * @param string $field
         * @return string
         * @throws \Core\Mexception
         */
        protected function _fetchField($field) {
            if (isset($this->info[$this->activeTable.'_'.$field])) {
                return $this->activeTable.'_'.$field;
            } else {
                throw new \Core\Mexception('Unknown field');
            }
        }

        /**
         * Checks wheter $field is a valid field of the active table
         *
         * @param string $field
         * @return boolean
         */
        protected function _isField($field) {
            if (isset($this->info[$field])) {
                return true;
            } else {
                return false;
            }
        }

        /**
         *
         *
         * ************************************************
         * ************* MARKER METHODS        ************
         * ************************************************
         *
         *
         */

        /**
         * Resets the object to be ready to create a new statement
         *
         * @return boolean
         */
        protected function _reset() {
            $this->marker = array();
            $this->result = null;
            $this->statement = array();
            return true;
        }

        /**
         * Sets a marker on $function
         *
         * @param string $function
         * @return boolean
         */
        protected function _setMarker($function) {
            $this->marker[] = $function;
            return true;
        }

        /**
         * Checks wheter the marker of $function is set or not
         *
         * @param string $function
         * @return boolean
         */
        protected function _callMarker($function) {
            if (in_array($function, $this->marker)) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Validates wheter all marker in $needed are called
         *
         * @param array $needed
         * @return boolean
         */
        protected function _validateMarker(array $needed) {
            foreach ($needed AS $value) {
                if (!in_array($value, $this->marker)) {
                    return false;
                }
            }
            return true;
        }

        /**
         *
         *
         * ************************************************
         * ************* SQL METHODS           ************
         * ************************************************
         *
         *
         */

        /**
         * Get the object result or in case of failure returns boolean
         *
         * @return boolean
         */
        public function getResult() {
            if (is_object($this->result)) {
                return $this->result;
            } elseif ($this->Database->errno == 0) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Get the error number of the last query or in case of success return false
         *
         * @return boolean
         */
        public function getError() {
            if ($this->Database->errno != 0) {
                return $this->Database->errno;
            } else {
                return false;
            }
        }

        /**
         *
         *
         * ************************************************
         * ************* TRANSACTION METHODS   ************
         * ************************************************
         *
         *
         */

        /**
         * Opens a new transaction
         *
         * @return boolean
         */
        public function beginTransaction() {
            $this->Database->query('START TRANSACTION;');
            if ($this->Database->errno) {
                return false;
            }
            return true;
        }

        /**
         * Commits the transaction
         *
         * @return boolean
         */
        public function commit() {
            $this->Database->query('COMMIT;');
            if ($this->Database->errno != 0) {
                return false;
            }
            return true;
        }

        /**
         * Rollback to $savepoint or beginning
         * @param string $savepoint
         * @return boolean
         */
        public function rollback($savepoint = null) {
            if ($savepoint == null) {
                $this->Database->query('ROLLBACK;');
            } else {
                if (!preg_match('/([\w]+)/', $savepoint)) {
                    return false;
                }
                $this->Database->query('ROLLBACK TO '.$savepoint.';');
                if ($this->Database->errno != 0) {
                    return false;
                }
            }
            if ($this->Database->errno != 0) {
                return false;
            }
            return true;
        }

        /**
         * Create savepoint
         * @param string $name
         * @return boolean
         */
        public function savepoint($name) {
            if (!preg_match('/([\w]+)/', $name)) {
                return false;
            }
            $this->Database->query('SAVEPOINT '.$name);
            if ($this->Database->errno != 0) {
                return false;
            }
            return true;

        }

        /**
         *
         *
         * ************************************************
         * ************* STATEMENT METHOD      ************
         * ************************************************
         *
         *
         */

        /**
         * Initializes an Select-Query
         *
         * @return boolean
         */
        protected function select() {
            $this->_reset();
            $this->_addElement('type', 'select');
            return true;
        }

        /**
         * Initializes an Update-Query
         *
         * @return boolean
         */
        protected function update() {
            $this->_reset();
            $this->_addElement('type', 'update');
            return true;
        }

        /**
         * Initializes an Delete-Query
         *
         * @return boolean
         */
        protected function delete() {
            $this->_reset();
            $this->_addElement('type', 'delete');
            return true;
        }

        /**
         * Initializes an Insert-Query
         *
         * @return boolean
         */
        protected function insert() {
            $this->_reset();
            $this->_addElement('type', 'insert');
            return true;
        }

        /**
         * Initializes an join
         *
         * @param array $argv
         * @return boolean
         * @throws \Core\Mexception
         */
        protected function join($argv) {
            if (!isset($argv[1])) $argv[1] = 'inner';
            elseif ($argv[1] != 'left' AND $argv[1] != 'right' AND $argv[1] != 'inner')
                throw new \Core\Mexception('Unknown join condition');
            $this->activeTable = $argv[0];
            $this->_fetchTableinfo($argv[0]);
            $this->_addElement('join',  array('table'=>$argv[0], 'type'=>$argv[1]));
            return true;
        }

        /**
         * Adds an using() after the join
         *
         * @param array $argv
         * @return boolean
         * @throws \Core\Mexception
         */
        protected function using($argv) {
            if (!isset($this->info[$argv[0]])) {
                throw new \Core\Mexception('Unknown column');
            } else {
                $this->_addElement('using', $argv[0]);
                return true;
            }
        }

        /**
         * Adds a table to the actual query
         *
         * @param array $argv
         * @return boolean
         */
        protected function table($argv) {
            $this->_addElement('table', $argv[0]);
            $this->activeTable = $argv[0];
            // fetch tableinfo
            $this->_fetchTableinfo($argv[0]);
            return true;
        }

        /**
         * Simple way to select with a given primaray-key
         * Adds in one the field and the where condition onto the query
         *
         * @param array $argv
         * @return boolean
         */
        protected function primary($argv) {
            $this->_setMarker('fields');
            $this->fields($this->_fetchField('id'));
            $this->where(array($this->_fetchField('id'), '=', $argv[0]));
            return true;
        }

        /**
         * Selects simply a 1, no other fields (check wheter a row exists or not)
         * Maybe alias of calling fields(array('1'))
         *
         * @param array $argv
         */
        protected function null($argv) {
            $this->_setMarker('fields');
            $this->_addElement('fields',  1);
            return true;
        }

        /**
         * Selects the given fields
         * If $argv[0] is null, there will be all fields selected
         *
         * @param array $argv
         * @return boolean
         */
        protected function fields($argv) {
            if (is_array($argv[0])) {
                $this->_addElement('fields', $argv[0]);
            } elseif ($argv[0] !== null) {
                $this->_addElement('fields', array($argv));
            } else {
                $this->_addElement('fields', null);
            }
            return true;
        }

        /**
         * Initializes a VALUE-Statement
         *
         * @param array $argv
         * @return boolean
         */
        protected function values($argv) {
            if (is_array($argv[0])) {
                $this->_addElement('values', $argv[0]);
            } elseif ($argv !== null) {
                $this->_addElement('values', array($argv));
            } else {
                return false;
            }
            return true;
        }

        /**
         * Adds a where condition
         * Give parameters in following order (not as array)
         *  1. Field of table
         *  2. Operator (e.g. =, <=, ...)
         *  3. Condition (the value the field has to be)
         *  4. Relation related to the previous element
         *
         * @param array $argv
         * @return boolean
         */
        protected function where($argv) {
            //                                       field     operator  condition relation
            // $this->statement[]['where'][] = array($argv[0], $argv[1], $argv[2], $argv[3]);

            if (!in_array($argv[1], $this->allowedOperators))   return false;
            if (!$this->_isField($argv[0]))                    return false;

            // set a relation (like and, or, ...)
            if (isset($argv[3])) {
                if (!in_array(strtolower($argv[3]), $this->allowedRelations)) {
                    return false;
                }
                $this->_addElement('where', array($argv[0], $argv[1], $argv[2], $argv[3]));
                return true;
            // or don't set a relation
            } else {
                $this->_addElement('where', array($argv[0], $argv[1], $argv[2], null));
                return true;
            }
        }

        /**
         * Adds an Order-Statement
         * Give parameters in following order (not as array)
         *  1. Field of table
         *  2. Order (e.g. asc, desc, <, ...)
         *
         * @param array $argv
         * @return boolean
         * @throws \Core\Mexception
         */
        protected function order($argv) {
            if (isset($argv[1])) {
                if (!in_array($argv[1], $this->allowedOrder) AND !isset($this->info[$argv[0]])) {
                    $this->_addElement('order', array(array($argv[0], $argv[1])));
                } else {
                    throw new \Core\Mexception('Unknown order type/Unknown column');
                }
            } else {
                foreach ($argv[0] AS $value) {
                    if (!in_array($value[1], $this->allowedOrder) OR !isset($this->info[$value[0]])) {
                        throw new \Core\Mexception('Unknown order type/unknown column');
                    }
                }
                $this->_addElement('order', $argv[0]);
            }
            return true;
        }

        /**
         * Adds a limit condition
         * You could give following parameters:
         *  - Simple int for selecting only (int) rows
         *  - Two parameters as LIMIT (int),(int)
         *
         * @param array $argv
         * @return boolean
         */
        protected function limit($argv) {
            if (!is_int($argv[0]) OR (isset($argv[1]) AND !is_int($argv[1]))) return false;

            if (isset($argv[1])) {
                $this->_addElement('limit', array(intval($argv[0]), intval($argv[1])));
                return true;
            } else {
                $this->_addElement('limit', intval($argv[0]));
                return true;
            }
        }

        /**
         *
         *
         * ************************************************
         * ************* PARSE METHODS         ************
         * ************************************************
         *
         *
         */

        /**
         * Parses and executes the created statement
         *
         * @return mixed
         * @throws \Core\Mexception
         */
        protected function execute() {
            $type = null; $table = null; $fields = null; $values = null; $limit = null; $order = null;
            $join = null; $using = null; $where = null;
            // Parse query
            foreach ($this->statement AS $value) {
                switch ($value['type']) {
                    case 'type' :
                        $type = $this->parseType($value['value']);
                        break;
                    case 'table' :
                        $table = $this->parseTable($value['value']);
                        break;
                    case 'fields' :
                        $fields = $this->parseFields($value['value'], $type);
                        break;
                    case 'values' :
                        $values = $this->parseValues($value['value'], $type);
                        break;
                    case 'where' :
                        $where[] = $this->parseWhere($value['value']);
                        break;
                    case 'order' :
                        $order = $this->parseOrder($value['value']);
                        break;
                    case 'limit' :
                        $limit = $this->parseLimit($value['value']);
                        break;
                    case 'join' :
                        $join[] = $this->parseJoin($value['value']);
                        break;
                    case 'using' :
                        $using[] = $this->parseUsing($value['value']);
                        break;
                    default :
                        throw new \Core\Mexception('Unknown Type '.$value['type']);
                }
            }

            // Get where conditions
            if (is_array($where)) {
                $tmp = '';
                foreach ($where AS $value) {
                    $tmp .= $value;
                }
                $where = $tmp;
                unset($tmp);
            }

            // build joins
            if (is_array($join) AND is_array($using)) {
                $join = array_combine($join, $using);
                $tmp = '';
                foreach ($join AS $key=>$value) {
                    $tmp .= $key.' '.$value;
                }
                $join = $tmp;
            unset($tmp);
            }

            if ($type == 'SELECT') {
                $neededMarker = array('fields', 'table');
                if (!$this->_validateMarker($neededMarker)) {
                    throw new \Core\Mexception('Statement not complete');
                }

                // SELECT [fields]
                //      FROM [table]
                //      [[join] JOIN [table] USING([field])]
                //      [WHERE [conditions]]
                //      [ORDER BY [order]]
                //      [LIMIT [limit]]
                $query = "SELECT $fields FROM $table $join $using $where $order $limit ;";
            } elseif ($type == 'UPDATE') {
                $neededMarker = array('table', 'values', 'where');
                if (!$this->_validateMarker($neededMarker)) {
                    throw new \Core\Mexception('Statement not complete');
                }

                // UPDATE [table]
                //      SET [[field] = [value] *]
                //      [WHERE [conditions]]
                //      [LIMIT [limit]]
                $query = "UPDATE $table SET $values $where $order $limit ;";
            } elseif ($type == 'DELETE') {
                $neededMarker = array('table', 'where', 'limit');
                if (!$this->_validateMarker($neededMarker)) {
                    throw new \Core\Mexception('Statement not complete');
                }

                // DELETE FROM [table]
                //      [WHERE [conditions]]
                //      [ORDER BY [order]]
                //      [LIMIT [limit]]
                $query = "DELETE FROM $table $where $order $limit ;";
            } elseif ($type == 'INSERT') {
                $neededMarker = array('table', 'fields', 'values');
                if (!$this->_validateMarker($neededMarker)) {
                    throw new \Core\Mexception('Statement not complete');
                }
                // INSERT INTO [table] ([fields])
                //      VALUES ([values])
                $query = "INSERT INTO $table ($fields) VALUES ($values) ;";
            }
            // Throw query through database and give result back
            $this->result = $this->Database->query($query);
            return $this->result;
        }

        /**
         * Parses the Query-Type
         *
         * @param string $type
         * @return string
         * @throws \Core\Mexception
         */
        protected function parseType($type) {
            switch ($type) {
                case 'select' :
                    return 'SELECT';
                case 'update' :
                    return 'UPDATE';
                case 'delete' :
                    return 'DELETE';
                case 'insert' :
                    return 'INSERT';
                default :
                    throw new \Core\Mexception('Unknown Type '.$type);
            }
        }

        /**
         * Parses the table
         *
         * @param string $table
         * @return string
         */
        protected function parseTable($table) {
            return '`'.$table.'`';
        }

        /**
         * Parses the fields
         *
         * @param array $fields
         * @param string $type
         * @return boolean|string
         */
        protected function parseFields($fields, $type) {
            /**
             * Possible values for $fields
             *
             * 1        You need to select simply a "1"
             * array    You have to parse a list of fields
             * null     You need to select all fields
             */
            if ($type == 'UPDATE') {
                $this->tmp_fields = $fields;
                return true;
            } else {
                $return = '';
                if (is_array($fields)) {
                    foreach ($fields AS $value) {
                        if (strlen($return) == 0) {
                            $return = '`'.$value.'`';
                        } else {
                            $return .= ', '.'`'.$value.'`';
                        }
                    }
                } elseif ($fields === null) {
                    $return = '*';
                } else {
                    $return = '1';
                }
                return $return;
            }
        }

        /**
         * Parses the values
         * If you have an update staement, fields will be integrated into query here
         *
         * @param string $values
         * @param string $type
         * @return string
         * @throws \Core\Mexception
         */
        protected function parseValues($values, $type) {
            if ($type == 'UPDATE') {
                $return = '';
                foreach ($this->tmp_fields AS $key=>$value) {
                    if (!isset($this->info[$value]))
                        throw new \Core\Mexception('Unknown field');
                    if (isset($values[$key])) {
                        if (strlen($return) == 0) {
                            $return = '`'.$value.'`'.' = '.$this->_notationByType(
                                    $this->_ensureByType(
                                    $values[$key], $this->info[$value]['type']
                                    ), $this->info[$value]['type']);
                        } else {
                            $return .= ', `'.$value.'`'.' = '.$this->_notationByType(
                                    $this->_ensureByType(
                                    $values[$key], $this->info[$value]['type']
                                    ), $this->info[$value]['type']);
                        }
                    } else {
                        throw new \Core\Mexception('There is no value for a given field');
                    }
                }
                return $return;
            } else {
            $return = '';
            if (is_array($values)) {
                foreach ($values AS $value) {
                    if (strlen($return) == 0) {
                        $return = "'".$value."'";
                    } else {
                        $return .= ", "."'".$value."'";
                    }
                }
            } elseif ($values === null) {
                $return = '*';
            } else {
                $return = '1';
            }
            }
            return $return;
        }

        /**
         * Parses a where-statement
         * where-statement will be saved as $this->tmp_where and will not be returned
         *
         * @param array $where
         * @return boolean
         * @throws \Core\Mexception
         */
        protected function parseWhere($where) {
            //                                       field     operator  condition relation
            // $this->statement[]['where'][] = array($argv[0], $argv[1], $argv[2], $argv[3]);

            // fetch where conditions
            if (strlen($where[3]) == 0) {
                return 'WHERE '.
                        '`'.
                        $where[0].
                        '`'.
                        ' '.
                        $where[1].
                        ' '.
                        $this->_notationByType(
                            $this->_ensureByType($where[2], $this->info[$where[0]]['type']),
                            $this->info[$where[0]]['type']);
            } else {
                return ' '.
                        $where[3].
                        ' '.
                        '`'.
                        $where[0].
                        '`'.
                        ' '.
                        $where[1].
                        ' '.
                        $this->_notationByType(
                            $this->_ensureByType($where[2], $this->info[$where[0]]['type']),
                            $this->info[$where[0]]['type']);
            }
        }

        /**
         * Parses the order-statement
         *
         * @param array $order
         * @return string
         */
        protected function parseOrder($order) {
            $return = '';
            foreach ($order AS $value) {
                if ($value[1] == 'a' OR $value[1] == '<') $value[1] = 'ASC';
                if ($value[1] == 'd' OR $value[1] == '>') $value[1] = 'DESC';
                if (strlen($return) == 0) {
                    $return = '`'.$value[0].'` '.$value[1];
                } else {
                    $return .= ', '.'`'.$value[0].'` '.$value[1];
                }
            }
            return 'ORDER BY '.$return;
        }

        /**
         * Parses the limit-staement
         *
         * @param array|string $limit
         * @return string
         */
        protected function parseLimit($limit) {
            if (is_array($limit)) {
                return 'LIMIT '.$limit[0].','.$limit[1];
            } else {
                return 'LIMIT '.$limit;
            }
        }

        /**
         * Parses the join-statement
         *
         * @param array $value
         * @return string
         */
        protected function parseJoin($value) {
            return (strtoupper($value['type']).' JOIN `'.$value['table'].'`');
        }

        /**
         * Parses the using-statement
         * @param string $using
         * @return string
         */
        protected function parseUsing($using) {
            return ('USING(`'.$using.'`)');
        }
    }
?>
