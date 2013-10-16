<?php
/*
 * This file defines the interface for interacting with databases.
 */

interface DB {
    public function __construct($server, $username, $password, $dbname);

    // Begins a transaction
    public function Begin();

    // Closes the DB connection
    public function Close();

    // Executes a query (usually for UPDATEs, INSERTs and DELETEs)
    public function Exec($query, $args);

    // Prepare a statement query
    public function Prepare($query);

    // Runs a query that might return multiple results
    public function Query($query, $args);

    // Runs a query that returns a single row
    public function QueryRow($query, $args);

    // Commits a transaction
    public function Commit();

    // Rolls back a transaction
    public function Rollback();
}

interface Row {
    // Returns an array
    public function Scan();
}

interface Rows {
    // Closes the row, prevents further scans
    public function Close();

    // Returns the columns
    public function Columns();

    // Returns if there is any error
    public function Err();

    // Iterates to the next row
    public function Next();

    // Returns an array of a row
    public function Scan();
}

interface Stmt {
    // Closes the statement
    public function Close();

    // Executes a statement with arguments $args
    public function Exec($args);

    // Executes a statement with argument $args
    public function Query($args);

    // Executes a statement which will return only 1 row with $args
    public function QueryRow($args);
}

interface Result {
    public function LastInsertId();
    public function RowsAffected();
}