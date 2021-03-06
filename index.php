<?php

/**
 * The MIT License
 *
 * Copyright 2019 Austrian Centre for Digital Humanities.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 */

use zozlak\rest\HttpController;
use zozlak\util\Config;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

$composer = require_once 'vendor/autoload.php';
$composer->addPsr4('acdhOeaw\\', __DIR__ . '/src/acdhOeaw');

$config = new Config('config.ini');
set_error_handler('\zozlak\rest\HttpController::errorHandler');
try {
    // black magic to handle fully-qualified ARCHE URIs
    $url = substr($_SERVER['REDIRECT_URL'], strlen($config->get('baseUrl')));
    $url = explode('/', preg_replace('|^/|', '', $url));
    $_SERVER['CUSTOM_URL'] = array_shift($url);
    $_SERVER['CUSTOM_URL'] .= '/' . urlencode(implode('/', $url));

    $controller = new HttpController('acdhOeaw\\dissService\\mapserver\\endpoint', '', 'CUSTOM_URL');
    $controller->
        setConfig($config)->
        handleRequest();
} catch (Throwable $ex) {
    HttpController::reportError($ex, $config->get('debug'));
}


