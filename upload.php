<?php
session_start();

require_once "./config/utils.php"; // Ajuste o caminho conforme necessário
require_once "./model/Usuario.php"; // Ajuste o caminho conforme necessário

$targetDir = "./uploads/profile-pictures/";

function returnJson($code, $msg) {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode(['msg' => $msg]);
    exit;
}

// Verifica se foi enviado um arquivo
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    returnJson(400, 'Erro ao enviar a foto de perfil');
}

// Obtém o ID do usuário logado (substitua conforme a sua lógica de autenticação)
$idUsuario = idUsuarioLogado(); // Implemente sua lógica para obter o ID do usuário

// Gera um nome único para o arquivo com o ID do usuário na frente
$fileName = $idUsuario . '_' . uniqid('profile_') . '_' . basename($_FILES['file']['name']);
$uploadPath = $targetDir . $fileName;

// Remove a imagem anterior do usuário, se existir
$oldImagePath = glob($targetDir . $idUsuario . '_profile_*');
if ($oldImagePath) {
    unlink($oldImagePath[0]); // Remove apenas a primeira imagem encontrada (deve ser apenas uma)
}

// Função para redimensionar a imagem
function resizeImage($file, $maxHeight) {
    list($width, $height, $type) = getimagesize($file);

    if ($height > $maxHeight) {
        $ratio = $maxHeight / $height;
        $newWidth = $width * $ratio;
        $newHeight = $maxHeight;

        $src = null;
        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($file);
                break;
            case IMAGETYPE_GIF:
                $src = imagecreatefromgif($file);
                break;
            default:
                return false;
        }

        $dst = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        switch ($type) {
            case IMAGETYPE_JPEG:
                imagejpeg($dst, $file);
                break;
            case IMAGETYPE_PNG:
                imagepng($dst, $file);
                break;
            case IMAGETYPE_GIF:
                imagegif($dst, $file);
                break;
        }

        imagedestroy($src);
        imagedestroy($dst);
    }

    return true;
}

// Move o arquivo do diretório temporário para o diretório de upload e redimensiona
if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadPath) || !resizeImage($uploadPath, 500)) {
    returnJson(500, 'Erro ao salvar ou redimensionar a foto de perfil');
}

$res = Usuario::salvarFotoPerfil($idUsuario, $uploadPath);

if ($res) {
    // Retorna o caminho da foto de perfil para ser utilizado na interface
    echo json_encode(['msg' => 'Foto de perfil enviada e atualizada com sucesso.', 'foto_perfil' => $uploadPath]);
} else {
    returnJson(500, 'Erro ao atualizar a foto de perfil na base de dados.');
}
?>
