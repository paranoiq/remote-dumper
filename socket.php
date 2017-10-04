<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit (0); 

$address = '127.0.0.1'; 
$port = 6666;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_bind($sock, $address, $port) or die('Could not bind to address');
socket_listen($sock, 20);
socket_set_nonblock($sock);

$connections = [];
while (true) {
    if ($newConnection = socket_accept($sock)) {
        if (is_resource($newConnection)) {
        	//socket_write($newConnection, '>>');
            $connections[] = $newConnection;
        } 
    } 

    foreach ($connections as $i => $connection) {
        $message = socket_read($connection, 1000000);
        if ($message === false) {
            socket_close($connection);
            unset($connections[$i]);
        } elseif ($message) {
            echo $message;
        }
    } 

    usleep(20);
}


socket_close($sock);
