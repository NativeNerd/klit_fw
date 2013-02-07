<?php
    namespace Lib\Query;
    /**
     * ToDo (nach Priorität geordnet):
     *
     * @todo Where-Bedingungen verschachteln
     * @todo Statement-Array noch intensiver strukturieren
     * @todo $allowedAfterwards kontrollieren
     * @todo Diverse Erweiterungen integrieren
     */

    /**
     * [Query.class.php]
     * @version 2.0.0
     * @revision 02
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
    class Query implements \Core\Interfaces\Lib {
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
        protected $marker;
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
            'in',
            'join',
            'using',
            'on',
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
                array('join', 'values', 'fields', 'primary', 'null', 'where', 'in'),
            'primary' =>
                array('fields', 'join', 'where', 'using', 'order', 'limit', 'execute'),
            'null' =>
                array('join', 'where', 'using', 'order', 'limit', 'execute', 'in'),
            'fields' =>
                array('values', 'join', 'where', 'using', 'order', 'limit', 'execute', 'in'),
            'values' =>
                array('where', 'order', 'limit', 'execute', 'in'),
            'where' =>
                array('where', 'order', 'limit', 'execute', 'in'),
            'in' =>
                array('where', 'in', 'order', 'limit', 'execute'),
            'join' =>
                array('using', 'on'),
            'using' =>
                array('where', 'order', 'limit', 'execute', 'in'),
            'on' =>
                array('where', 'order', 'limit', 'execute', 'in'),
            'order' =>
                array('limit', 'execute'),
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
            $this->Database = \Lib\Database\MySQL::getInstance();
            return ;
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
        public function _validateCall($name) {
            if (in_array($name, $this->allowedCall, true)) {
                if (count($this->marker) > 0) {
                    if (!in_array($name, $this->allowedAfterward[end($this->marker)])) {
                        throw new \Core\Mexception('Function '.$name.' is not allowed to be called here');
                    }
                }
                $this->_setMarker($name);
                return true;
            }
            throw new \Core\Mexception('Function '.$name.' is not allowed to access');
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
            }
            throw new \Core\Mexception('Unknown field');
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
            }
            return false;
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
            }
            return false;
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
            }
            return false;
        }

        /**
         * Get the error number of the last query or in case of success return false
         *
         * @return boolean
         */
        public function getError() {
            if ($this->Database->errno != 0) {
                return $this->Database->errno;
            }
            return false;
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
        public function select() {
            $this->_validateCall('select');
            $this->_reset();
            $this->_addElement('type', 'select');
            return $this;
        }

        /**
         * Initializes an Update-Query
         *
         * @return boolean
         */
        public function update() {
            $this->_validateCall('update');
            $this->_reset();
            $this->_addElement('type', 'update');
            return $this;
        }

        /**
         * Initializes an Delete-Query
         *
         * @return boolean
         */
        public function delete() {
            $this->_validateCall('delete');
            $this->_reset();
            $this->_addElement('type', 'delete');
            return $this;
        }

        /**
         * Initializes an Insert-Query
         *
         * @return boolean
         */
        public function insert() {
            $this->_validateCall('insert');
            $this->_reset();
            $this->_addElement('type', 'insert');
            return $this;
        }

        /**
         * Initializes an join
         *
         * @param array $argv
         * @return boolean
         * @throws \Core\Mexception
         */
        public function join($table, $type = 'inner') {
            $this->_validateCall('join');
            if ($type != 'left' AND $type != 'right' AND $type != 'inner')
                throw new \Core\Mexception('Unknown join condition');
            $this->activeTable = $table;
            $this->_fetchTableinfo($table);
            $this->_addElement('join',  array('table'=>$table, 'type'=>$type));
            return $this;
        }

        /**
         * Adds an using() after the join
         *
         * @param array $argv
         * @return boolean
         * @throws \Core\Mexception
         */
        public function using($using) {
            $this->_validateCall('using');
            if (!isset($this->info[$using])) {
                throw new \Core\Mexception('Unknown column');
            }
            $this->_addElement('using', $using);
            return $this;
        }

        public function on($left, $right) {
            $this->_validateCall('on');
            if (!isset($this->info[$left]) OR !isset($this->info[$right])) {
                throw new \Core\Mexception('Unknown column');
            }
            $this->_addElement('on', array($left, $right));
            return $this;
        }

        /**
         * Adds a table to the actual query
         *
         * @param array $argv
         * @return boolean
         */
        public function table($table) {
            $this->_validateCall('table');
            $this->_addElement('table', $table);
            $this->activeTable = $table;
            $this->_fetchTableinfo($table);
            return $this;
        }

        /**
         * Simple way to select with a given primaray-key
         * Adds in one the field and the where condition onto the query
         *
         * @param array $argv
         * @return boolean
         */
        public function primary($onId) {
            $this->_validateCall('primary');
            $this->fields(array(null));
            $this->where($this->_fetchField('id'), '=', $onId);
            $this->_setMarker('fields');
            $this->_setMarker('where');
            return $this;
        }

        /**
         * Selects simply a 1, no other fields (check wheter a row exists or not)
         * Maybe alias of calling fields(array('1'))
         *
         * @param array $argv
         */
        public function null() {
            $this->_validateCall('null');
            $this->_setMarker('fields');
            $this->_addElement('fields',  1);
            return $this;
        }

        /**
         * Selects the given fields
         * If $argv[0] is null, there will be all fields selected
         *
         * @param array $argv
         * @return boolean
         */
        public function fields($fields) {
            $this->_validateCall('fields');
            if (is_array($fields) AND count($fields) > 0) {
                $this->_addElement('fields', $fields);
            } elseif ($fields === null) {
                $this->_addElement('fields', null);
            } else {
                $this->_addElement('fields', array($fields));
            }
            return $this;
        }

        /**
         * Initializes a VALUE-Statement
         *
         * @param array $argv
         * @return boolean
         */
        public function values($values) {
            $this->_validateCall('values');
            if (is_array($values) > 0 AND count($values) > 0) {
                $this->_addElement('values', $values);
            } else {
                $this->_addElement('values', array($values));
            }
            return $this;
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
        public function where($onField, $operator = null, $comparisonValue = null, $relationToBefore = null) {
            $this->_validateCall('where');

            if ($onField === null) {
                $this->allowedAfterwardOnly = 'in';
                return $this;
            }

            //                                       field     operator  comp.vl.  relation
            // $this->statement[]['where'][] = array($argv[0], $argv[1], $argv[2], $argv[3]);

            if (!in_array($operator, $this->allowedOperators))   return false;
            if (!$this->_isField($onField))                      return false;

            // set a relation (like and, or, ...)
            if ($relationToBefore !== null) {
                if (!in_array(strtolower($relationToBefore), $this->allowedRelations)) {
                    return false;
                }
            }
            $this->_addElement('where', array($onField, $operator, $comparisonValue, $relationToBefore, null));
            return $this;
        }

        public function in($onField, array $arrayOfIn, $negate = false, $relationToBefore = null) {
            $this->_validateCall('in');
            if (!$this->_isField($onField)) return false;

            $this->_addElement('where', array($onField, $negate, null, $relationToBefore, $arrayOfIn));
            return $this;
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
        public function order($onField, $order = 'asc') {
            $this->_validateCall('order');
            if (!in_array($order, $this->allowedOrder) AND !isset($this->info[$onField])) {
                $this->_addElement('order', array($onField, $order));
            } else {
                throw new \Core\Mexception('Unknown order type/Unknown column');
            }
            return $this;
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
        public function limit($limit, $offset = null) {
            $this->_validateCall('limit');
            if (!is_int($limit) OR ($offset !== null AND !is_int($offset))) return false;
            if ($offset !== null) {
                $this->_addElement('limit', array(intval($limit), intval($offset)));
                return $this;
            } else {
                $this->_addElement('limit', intval($limit));
                return $this;
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
        public function execute() {
            $type = null; $table = null; $fields = null; $values = null; $limit = null; $order = null;
            $join = null; $using = null; $where = null; $on = null;
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
                    case 'on' :
                        $on[] = $this->parseOn($value['value']);
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
            $join = array_merge($join, $on);
            if (is_array($join) AND is_array($using)) {
                $join = array_combine($join, $using);
                $tmp = '';
                foreach ($join AS $key=>$value) {
                    $tmp .= $key.' '.$value;
                }
                $join = $tmp;
                unset($tmp);
            }
            if (is_array($join) AND is_array($on)) {
                $join = array_combine($join, $on);
                $tmp = '';
                foreach ($jon AS $key=> $value) {
                    $tmp .= $key . ' ' . $value;
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
            var_dump($query);
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
            //                                       field     operator  condition relation  in()
            // $this->statement[]['where'][] = array($argv[0], $argv[1], $argv[2], $argv[3], $argv[4]);

            // fetch where conditions
            if (strlen($where[3]) == 0 AND !is_array($where[4])) {
                // No relation
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
            } elseif (!is_array($where[4])) {
                // with relation
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
            } else {
                // an in()
                if (strlen($where[3]) > 0) $in = ' '.$where[3];
                else $in = 'WHERE ';
                $in .= '`'.$where[0].'`';
                if ($where[1] == true) $in .= ' NOT ';
                $in .= ' IN(';
                $count = count($where[4]);
                foreach ($where[4] AS $value) {
                    $in .= $this->_notationByType(
                        $this->_ensureByType($value, $this->info[$where[0]]['type']),
                        $this->info[$where[0]]['type']);
                    if (--$count != 0) $in .= ', ';
                }
                $in .= ')';
                return $in;
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

        protected function parseOn($on) {
            return ('ON (`'.$on[0].'` = `'.$on[1].'`)');
        }
    }
?>
