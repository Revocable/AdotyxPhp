<?php

require_once (__DIR__ . "/../config/Conexao.php");

class Mensagem
{
    public static function enviarMensagem($idUsuarioSender, $idUsuarioReciever, $mensagem)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("INSERT INTO chat (idRemetente, idDestinatario, conteudo) VALUES (?, ?, ?)");
            $stmt->execute([$idUsuarioSender, $idUsuarioReciever, $mensagem]);

            return $stmt->rowCount() === 1;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public static function retornarMensagensChat($idUsuarioSender, $idUsuarioReciever)
    {
        try {
            $conexao = Conexao::getConexao();
            $stmt = $conexao->prepare("
                SELECT c.*, 
                       uSender.nome AS nomeRemetente,
                       uReceiver.nome AS nomeDestinatario
                  FROM chat c
                  INNER JOIN usuario uSender ON c.idRemetente = uSender.id
                  INNER JOIN usuario uReceiver ON c.idDestinatario = uReceiver.id
                 WHERE (c.idRemetente = ? AND c.idDestinatario = ?) 
                    OR (c.idDestinatario = ? AND c.idRemetente = ?) 
                 ORDER BY c.data_envio
            ");
            $stmt->execute([$idUsuarioSender, $idUsuarioReciever, $idUsuarioReciever, $idUsuarioSender]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return [];
        }
    }
    
    public static function retornarContatos($idRemetente) {
        try {
            $conexao = Conexao::getConexao();
    
            // Consulta SQL utilizando INNER JOIN para obter os contatos
            $sql = "SELECT DISTINCT u.id, u.nome
                    FROM usuario u
                    INNER JOIN chat c ON u.id = c.idDestinatario
                    WHERE c.idRemetente = :idRemetente
                    ORDER BY u.nome";
    
            $stmt = $conexao->prepare($sql);
            $stmt->bindParam(':idRemetente', $idRemetente, PDO::PARAM_INT);
            $stmt->execute();
    
            $contatos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $contatos;
        } catch (PDOException $e) {
            throw new Exception("Erro ao retornar contatos: " . $e->getMessage(), 500);
        }
    }
    
}

?>
