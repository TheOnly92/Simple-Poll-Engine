<?php

error_reporting(E_ALL);
ini_set('display_errors', 'on');

require_once('init.php');

checkInstalled($pollDb, $config);
$pollRepository = new DbPollRepository($pollDb, $config['database']['prefix']);
$requestRepository = new DefaultRequestRepository();
if ($config['admin']['type'] == 'default') {
    $adminInteractor = new AdminInteractor($config['admin']);
} elseif (isset($config['admin']['interactor']) && ($config['admin']['interactor'] instanceof AdminInteractorInterface)) {
    $adminInteractor = $config['admin']['interactor'];
}
$pollInteractor = new PollInteractor($pollRepository, $requestRepository);

$adminHandler = new AdminHandler($pollInteractor, $adminInteractor);
$indexHandler = new IndexHandler($pollInteractor);
if (!isset($_GET['c'])) $_GET['c'] = '';
if (!isset($_GET['a'])) $_GET['a'] = '';
switch ($_GET['c']) {
    case 'admin':
        $adminHandler->Route($_GET['a']);
        break;
    case 'index':
    default:
        $indexHandler->Route($_GET['a']);
        break;
}