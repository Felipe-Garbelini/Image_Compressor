<?php
// Verifica se o parâmetro 'file' foi passado
if (isset($_GET['file'])) {
    $file = basename($_GET['file']); // Obtém o nome do arquivo

    // Caminho para o diretório onde os arquivos são armazenados temporariamente
    $filePath = sys_get_temp_dir() . '/' . $file;

    // Verifica se o arquivo existe
    if (file_exists($filePath)) {
        // Força o download do arquivo
        header('Content-Description: File Transfer');
        header('Content-Type: ' . mime_content_type($filePath));
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);

        // Remove o arquivo temporário após o download
        unlink($filePath);
        exit;
    } else {
        // Arquivo não encontrado
        http_response_code(404);
        echo 'Arquivo não encontrado.';
        exit;
    }
} else {
    // Parâmetro 'file' não fornecido
    http_response_code(400);
    echo 'Parâmetro de arquivo não fornecido.';
    exit;
}
?>
