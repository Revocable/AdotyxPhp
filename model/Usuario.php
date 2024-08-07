<?php

require_once (__DIR__ . "/../config/Conexao.php");

class Usuario
{
    public static function cadastrarUsuario($username, $password, $email)
    {
        try {
            $conexao = Conexao::getConexao();
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $conexao->prepare("INSERT INTO usuario (nome, senha, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hashedPassword, $email]);
            return $stmt->rowCount() === 1;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function listarUsuarios()
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->query("SELECT * FROM usuario");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function existLogin($email)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("SELECT COUNT(*) FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getLogin($email, $password)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("SELECT * FROM usuario WHERE email = ?");
            $stmt->execute([$email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['senha'])) {
                return $usuario;
            } else {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    public static function salvarFotoPerfil($idUsuario, $imageData)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("UPDATE usuario SET foto_perfil = ? WHERE id = ?");
            return $stmt->execute([$imageData, $idUsuario]);
        } catch (Exception $e) {
            return false;
        }
    }

    public static function getFotoPerfil($idUsuario)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("SELECT foto_perfil FROM usuario WHERE id = ?");
            $stmt->execute([$idUsuario]);
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return null;
        }
}

}