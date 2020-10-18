<?php
$loader = include __DIR__ . '/../../../vendor/autoload.php';
$loader->setPsr4("", __DIR__ . "/app/");

if (! defined('ROOT')) {
	define('ROOT', __DIR__ . "/app/");
}
if (! defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}