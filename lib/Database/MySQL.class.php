<?php
    /**
     * [MySQL.class.php]
     * @version 3.0.1
     * @author Christian Klauenbösch
     * @copyright Klauenbösch IT Services
     * @link http://www.klit.ch
     *
     * previous now     what changed
     *          1.0.2   -
     * 1.0.2    1.0.3   querySingle returns on no-result now NULL in case of ""
     * 1.0.3    1.0.4   querySingle detects automatically a resultfield
     * 1.0.4    2.0.0   Einige Variablennamen und Sicherheitsaspekte (private/protected) geändert
     * 2.0.0    2.0.1   Funktionalität verringert (dumpQuery, showError)
     *                  Mexception eingefügt
     * 2.0.1    2.0.2   Umgesattelt auf mysqli (Prozedural)
     * 2.0.2    3.0.0   Umgesattelt auf mysqli (Objektorientiert)
     * 3.0.0    3.0.1   Multi-Query-Option entfernt (überflüssig)
     *          3.0.1   Error-Handling verbessert, Fehlernummer wird nun auch genutzt und behandelt
     *
     * release candidate
     */
    namespace Lib;
    class MySQL extends \mysqli {
        /**
         * Contains the Bootstrap object
         * @var object
         */
        private $Bootstrap;
        /**
         * Contains the last result object of a single query
         * @var object
         */
        private $query_singleResult         = null;
        /**
         * Contains all result object
         * @var array
         */
        private $results                    = array();

        /**
         * Initializes the class
         * @param \Core\Bootstrap $Bootstrap
         * @return null
         * @throws \Core\Mexception
         */
        public function __construct(\Core\Bootstrap $Bootstrap) {
            parent::__construct(\Config\Database::HOST,
                \Config\Database::USER,
                \Config\Database::PASS,
                \Config\Database::DATABASE);
            if (mysqli_connect_error()) throw new \Core\Mexception('Unable to connect to db');
            $this->choosedb = parent::select_db(\Config\Database::DATABASE);
            $this->query('SET NAMES \'' . \Config\Database::CHARSET . '\';');
            return ;
        }

        /**
         * Does a query
         * @param string $query
         * @return object
         */
        public function query($query) {
            $result = parent::query($query);
            if (!$result) {
                return $this->handleError();
            }
            $this->results[] = $result;
            return $result;
        }

        /**
        * A query with only one single result field and line
        *
        * @param <SQL-Query> $query
        * @param <string> $resultfield
        * @return <mixed>
        */
        public function querySingle($query, $resultfield = false) {
            $matches = array();
            if (preg_match('$select(.*)from(.*)$i', $query, $matches) AND $resultfield === false) {
                if (preg_match('$,$', $matches[1])) {
                    throw new Mexception('No valid resultfield');
                } else {
                    $resultfield = trim($matches[1]);
                }
            } elseif ($resultfield === false) {
                throw new Mexception('No valid resultfield');
            }
            $result = $this->query($query);
            $this->query_singleResult = $result;
            $return = null;
            while($row = $result->fetch_assoc()) {
                return $return = $row["$resultfield"];
            }
        }

        /**
         * Gives back the insert id
         * @return id
         */
        public function insertId() {
            return $this->insert_id;
        }

        /**
         * Returns some statistics
         * @return array
         */
        public function stat() {
            return array($this->queryCount);
        }

        /**
         * Handles errors
         * @return int
         * @throws \Core\Mexception
         */
        public function handleError() {
            switch ($this->errno) {
                case 1022 : # Duplicate key
                    return 1022;
                    break;
                case 1027 : # File in use
                    return 1027;
                    break;
                case 1061 : # Duplicate Name for key
                    return 1061;
                    break;
                case 1062 : # Duplicate Entry for key
                    return 1062;
                    break;
                case 1138 : # Unallowed (null)
                    return 1138;
                    break;
                case 1146 : # Table does not exist
                    return 1146;
                    break;
                case 1304 : # Process / function already exists
                    return 1304;
                    break;
                default :
                    throw new \Core\Mexception('Unknown sql error id '.$this->errno);
                    break;
            }
        }

        /**
         * Closes the class
         */
        public function __destruct() {
            foreach($this->results as $value) {
                if (is_object($value)) @$value->free_result();
            }
            parent::close();
        }
    }

?>
