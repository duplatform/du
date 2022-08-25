<?php

use Symfony\Component\Yaml\Yaml;
use Jubayed\A2\Pages\A2Controller;
use Jubayed\A2\Pages\FileController;
use Jubayed\A2\Pages\PageController;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        // may also be using PUT, PATCH, HEAD etc
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
}

/**
 * Load vendor autoload.php
 * 
 */
if (file_exists(__DIR__ . '/../../../../autoload.php')) {
    require __DIR__ . '/../../../../autoload.php';
} else if(file_exists(__DIR__ . '/../../../autoload.php')){
    require __DIR__ . '/../../../autoload.php';
}else{
    require __DIR__ . '/../vendor/autoload.php';
}


// set 404
if(!file_exists(a2_path('manifest.yaml'))){
    return abort(404);
}

$data = Yaml::parseFile(a2_path('manifest.yaml'));

putenv("APP_NAME={$data['APP_NAME']}");
putenv("A2_TECHNOLOGY={$data['A2_TECHNOLOGY']}");
putenv("A2_TOKEN={$data['A2_TOKEN']}");
putenv("API_SERVER={$data['API_SERVER']}");

header('A2-TOKEN: ' . getenv('A2_TOKEN'));
header('A2-TECHNOLOGY: ' . getenv('A2_TECHNOLOGY'));
header('A2-APPNAME: ' . getenv('APP_NAME'));

echo  getenv('APP_NAME');
exit();

// routes
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? ''
);

$uri = rtrim($uri, '/');
/*
|--------------------------------------------------------------------------
| duplatform
|--------------------------------------------------------------------------
|
*/
if($uri == '/du'){

    $class = new A2Controller;
    echo $class->index();
}else if( str_contains($uri, '/static') ){
    $param = explode('/', $uri);

    if(count($param ) != 4){ exit();  }

    $class = new A2Controller;
    echo $class->static($param[2], $param[3]);
} else if($uri == '/finder'){

    $class = new FileController;
    echo $class->finder();
} else if($uri == '/files/content'){
// // url ?path=/nay/any

    $class = new FileController();
    echo $class->getContent();
}else if($uri == '/files' && $_SERVER["REQUEST_METHOD"] == "POST" ){
// // store files

    $class = new FileController();
    echo $class->save();
} else if($uri == '/file/download' && $_SERVER["REQUEST_METHOD"] == "POST" ){
    $class = new FileController();
    echo $class->downloadFile();
} else {
    $class = new PageController();
    echo $class->display();
}

exit();
?>