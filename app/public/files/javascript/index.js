document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio do formulário padrão
    document.getElementById('loader').style.display = 'block'; // Exibe o loader
    document.getElementById('result').style.display = 'none'; // Oculta o resultado

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loader').style.display = 'none'; // Oculta o loader

        // Cria um link temporário para iniciar o download
        const downloadLink = document.createElement('a');
        downloadLink.href = data.compressedUrl; // URL da imagem compactada
        downloadLink.download = 'imagem_compactada.' + (data.originalUrl.split(';')[0].split('/')[1] === 'jpeg' ? 'jpg' : 'png'); // Nome padrão do arquivo
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);

        // Exibe as imagens e informações
        document.getElementById('originalImage').src = data.originalUrl;
        document.getElementById('compressedImage').src = data.compressedUrl;
        document.getElementById('originalSize').innerText = formatSize(data.originalSize);
        document.getElementById('compressedSize').innerText = formatSize(data.compressedSize);
        document.getElementById('reductionPercentage').innerText = `Redução: ${data.reductionPercentage}%`;

        // Exibe o container de resultados
        document.getElementById('result').style.display = 'block';
        scrollToResult();
    })
    .catch(error => {
        console.error('Erro:', error);
        document.getElementById('loader').style.display = 'none'; // Oculta o loader em caso de erro
    });
});

function previewImage(event) {
    const input = event.target;
    const reader = new FileReader();
    const preview = document.getElementById('imagePreview');
    
    reader.onload = function(){
        preview.src = reader.result;
        preview.classList.remove('d-none');
    }

    if(input.files && input.files[0]) {
        reader.readAsDataURL(input.files[0]);
    }
}

function formatSize(size) {
    if (size < 1024 * 1024) {
        return `Tamanho: ${(size / 1024).toFixed(2)} KB`;
    } else {
        return `Tamanho: ${(size / (1024 * 1024)).toFixed(2)} MB`;
    }
}

function scrollToResult() {
    const resultSection = document.getElementById('result');
    resultSection.scrollIntoView({ behavior: 'smooth' });
}