<?php

$config = array();

// ------------ Database Configuration ------------
$config['database'] = array();
$config['database']['type'] = 'mysql';          // Database type ('mysql' or 'pgsql')
$config['database']['server'] = 'localhost';    // Database server
$config['database']['username'] = 'root';       // Database username
$config['database']['password'] = '';           // Database password
$config['database']['dbname'] = 'poll';         // Database name
$config['database']['prefix'] = 'spe_';         // Prefix for table names

// ------------ Admin Configuration ---------------
$config['admin'] = array();
$config['admin']['type'] = 'default';           // Admin authentication type ('default' or 'custom')
// $config['admin']['interactor'] =                // Admin authentication interactor, must implement the interface AdminInteractorInterface
$config['admin']['username'] = 'admin';
$config['admin']['password'] = 'admin';

$config['upload_dir'] = POLL_SCRIPT_ROOT.'/uploads/';   // Directory to upload images