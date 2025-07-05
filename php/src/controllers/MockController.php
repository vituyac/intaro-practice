<?php

namespace App\controllers;


/**
 * Коллекция методов для генерации данных
 */
class MockController {
    
    /**
     * Used to 'login' user without actual login
     * GET: id
     */
    function mockLogin(){
        if (empty($_GET['id']) || (int)$_GET['id'] < 0){
            http_response_code(400);
        }
        else {
            $_SESSION['user_id'] = (int)$_GET['id'];
            http_response_code(200);
        }
    }
    function checkUser(){
        header('Content-type: application/json');
        if (empty($_SESSION['user_id'])){
            http_response_code(403);
        }
        echo json_encode(['user_id' => $_SESSION['user_id']]);
    }
}