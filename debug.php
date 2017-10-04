<?php

use Tracy\Debugger;
use Tracy\Dumper;

function d(...$params)
{
	Debugger::dump(...$params);
}

function bd(...$params)
{
	Debugger::barDump(...$params);
}

function rd($value, $depth = 5)
{
	static $n;
	static $garbage = [];

	if ($n === null) {
		$message = date('Y-m-d H:i:s') . " ----------------------------------------------------------------------------------------------------------------\n";
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
		socket_connect($socket, '127.0.0.1', 6666) or die("Could not connect to server\n");
		socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
		$n = 0;
	}

	$options = [
		Dumper::DEPTH => $depth, //Debugger::$maxDepth,
		Dumper::TRUNCATE => 1000, //Debugger::$maxLength,
		Dumper::LOCATION => false, //Debugger::$showLocation,
	];
	$dump = Dumper::toTerminal($value, $options);
	$trace = debug_backtrace();
	$message = trim($dump) . "\nin " . ($trace[0]['file'] ?? '?') . ':' . ($trace[0]['line'] ?? '?') .  " ($n)\n";

	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die("Could not create socket\n");
	socket_connect($socket, '127.0.0.1', 6666) or die("Could not connect to server\n");
	socket_write($socket, $message, strlen($message)) or die("Could not send data to server\n");
	$garbage[] = $socket;
	usleep(1000);
	$n++;
}

?>