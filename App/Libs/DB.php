<?php

namespace App\Libs;

use PDO;

/*
* Singleton DB Class
*/
class DB
{
    protected static $instance = null;
    protected function __construct() {}
    protected function __clone() {}

    public static function instance()
    {
        if (self::$instance === null) {
            $opt = array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            );
            $dsn = 'mysql:host=' . CONFIG['DB_HOST'] . ';dbname=' . CONFIG['DB_NAME'] . ';charset=' . CONFIG['DB_CHAR'];
		try {
            self::$instance = new PDO($dsn, CONFIG['DB_USER'], CONFIG['DB_PASS'], $opt);
        } catch (\PDOException $e) {
            die('The Database Details Are Incorrect');
        }
        }
        return self::$instance;
    }

    public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

    // Chain Prepared
    public static function run($sql, $args = [])
    {
        if (!$args) {
            return self::instance()->query($sql);
        }
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
        
    // select - multiple rows fetchall() eg ('tablename', '*/col', '[]/''', )
    public static function newSelect($table, $columns = '*', $where = '', $values = '', $other = '')
    {
        // TABLE, COLS, where, values, other
        if (!is_array($where)) {
            $sql = "SELECT $columns FROM `$table` $values";
            $stmt = DB::run($sql);
        } else {
            // setup where
            $whereDetails = null;
            $i = 0;
            foreach ($where as $key) {
                $whereDetails .= $i == 0 ? "$key ?" : " AND $key ?";
                $i++;
            }
            $sql = "SELECT $columns FROM `$table` WHERE $whereDetails $other";
            $stmt = DB::run($sql, $values);
        }
        return  $stmt;
    }
            
    // select - multiple rows fetchall() eg ('tablename', '*/col', '[]/''', )
    public static function fetchAll($table, $columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = self::newSelect($table, $columns, $where, $values, $other);
        return  $stmt;
    }
                
    // select - multiple rows fetchall() eg ('tablename', '*/col', '[]/''', )
    public static function fetchColumn($table, $columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = self::newSelect($table, $columns, $where, $values, $other)->fetchColumn();
        return  $stmt;
    }
                
    // select - multiple rows fetchall() eg ('tablename', '*/col', '[]/''', )
    public static function fetchRow($table, $columns = '*', $where = '', $values = '', $other = '')
    {
        $stmt = self::newSelect($table, $columns, $where, $values, $other)->fetch(PDO::FETCH_ASSOC);
        return  $stmt;
    }

    // update = careful merge removes duplicate array key ???
    public static function update($table, $columns, $where, $values)
    {
        $fieldDetails = null;
        foreach ($columns as $key) {
            $fieldDetails .= "$key = ?,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key) {
            $whereDetails .= $i == 0 ? "$key ?" : " AND $key ?";
            $i++;
        }
        $stmt = DB::run("UPDATE $table SET $fieldDetails WHERE $whereDetails", $values);
        return $stmt->rowCount();
    }

    // update = careful merge removes duplicate array key ???
    public static function updateset($table, $data, $where)
    {
        //merge data and where together
        $collection = array_merge($data, $where);
        //collect the values from collection
        $values = array_values($collection);
        //setup fields
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = ?,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            $whereDetails .= $i == 0 ? "$key = ?" : " AND $key = ?";
            $i++;
        }
        $stmt = self::run("UPDATE $table SET $fieldDetails WHERE $whereDetails", $values);
        return $stmt->rowCount();
    }

    // update = careful merge removes duplicate array key ???
    public static function updateWhereIn($table, $data, $ids, $col = 'id')
    {
        //merge data and where together
        $collection = $data;
        //collect the values from collection
        $values = array_values($collection);
        //setup fields
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = ?,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');
        $stmt = self::run("UPDATE $table SET $fieldDetails WHERE $col IN ($ids)", [$values]);
        return $stmt->rowCount();
    }

    // insert
    public static function insert($table, $data)
    {
        //add columns into comma seperated string
        $columns = implode(',', array_keys($data));
        //get values
        $values = array_values($data);
        $placeholders = array_map(function ($val) {
            return '?';
        }, array_keys($data));
        //convert array into comma seperated string
        $placeholders = implode(',', array_values($placeholders));

        $id = self::run("INSERT INTO $table ($columns) VALUES ($placeholders)", $values);
        return self::lastInsertId();
    }

    // delete
    public static function delete($table, $where, $limit = false)
    {
        //collect the values from collection
        $values = array_values($where);
        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            $whereDetails .= $i == 0 ? "$key = ?" : " AND $key = ?";
            $i++;
        }
        //if limit is a number use a limit on the query
        if (is_numeric($limit)) {
            $limit = "LIMIT $limit";
            $stmt = self::run("DELETE FROM $table WHERE $whereDetails $limit", $values);
            return $stmt->rowCount();
        } else {
            $stmt = self::run("DELETE FROM $table WHERE $whereDetails", $values);
            return $stmt->rowCount();
        }
    }
    
    // delete wher IN ()
    public static function deleteByIds($table, $where, $ids, $col = 'id', $values = null)
    {
        if (is_array($where)) {
            //collect the values for prepared statement?
            //$values = array_values($where);
            //setup where
            $whereDetails = null;
            $i = 0;
            foreach ($where as $key) {
                $whereDetails .= $i == 0 ? "$key ?" : " AND $key ?";
                $i++;
            }
            $stmt = self::run("DELETE FROM $table WHERE $whereDetails AND $col IN ($ids)", $values);
            return $stmt->rowCount();
        } else {
            $stmt = self::run("DELETE FROM $table WHERE $where IN ($ids)");
            return $stmt->rowCount();
        }
    }
    
    // truncate table
    public static function truncate($table)
    {
        self::run("TRUNCATE TABLE $table");
    }

}