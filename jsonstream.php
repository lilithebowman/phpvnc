<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('memory_limit', '128M');

require('./phpvnc.php');
ignore_user_abort(false);
ob_implicit_flush(true);

session_start();

// Validate session data
if (!isset($_SESSION['host']) || !isset($_SESSION['port']) || 
    !isset($_SESSION['passwd']) || !isset($_SESSION['username']) || 
    !isset($_SESSION['socket'])) {
    header("Content-Type: text/event-stream\n\n");
    $imgObj = new stdClass();
    $imgObj->error = "errauth";
    $imgObj->errstr = "Session data missing";
    $imgObj->errno = -1;
    
    echo "event: error\n";
    echo "data: " . json_encode($imgObj);
    echo "\n\n";
    die();
}

// Get session variables
$host = $_SESSION['host'];
$port = $_SESSION['port'];
$passwd = $_SESSION['passwd'];
$username = $_SESSION['username'];
$socket = $_SESSION['socket'];

$client = new vncClient();
$auth = $client->auth($host, $port, $passwd, $username);

if ($auth === false) {
    header("Content-Type: text/event-stream\n\n");
    $imgObj = new stdClass();
    $imgObj->error = "errauth";
    $imgObj->errstr = $client->errstr;
    $imgObj->errno = $client->errno;
    
    json_encode($imgObj);
    echo "\n\n";
    die();
}

$init = $client->serverInit();
$stat = $client->streamImage('jpeg', $socket, true);

