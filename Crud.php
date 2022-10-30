<?php

include_once('DbConfig.php');
include_once('appPDO.php');

class Crud extends appPDO {
    private $conn;
    public function __construct()
    {
        $this->conn = getdbconnection();
    }
    public function create($data_array,$table){
        $columns = implode(',',array_keys($data_array));
        $place_holders = ':'. implode(',:',array_keys($data_array));
        $sql = "INSERT INTO $table ($columns) VALUES ($place_holders)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data_array);
        return $this->conn->lastInsertId();
    }

    public function read($sql,$params = null, $fetch_type = 'all'){
        /**
         * Restricted passed $params to only those found in the query or the number of unamed params
         */
        $pdo_params = $this->filter_pdo_params($sql, $params);

        /**
         * Explode PDO params that are arrays into multiple values "in (a, b, c)"
         */
        $this->explode_pdo_arrays($sql, $pdo_params);

        /**
         * Execute prepared statement
         */
        $stmt = $this->conn->prepare($sql);
        $status = $stmt->execute($pdo_params);

        switch ($fetch_type) {
            case 'column':
                $res = $stmt->fetchColumn();
                break;
            case 'row':
                $res = $stmt->fetch(PDO::FETCH_ASSOC);
                break;
            case 'query':
                $res = $status;
                break;
            case 'affected':
                /* Similar to 'query' but explicitly returns rows affected. In this mode 0 is should not be considered an error. */
                $res = $stmt->rowCount();
                break;
            default:
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                break;
        }

        $stmt->closeCursor();
        return $res;
    }

    public function update($sql,$params = null, $fetch_type = 'query'){
        /**
         * Restricted passed $params to only those found in the query or the number of unamed params
         */
        $pdo_params = $this->filter_pdo_params($sql, $params);

        /**
         * Explode PDO params that are arrays into multiple values "in (a, b, c)"
         */
        $this->explode_pdo_arrays($sql, $pdo_params);

        /**
         * Execute prepared statement
         */
        $stmt = $this->conn->prepare($sql);
        $status = $stmt->execute($pdo_params);

        $stmt->closeCursor();
        return $status;
    }

    public function delete($sql,$params = null, $fetch_type = 'query'){
        /**
         * Restricted passed $params to only those found in the query or the number of unamed params
         */
        $pdo_params = $this->filter_pdo_params($sql, $params);

        /**
         * Explode PDO params that are arrays into multiple values "in (a, b, c)"
         */
        $this->explode_pdo_arrays($sql, $pdo_params);

        /**
         * Execute prepared statement
         */
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($pdo_params);
    }

}