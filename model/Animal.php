<?php
require_once (__DIR__ . "/../config/Conexao.php");

class Animal
{
    public static function cadastrarAnimal($nome, $idade, $raca, $sexo, $preco, $idDono)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("INSERT INTO animal (nome, idade, raca, sexo, preco) VALUES (?, ?, ?, ?, ?)");
            $result = $stmt->execute([$nome, $idade, $raca, $sexo, $preco]);
            
            if (!$result) {
                $errorInfo = $stmt->errorInfo();
                error_log("SQL Error: " . implode(", ", $errorInfo));
                throw new Exception("Erro ao inserir no banco de dados: " . $errorInfo[2]);
            }
            
            $lastInsertId = $conexao->lastInsertId();
            

            if ($idDono) {
                $stmtTutor = $conexao->prepare("INSERT INTO tutor (idAnimal, idTutor) VALUES (?, ?)");
                $resultTutor = $stmtTutor->execute([$lastInsertId, $idDono]);
                
                if (!$resultTutor) {
                    $errorInfo = $stmtTutor->errorInfo();
                    error_log("SQL Error (tutor): " . implode(", ", $errorInfo));
                    throw new Exception("Erro ao inserir tutor no banco de dados: " . $errorInfo[2]);
                }
            }
            
            return $lastInsertId;
        } catch (Exception $e) {
            error_log("Exception in cadastrarAnimal: " . $e->getMessage());
            throw $e;
        }
    }
}