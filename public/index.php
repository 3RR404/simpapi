<?php 

require realpath( __DIR__ . "/../bootstrap.php" );

use Src\Controller\PostController;
use Src\Controller\UserController;
use Src\Seeds\PostSeed;
use Src\Seeds\UserSeed;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri );

$newUserTable = new UserSeed( 'user' );
$newUserTable = new PostSeed( 'post' );

$controller = false;
$requestMethod = $_SERVER["REQUEST_METHOD"];


if( isset( $uri[2] ) )
{
    switch( $uri[2] )
    {
        case 'user' :
            $userId = null;
            if ( isset( $uri[3] ) )
            {
                $userId = (int) $uri[3];
            }
            $controller = new UserController($dbConnection, $requestMethod, $userId);
        break;
        case 'post' :
            $postId = null;
            if ( isset( $uri[3] ) )
            {
                $postId = (int) $uri[3];
            }
            $controller = new PostController($dbConnection, $requestMethod, $postId);
        break;
        default : 
            header("HTTP/1.1 404 Not Found");
            exit();
    }
}

// pass the request method and user ID to the PersonController and process the HTTP request:
if( $controller )
{
    $controller->processRequest();
}
