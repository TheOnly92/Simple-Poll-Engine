<?php
/*
 * An implementation of a database handler for mysql
 */

class MySQLiHandler implements DB {
    private $connection;

    public function __construct($server, $username, $password, $dbname) {
        $this->connection = new mysqli($server, $username, $password, $dbname);

        if (mysqli_connect_error()) {
            throw new Exception('Failed to connect to database: '.mysqli_connect_error());
        }
    }

    public function Begin() {
        $this->connection->begin_transaction();
    }

    public function Close() {
        $this->connection->close();
    }

    public function Exec($query, $args = array()) {
        return $this->Prepare($query)->Exec($args);
    }

    public function Prepare($query) {
        $stmt = $this->connection->prepare($query);
        if (!$stmt) {
            throw new Exception($this->connection->error);
        }
        return new MySQLiStatement($stmt);
    }

    public function Query($query, $args = array()) {
        return $this->Prepare($query)->Query($args);
    }

    public function QueryRow($query, $args = array()) {
        return $this->Prepare($query)->QueryRow($args);
    }

    public function Commit() {
        $this->connection->commit();
    }

    public function Rollback() {
        $this->connection->rollback();
    }
}

class MySQLiStatement implements Stmt {
    private $stmt;

    public function __construct($stmt) {
        $this->stmt = $stmt;
    }

    public function Close() {
        $this->stmt->close();
    }

    public function Exec($args = array()) {
        if (count($args) > 0) {
            $type = '';
            $fields = array($this->stmt);
            foreach ($args as $i => $arg) {
                $type .= mysqliGetType($arg);
            }
            $fields[1] = $type;
            foreach ($args as $i => $arg) {
                $fields[] = &$args[$i];
            }
            call_user_func_array('mysqli_stmt_bind_param', $fields);
        }
        $this->stmt->execute();
        if ($this->stmt->error != '') {
            throw new Exception($this->stmt->error);
        }
        return new MySQLiResult($this->stmt->insert_id, $this->stmt->affected_rows);
    }

    public function Query($args = array()) {
        if (count($args) > 0) {
            $type = '';
            $fields = array($this->stmt);
            foreach ($args as $i => $arg) {
                $type .= mysqliGetType($arg);
            }
            $fields[1] = $type;
            foreach ($args as $i => $arg) {
                $fields[] = &$args[$i];
            }
            call_user_func_array('mysqli_stmt_bind_param', $fields);
        }
        $this->stmt->execute();
        if ($this->stmt->error != '') {
            throw new Exception($this->stmt->error);
        }
        return new MySQLiRows($this->stmt);
    }

    public function QueryRow($args = array()) {
        if (count($args) > 0) {
            $type = '';
            $fields = array($this->stmt);
            foreach ($args as $i => $arg) {
                $type .= mysqliGetType($arg);
            }
            $fields[1] = $type;
            foreach ($args as $i => $arg) {
                $fields[] = &$args[$i];
            }
            call_user_func_array('mysqli_stmt_bind_param', $fields);
        }
        $this->stmt->execute();
        if ($this->stmt->error != '') {
            throw new Exception($this->stmt->error);
        }
        // if ($this->stmt->num_rows == 0) {
        //     throw new Exception('No rows found');
        // }
        return new MySQLiRow($this->stmt);
    }
}

class MySQLiResult implements Result {
    private $insertId;
    private $affectedRows;
    public function __construct($insertId, $affectedRows) {
        $this->insertId = $insertId;
        $this->affectedRows = $affectedRows;
    }

    public function LastInsertId() {
        return $this->insertId;
    }

    public function RowsAffected() {
        return $this->affectedRows;
    }
}

class MySQLiRow implements Row {
    private $stmt;

    private $currRow;

    public function __construct($stmt) {
        $this->stmt = $stmt;
        $data = $this->stmt->result_metadata();
        $this->currRow = array();
        $fields = array();
        $fields[0] = $this->stmt;
        while ($field = $data->fetch_field()) {
            $fields[] = &$this->currRow[$field->name];
        }
        call_user_func_array('mysqli_stmt_bind_result', $fields);
        $this->stmt->fetch();
    }

    public function Scan() {
        return $this->currRow;
    }
}

class MySQLiRows implements Rows {
    private $stmt;

    private $closed = false;

    private $currRow;

    public function __construct($stmt) {
        $this->stmt = $stmt;
        $data = $this->stmt->result_metadata();
        $this->currRow = array();
        $fields = array();
        $fields[0] = $this->stmt;
        while ($field = $data->fetch_field()) {
            $fields[] = &$this->currRow[$field->name];
        }
        call_user_func_array('mysqli_stmt_bind_result', $fields);
    }

    public function Close() {
        $this->closed = true;
        $this->result->close();
    }

    public function Columns() {
        if ($this->closed) {
            throw new Exception('Rows result has already been closed!');
        }
        $fields = $this->result->fetch_fields();
        $rt = array();
        foreach ($fields as $field) {
            $rt[] = $field->name;
        }
        return $rt;
    }

    public function Err() {

    }

    public function Next() {
        return $this->stmt->fetch();
    }

    public function Scan() {
        return $this->currRow;
    }
}

function mysqliGetType($arg) {
    if (is_int($arg)) {
        return 'i';
    }
    if (is_float($arg)) {
        return 'd';
    }
    return 's';
}
