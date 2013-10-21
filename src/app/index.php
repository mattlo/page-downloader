<?php
# bootstrap

// auto loader
function __autoload($class) {
    $parts = explode('\\', $class);
    require join('/', $parts) . '.php';
}

// treat warnings as exceptions
function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
}

//set_error_handler('handleError');
//error_reporting(E_ERROR | E_PARSE); // DOMDocument warnings on mal-formed html

try {
	$app = new PageDownloader\Main;
	$app->init();
} catch (\Exception $e) {
	echo '<pre>' . $e . '</pre>';
}