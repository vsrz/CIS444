<?php

class Db {

    const DB_HOSTNAME = 'localhost';
    const DB_USER = 'groupa';
    const DB_PASSWD = 'SRSd95T8';
    const DB_SCHEMA_NAME = 'groupa';

    private static $_mysql_host;
    private static $_mysql_user;
    private static $_mysql_pass;
    private static $_mysql_db;
    protected static $dbconn;

    // Make the MySQL connection
    public function __construct(
            $mysql_host = Db::DB_HOSTNAME, 
            $mysql_user = Db::DB_USER, 
            $mysql_pass = Db::DB_PASSWD, 
            $mysql_db   = Db::DB_SCHEMA_NAME) {

        $this->_mysql_host = $mysql_host;
        $this->_mysql_user = $mysql_user;
        $this->_mysql_pass = $mysql_pass;
        $this->_mysql_db   = $mysql_db;
        self::$dbconn = new mysqli($mysql_host, $mysql_user, $mysql_pass, $mysql_db);
    }

    // Close the MySQL connection automatically when the class is 
    public function __destruct() {
        // mysqli_close(self::$dbconn);
    }

    public function select($query = '') {
        if (empty($query)) {
            return(false);
        }
        $result = self::$dbconn->real_query($query);
        if ($result == false)
            return(false);
        $results = array();
        $res = self::$dbconn->use_result();

        /**
         * Result is an array of rows. Each row will contain an associative
         * array with the columns that were requested by the query provided.        
         */
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                array_push($results, $row);
            }
        } else {
            // If the query returns an empty set, null will be returned
            return(null);
        }
        
        // Return all the rows in an array
        return($results);
    }
    
    public function scrub($val = null) {
        if ($val == null)
            return(null);
        return(self::$dbconn->real_escape_string($val));
    }

    public function insert($table = '', $data = '') {
        if (empty($data) || empty($table))
            return(false);
        $cols = implode(',', array_keys($data));
        foreach (array_values($data) as $values) {
            $esc = '\'';
            isset($val) ? $val .= ',' : $val = '';
            if (strpos($values, '()') > 0)
                $esc = '';
            $val .= $esc . self::$dbconn->real_escape_string($values) . $esc;
        }
        $dbquery = 'INSERT INTO ' . $table . ' (' . $cols . ') VALUES (' . $val . ')';

        self::$dbconn->real_query($dbquery);       
        $result = true;

        // Return error code and message if there was a problem
        if (self::$dbconn->errno > 0) {
            $result = array(self::$dbconn->errno, self::$dbconn->error);
        }
        
        // Return true if the insert was suceessful
        return($result);
    }

}
