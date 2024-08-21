<?php
// Função para compactar a imagem mantendo a resolução
function compressImage($source, $destination, $quality) {
    // Obtém informações da imagem
    $info = getimagesize($source);

    // Verifica o tipo de imagem
    if ($info['mime'] == 'image/jpeg') {
        // Compacta JPEG
        $image = imagecreatefromjpeg($source);
        imagejpeg($image, $destination, $quality);
        imagedestroy($image);
    } elseif ($info['mime'] == 'image/png') {
        // Otimiza PNG diretamente com pngquant, sem salvar temporariamente com GD
        shell_exec("pngquant --quality=65-80 --force --output '$destination' '$source'");
    } elseif ($info['mime'] == 'image/gif') {
        // Compacta GIF
        $image = imagecreatefromgif($source);
        imagegif($image, $destination);
        imagedestroy($image);
    } else {
        throw new Exception('Tipo de imagem não suportado.');
    }
}

// Verifica se o formulário foi enviado e se o arquivo foi carregado com sucesso
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $error = $_FILES['image']['error'];

    if ($error === UPLOAD_ERR_OK) {
        // Verifica o tamanho do arquivo
        if ($_FILES['image']['size'] > 50 * 1024 * 1024) { // 50MB
            die('O arquivo é muito grande. O tamanho máximo permitido é 50MB.');
        }

        // Configurações
        $uploadedFile = $_FILES['image']['tmp_name'];
        $originalFileName = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME);
        $originalFileExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $compressedFileName = $originalFileName . '_compress.' . $originalFileExtension;
        $tempFile = sys_get_temp_dir() . '/' . $compressedFileName;

        // Define a qualidade da compactação (para JPEG, é de 0 a 100)
        $quality = 25; 

        try {
            // Compacta a imagem
            compressImage($uploadedFile, $tempFile, $quality);

            // Responde com a URL do arquivo para download
            $compressedFilePath = '/path/to/temp/' . $compressedFileName; // Atualize o caminho conforme necessário
            echo json_encode([
                'originalUrl' => 'data:image/' . $originalFileExtension . ';base64,' . base64_encode(file_get_contents($uploadedFile)),
                'compressedUrl' => 'data:image/' . $originalFileExtension . ';base64,' . base64_encode(file_get_contents($tempFile)),
                'originalSize' => filesize($uploadedFile),
                'compressedSize' => filesize($tempFile),
                'reductionPercentage' => round((1 - filesize($tempFile) / filesize($uploadedFile)) * 100, 2)
            ]);
            exit;
        } catch (Exception $e) {
            die('Erro ao compactar a imagem: ' . $e->getMessage());
        }
    } else {
        die('Erro no upload do arquivo.');
    }
} else {
    die('Método de requisição inválido ou arquivo não enviado.');
}
?>
