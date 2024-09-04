<?php
session_start();
require_once "./config/utils.php";
require_once "./config/verbs.php";
require_once "./config/header.php";
require_once "./model/Usuario.php";
require_once "./model/Animal.php";

function returnJson($code, $msg, $details = null) {
    header('Content-Type: application/json');
    http_response_code($code);
    $response = ['msg' => $msg];
    if ($details !== null) {
        $response['details'] = $details;
    }
    echo json_encode($response);
    exit;
}

function getRequestData() {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
    
    if (stripos($contentType, 'application/json') !== false) {
        $content = trim(file_get_contents("php://input"));
        $decoded = json_decode($content, true);
        
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    
    return $_POST;
}

$usuario = idUsuarioLogado();

if (isMetodo("POST")) {
    try {
        $requestData = getRequestData();
        
        $requiredParams = ["nome", "idade", "raca", "sexo", "preco"];
        $missingParams = array_diff($requiredParams, array_keys($requestData));
        
        if (!empty($missingParams)) {
            throw new Exception("Parâmetros ausentes: " . implode(', ', $missingParams), 400);
        }

        // Validate data types
        if (!is_string($requestData["nome"]) || !is_string($requestData["raca"]) || !in_array($requestData["sexo"], ['M', 'F'])) {
            throw new Exception("Tipo de dados inválido para nome, raça ou sexo", 400);
        }
        
        if (!is_numeric($requestData["idade"]) || !is_numeric($requestData["preco"])) {
            throw new Exception("Idade e preço devem ser números", 400);
        }

        $res = Animal::cadastrarAnimal(
            $requestData["nome"],
            intval($requestData["idade"]),
            $requestData["raca"],
            $requestData["sexo"],
            floatval($requestData["preco"]),
            $usuario
        );

        if (!$res) {
            throw new Exception("Erro no cadastro do animal", 500);
        }
        
        returnJson(200, "Cadastro realizado!");
    } catch (Exception $e) {
        error_log($e->getMessage());
        returnJson($e->getCode() ?: 500, $e->getMessage());
    }
} else {
    returnJson(405, "Método não permitido");
}