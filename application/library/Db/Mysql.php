<?php
class Db_Mysql
{

    public static $SHOW_ERR = true;
    public static $TIMESTAMP_WRITES = false;
    protected $config_master;
    protected $config_slave;
    protected $pdo_master;
    protected $pdo_slave;
    protected $pdo_exception;
    protected static $instance = null;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Db_Mysql();
        }
        return self::$instance;
    }

    public function __construct($db)
    {
        $this->configMaster($db['m']['host'], $db['m']['name'],
                $db['m']['user'], $db['m']['pwd'], $db['m']['port']);
        $this->configSlave($db['s']['host'], $db['s']['name'], $db['s']['user'],
                $db['s']['pwd'], $db['s']['port']);
    }

    public function configMaster($host, $name, $user, $password, $port = null, $driver = 'mysql')
    {
        if (isset($this->pdo_master)) {
            throw new Exception ('请勿重连主库');
        }
        $this->config_master = array(
            'driver' => $driver,
            'host' => $host,
            'name' => $name,
            'user' => $user,
            'password' => $password,
            'port' => $port
        );
    }

    public function configSlave($host, $name, $user, $password, $port = null, $driver = 'mysql')
    {
        if (isset($this->pdo_slave)) {
            throw new Exception ('请勿重连从库');
        }
        $this->config_slave = array(
            'driver' => $driver,
            'host' => $host,
            'name' => $name,
            'user' => $user,
            'password' => $password,
            'port' => $port
        );
    }

    protected function createConnection($driver, $host, $name, $user, $password, $port = null)
    {

        try {
            $dsn = $driver . ':host=' . $host;
            if (!empty($port)) {
                $dsn .= ";port=$port";
            }
            $dsn .= ";dbname=$name";
            $conn = new PDO($dsn, $user, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            //抛出异常进行捕获
            throw $e;
            return false;
        } catch (Exception $e) {
            //抛出异常进行捕获
            throw $e;
            return false;
        }
    }

    protected function getMaster()
    {

        if (!isset($this->pdo_master)) {
            $this->pdo_master = $this->createConnection(
                    $this->config_master['driver'],
                    $this->config_master['host'], $this->config_master['name'],
                    $this->config_master['user'],
                    $this->config_master['password'],
                    $this->config_master['port']
            );
        }

        return $this->pdo_master;
    }

    protected function getSlave()
    {
        if (!isset($this->pdo_slave)) {
            $this->pdo_slave = $this->createConnection(
                    $this->config_slave['driver'], $this->config_slave['host'],
                    $this->config_slave['name'], $this->config_slave['user'],
                    $this->config_slave['password'], $this->config_slave['port']
            );
        }

        return $this->pdo_slave;
    }

    /**
     * method select.
     * 	- retrieve information from the database, as an array
     *
     * @param string $table - the name of the db table we are retreiving the rows from
     * @param array $params - associative array representing the WHERE clause filters
     * @param int $limit (optional) - the amount of rows to return
     * @param int $start (optional) - the row to start on, indexed by zero
     * @param array $order_by (optional) - an array with order by clause
     * @param bool $use_master (optional) - use the master db for this read
     * @return mixed - associate representing the fetched table row, false on failure
     */
    public function select($table, $params = null, $limit = null, $start = null, $order_by = null, $use_master = false, $break = false)
    {
        $sql_str = "SELECT * FROM $table";
        $sql_str .= ( count($params) > 0 ? ' WHERE ' : '' );
        $add_and = false;
        if (empty($params)) {
            $params = array();
        }
        foreach ($params as $key => $val) {
            if ($add_and) {
                $sql_str .= ' AND ';
            } else {
                $add_and = true;
            }
            $sql_str .= "$key = :$key";
        }

        if (!empty($order_by)) {
            $sql_str .= ' ORDER BY';
            $add_comma = false;
            foreach ($order_by as $column => $order) {
                if ($add_comma) {
                    $sql_str .= ', ';
                } else {
                    $add_comma = true;
                }
                $sql_str .= " $column $order";
            }
        }
        if ($break) {
            return $sql_str;
        }
        try {
            $pdo_connection = $use_master ? $this->getMaster() : $this->getSlave();
            $pdoDriver = $pdo_connection->getAttribute(PDO::ATTR_DRIVER_NAME);
            $disableLimit = array("sqlsrv", "mssql", "oci");
            if (!is_null($limit) && !in_array($pdoDriver, $disableLimit)) {
                $sql_str .= ' LIMIT ' . (!is_null($start) ? "$start, " : '') . "$limit";
            }

            $pstmt = $pdo_connection->prepare($sql_str);
            foreach ($params as $key => $val) {
                $pstmt->bindValue(':' . $key, $val);
            }

            $pstmt->execute();

            if (!is_null($limit) && $limit == 1) {
                return $pstmt->fetch(PDO::FETCH_ASSOC);
            } else {
                return $pstmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        }
    }

    /**
     * method selectMaster.
     * 	- retrieve information from the master database, as an array
     *
     * @param table - the name of the db table we are retreiving the rows from
     * @param params - associative array representing the WHERE clause filters
     * @param int $limit (optional) - the amount of rows to return
     * @param int $start (optional) - the row to start on, indexed by zero
     * @param array $order_by (optional) - an array with order by clause
     * @return mixed - associate representing the fetched table row, false on failure
     */
    public function selectMaster($table, $params = array(), $limit = null, $start = null, $order_by = null, $break = false)
    {
        return $this->select($table, $params, $limit, $start, $order_by, true,
                        $break);
    }

    /**
     * method selectFirst.
     * 	- retrieve the first row returned from a select statement
     *
     * @param table - the name of the db table we are retreiving the rows from
     * @param params - associative array representing the WHERE clause filters
     * @param array $order_by (optional) - an array with order by clause
     * @return mixed - associate representing the fetched table row, false on failure
     */
    public function selectFirst($table, $params = array(), $order_by = null, $break = false)
    {
        return $this->select($table, $params, 1, null, $order_by, false, $break);
    }

    /**
     * method selectFirstMaster.
     * 	- retrieve the first row returned from a select statement using the master database
     *
     * @param table - the name of the db table we are retreiving the rows from
     * @param params - associative array representing the WHERE clause filters
     * @param array $order_by (optional) - an array with order by clause
     * @return mixed - associate representing the fetched table row, false on failure
     */
    public function selectFirstMaster($table, $params = array(), $order_by = null, $break = false)
    {
        return $this->select($table, $params, 1, null, $order_by, true, $break);
    }

    /**
     * method delete.
     * 	- deletes rows from a table based on the parameters
     *
     * @param table - the name of the db table we are deleting the rows from
     * @param params - associative array representing the WHERE clause filters
     * @return bool - associate representing the fetched table row, false on failure
     */
    public function delete($table, $params = array(), $break = false)
    {
        // building query string
        $sql_str = "DELETE FROM $table";
        // append WHERE if necessary
        $sql_str .= ( count($params) > 0 ? ' WHERE ' : '' );

        $add_and = false;
        // add each clause using parameter array
        foreach ($params as $key => $val) {
            // only add AND after the first clause item has been appended
            if ($add_and) {
                $sql_str .= ' AND ';
            } else {
                $add_and = true;
            }

            // append clause item
            $sql_str .= "$key = :$key";
        }
        if ($break) {
            return $sql_str;
        }
        // now we attempt to retrieve the row using the sql string
        try {
            $pstmt = $this->getMaster()->prepare($sql_str);

            // bind each parameter in the array			
            foreach ($params as $key => $val) {
                $pstmt->bindValue(':' . $key, $val);
            }

            // execute the delete query
            $successful_delete = $pstmt->execute();

            // if we were successful, return the amount of rows updated, otherwise return false
            return ($successful_delete == true) ? $pstmt->rowCount() : false;
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        }
    }

    /**
     * method update.
     * 	- updates a row to the specified table
     *
     * @param string $table - the name of the db table we are adding row to
     * @param array $params - associative array representing the columns and their respective values to update
     * @param array $wheres (Optional) - the where clause of the query
     * @param bool $timestamp_this (Optional) - if true we set date_created and date_modified values to now
     * @return int|bool - the amount of rows updated, false on failure
     */
    public function update($table, $params, $wheres = array(), $timestamp_this = null, $break = false)
    {
        if (is_null($timestamp_this)) {
            $timestamp_this = self::$TIMESTAMP_WRITES;
        }
        // build the set part of the update query by
        // adding each parameter into the set query string
        $add_comma = false;
        $set_string = '';
        foreach ($params as $key => $val) {
            // only add comma after the first parameter has been appended
            if ($add_comma) {
                $set_string .= ', ';
            } else {
                $add_comma = true;
            }

            // now append the parameter
            $set_string .= "$key=:param_$key";
        }

        // add the timestamp columns if neccessary
        if ($timestamp_this === true) {
            $set_string .= ($add_comma ? ', ' : '') . 'date_modified=' . time();
        }

        // lets add our where clause if we have one
        $where_string = '';
        if (!empty($wheres)) {
            // load each key value pair, and implode them with an AND
            $where_array = array();
            foreach ($wheres as $key => $val) {
                $where_array[] = "$key=:where_$key";
            }
            // build the final where string
            $where_string = 'WHERE ' . implode(' AND ', $where_array);
        }

        // build final update string
        $sql_str = "UPDATE $table SET $set_string $where_string";
        if ($break) {
            return $sql_str;
        }
        // now we attempt to write this row into the database
        try {
            $pstmt = $this->getMaster()->prepare($sql_str);

            // bind each parameter in the array			
            foreach ($params as $key => $val) {
                $pstmt->bindValue(':param_' . $key, $val);
            }

            // bind each where item in the array			
            foreach ($wheres as $key => $val) {
                $pstmt->bindValue(':where_' . $key, $val);
            }

            // execute the update query
            $successful_update = $pstmt->execute();

            // if we were successful, return the amount of rows updated, otherwise return false
            return ($successful_update == true) ? $pstmt->rowCount() : false;
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        }
    }

    /**
     * method insert.
     * 	- adds a row to the specified table
     *
     * @param string $table - the name of the db table we are adding row to
     * @param array $params - associative array representing the columns and their respective values
     * @param bool $timestamp_this (Optional), if true we set date_created and date_modified values to now
     * @return mixed - new primary key of inserted table, false on failure
     */
    public function insert($table, $params = array(), $timestamp_this = null, $break = false)
    {
        if (is_null($timestamp_this)) {
            $timestamp_this = self::$TIMESTAMP_WRITES;
        }

        // first we build the sql query string
        $columns_str = '(';
        $values_str = 'VALUES (';
        $add_comma = false;

        // add each parameter into the query string
        foreach ($params as $key => $val) {
            // only add comma after the first parameter has been appended
            if ($add_comma) {
                $columns_str .= ', ';
                $values_str .= ', ';
            } else {
                $add_comma = true;
            }

            // now append the parameter
            $columns_str .= "$key";
            $values_str .= ":$key";
        }

        // add the timestamp columns if neccessary
        if ($timestamp_this === true) {
            $columns_str .= ($add_comma ? ', ' : '') . 'date_created, date_modified';
            $values_str .= ($add_comma ? ', ' : '') . time() . ', ' . time();
        }

        // close the builder strings
        $columns_str .= ') ';
        $values_str .= ')';

        // build final insert string
        $sql_str = "INSERT INTO $table $columns_str $values_str";
        if ($break) {
            return $sql_str;
        }
        // now we attempt to write this row into the database
        try {
            $pstmt = $this->getMaster()->prepare($sql_str);

            // bind each parameter in the array			
            foreach ($params as $key => $val) {
                $pstmt->bindValue(':' . $key, $val);
            }

            $pstmt->execute();
            $newID = $this->getMaster()->lastInsertId();

            // return the new id
            return $newID;
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        }
    }

    /**
     * method insertMultiple.
     * 	- adds multiple rows to a table with a single query
     *
     * @param string $table - the name of the db table we are adding row to
     * @param array $columns - contains the column names
     * @param bool $timestamp_these (Optional), if true we set date_created and date_modified values to NOW() for each row
     * @return mixed - new primary key of inserted table, false on failure
     */
    public function insertMultiple($table, $columns = array(), $rows = array(), $timestamp_these = null, $break = false)
    {
        if (is_null($timestamp_these)) {
            $timestamp_these = self::$TIMESTAMP_WRITES;
        }

        // generate the columns portion of the insert statment
        // adding the timestamp fields if needs be
        if ($timestamp_these) {
            $columns[] = 'date_created';
            $columns[] = 'date_modified';
        }
        $columns_str = '(' . implode(',', $columns) . ') ';

        // generate the values portions of the string
        $values_str = 'VALUES ';
        $add_comma = false;

        foreach ($rows as $row_index => $row_values) {
            // only add comma after the first row has been added
            if ($add_comma) {
                $values_str .= ', ';
            } else {
                $add_comma = true;
            }

            // here we will create the values string for a single row
            $values_str .= '(';
            $add_comma_forvalue = false;
            foreach ($row_values as $value_index => $value) {
                if ($add_comma_forvalue) {
                    $values_str .= ', ';
                } else {
                    $add_comma_forvalue = true;
                }
                // generate the bind variable name based on the row and column index
                $values_str .= ':' . $row_index . '_' . $value_index;
            }
            // append timestamps if necessary
            if ($timestamp_these) {
                $values_str .= ($add_comma_forvalue ? ', ' : '') . time() . ', ' . time();
            }
            $values_str .= ')';
        }

        // build final insert string
        $sql_str = "INSERT INTO $table $columns_str $values_str";
        if ($break) {
            return $sql_str;
        }
        // now we attempt to write this multi inster query to the database using a transaction
        try {
            $this->getMaster()->beginTransaction();
            $pstmt = $this->getMaster()->prepare($sql_str);

            // traverse the 2d array of rows and values to bind all parameters
            foreach ($rows as $row_index => $row_values) {
                foreach ($row_values as $value_index => $value) {
                    $pstmt->bindValue(':' . $row_index . '_' . $value_index,
                            $value);
                }
            }

            // now lets execute the statement, commit the transaction and return
            $pstmt->execute();
            $this->getMaster()->commit();
            return true;
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            $this->getMaster()->rollback();
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            $this->getMaster()->rollback();
            return false;
        }
    }

    /**
     * method execute.
     * 	- executes a query that modifies the database
     *
     * @param string $query - the SQL query we are executing
     * @param bool $use_master (Optional) - whether or not to use the master connection
     * @return mixed - the affected rows, false on failure
     */
    public function execute($query, $params = array())
    {
        try {
            // use the master connection
            $pdo_connection = $this->getMaster();

            // prepare the statement
            $pstmt = $pdo_connection->prepare($query);

            // bind each parameter in the array			
            foreach ((array) $params as $key => $val) {
                $pstmt->bindValue($key, $val);
            }

            // execute the query
            $result = $pstmt->execute();

            // only if return value is false did this query fail
            return ($result == true) ? $pstmt->rowCount() : false;
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        }
    }

    /**
     * method query.
     * 	- returns data from a free form select query
     *
     * @param string $query - the SQL query we are executing
     * @param array $params - a list of bind parameters
     * @param bool $use_master (Optional) - whether or not to use the master connection
     * @return mixed - the affected rows, false on failure
     */
    public function query($query, $params = array(), $use_master = false)
    {
        try {
            // decide which database we are selecting from
            $pdo_connection = $use_master ? $this->getMaster() : $this->getSlave();
            $pstmt = $pdo_connection->prepare($query);

            // bind each parameter in the array			
            foreach ((array) $params as $key => $val) {
                $pstmt->bindValue($key, $val);
            }

            // execute the query
            $pstmt->execute();

            // now return the results
            return $pstmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if (self::$SHOW_ERR == true) {
                throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        } catch (Exception $e) {
            if (self::$SHOW_ERR == true) {
                    throw new Exception ($e);
            }
            $this->pdo_exception = $e;
            return false;
        }
    }

    /**
     * method queryFirst.
     * 	- returns the first record from a free form select query
     *
     * @param string $query - the SQL query we are executing
     * @param array $params - a list of bind parameters
     * @param bool $use_master (Optional) - whether or not to use the master connection
     * @return mixed - the affected rows, false on failure
     */
    public function queryFirst($query, $params = array(), $use_master = false)
    {
        $result = $this->query($query, $params, $use_master);
        if (empty($result)) {
            return false;
        } else {
            return $result[0];
        }
    }

    /**
     * method getErrorMessage.
     * 	- returns the last error message caught
     */
    public function getErrorMessage()
    {
        if ($this->pdo_exception) {
            return $this->pdo_exception->getMessage();
        } else {
            return 'Database temporarily unavailable';
        }
    }

    public function getPDOException()
    {
        return $this->pdo_exception;
    }

    function __destruct()
    {
        unset($this->pdo_master);
        unset($this->pdo_slave);
    }

}
