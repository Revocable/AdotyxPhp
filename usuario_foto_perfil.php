<?php
session_start();

require_once "./config/utils.php";
require_once "./model/Usuario.php";

function returnJson($code, $msg) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['msg' => $msg]);
    exit;
}

$usuario = idUsuarioLogado(); // Implemente sua lógica para obter o ID do usuário logado

if (!$usuario) {
    returnJson(401, "Usuário não está logado");
}

try {
    $fotoPerfil = Usuario::getFotoPerfil($usuario);

    if ($fotoPerfil) {
        returnJson(200, ['foto_perfil' => $fotoPerfil]);
    } else {
        returnJson(404, "Foto de perfil não encontrada para o usuário");
    }
} catch (Exception $e) {
    returnJson(500, "Erro ao obter a foto de perfil: " . $e->getMessage());
}
?>
