<?php

session_start();

require_once "./config/utils.php";
require_once "./config/verbs.php";
require_once "./config/header.php";
require_once "./model/Mensagem.php";
require_once "./model/Usuario.php";

function returnJson($code, $msg) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['msg' => $msg]);
    exit;
}

$usuario = idUsuarioLogado();

if (isMetodo("GET")) {
    try {
        if (parametrosValidos($_GET, ["logout"])) {
            if (!$usuario) {
                throw new Exception("Usuário não está logado", 401);
            }
            session_destroy();
            returnJson(200, "Deslogado com sucesso");
        } else {
            $usuario = Usuario::listarUsuarios();
            returnJson(200, $usuario);
        }
    } catch (Exception $e) {
        returnJson($e->getCode(), $e->getMessage());
    }
}

if (isMetodo("POST")) {
    try {
        $usuario = idUsuarioLogado();

        if (parametrosValidos($_POST, ["signup", "login", "nome", "senha"])) {
            if (Usuario::existLogin($_POST["login"])) {
                throw new Exception("Este login já existe", 400);
            }

            $res = Usuario::cadastrarUsuario($_POST["nome"], $_POST["senha"], $_POST["login"]);
            if (!$res) {
                throw new Exception("Erro no cadastro", 500);
            }
            returnJson(200, "Cadastro realizado com sucesso");
        }

        if (parametrosValidos($_POST, ["login", "senha"])) {
            if ($usuario) {
                throw new Exception("Você está logado e não pode fazer isso", 400);
            }

            $res = Usuario::getLogin($_POST["login"], $_POST["senha"]);
            if (!$res) {
                throw new Exception("Login ou senha inválida", 400);
            }

            setIdUsuarioLogado($res["id"]);
            returnJson(200, "Login realizado com sucesso");
        }

        throw new Exception("Requisição não reconhecida", 400);

        
    } catch (Exception $e) {
        returnJson($e->getCode(), $e->getMessage());
    }
} else {
    returnJson(405, "Método não permitido");
}

?>
