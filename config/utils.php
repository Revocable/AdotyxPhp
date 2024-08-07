<?php

function parametrosValidos($metodo, $lista)
{
    $obtidos = array_keys($metodo);
    $nao_encontrados = array_diff($lista, $obtidos);
    if (empty($nao_encontrados)) {
        foreach ($lista as $p) {
            if (empty(trim($metodo[$p]))) {
                return false;
            }
        }
        return true;
    }
    return false;
}


function isMetodo($metodo)
{
    if (!strcasecmp($_SERVER['REQUEST_METHOD'], $metodo)) {
        return true;
    }
    return false;
}


function output($codigo, $msg)
{
    http_response_code($codigo);
    echo json_encode($msg);
    exit;
}


// Função que retorna o valor do campo "usuario" da sessão, se houver. False, caso não exista.
function idUsuarioLogado() {
    if(parametrosValidos($_SESSION, ["usuario"])) {
        return $_SESSION["usuario"];
    } 
    return false;
}

// Função que define o valor do campo "usuario" da sessão
function setIdUsuarioLogado($id) {
    $_SESSION["usuario"] = $id;
}

