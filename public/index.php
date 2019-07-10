<?php

if (!isset($_SERVER['REQUEST_URI'])) {
    http_response_code(404);
    exit;
}

require_once '../module/controller.php';

$controller = new Controller();

switch ($_SERVER['REQUEST_URI']) {
    
    case '/application/set-mode':     

        $controller->setMode();
        break;

    case '/application/get-modes':
        $controller->getModes();
        break;
        
    case '/response':
        try {
            $controller->getResponse();
        } catch (Exception $e) {
            http_response_code(503);
            echo "Error. " . $e->getMessage();
            exit;
        }
        break;

    case '/application/get-results':
        $controller->getResults();
        break;
    
    case '/application/get-mode':
        $controller->getMode();
        break;
        
    default:
        http_response_code(404);
        exit;
        
}
    
    
    