<?php
session_start();
require_once "./config/utils.php";
require_once "./config/verbs.php";
require_once "./config/header.php";
require_once "./model/Mensagem.php";
require_once "./model/Usuario.php";

$idUsuario = idUsuarioLogado();

if (isMetodo("GET")) {
    try {
        if (parametrosValidos($_GET, ["idDestinatario"])) {
            $idSender = $idUsuario;
            $idDestinatario = $_GET["idDestinatario"];
            $res = Mensagem::retornarMensagensChat($idSender, $idDestinatario);
            output(200, $res);
        } elseif (parametrosValidos($_GET,["contatos"])){
            output(200,Mensagem::retornarContatos($idUsuario));
        }
        
        
        else {
            $usuarios = Usuario::listarUsuarios();
            output(200, $usuarios);
        }
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}

if (isMetodo("POST")) {
    try {
        if (parametrosValidos($_POST, ["idDestinatario", "msg"])) {
            $idSender = $idUsuario;
            $idDestinatario = $_POST["idDestinatario"];
            $msg = $_POST["msg"];
            $res = Mensagem::enviarMensagem($idSender, $idDestinatario, $msg);
            if (!$res) {
                throw new Exception("Erro ao enviar mensagem", 500);
            }
            output(200, ["confirmacao" => "Mensagem enviada com sucesso!"]);
        } else {
            throw new Exception("Parâmetros inválidos para enviar mensagem", 400);
        }
    } catch (Exception $e) {
        output($e->getCode(), ["msg" => $e->getMessage()]);
    }
}
?>
