document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profile-form');

    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);

            const requestOptions = {
                method: 'POST',
                body: formData,
            };

            fetch('upload.php', requestOptions)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro ao enviar a foto de perfil');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(data.msg); // Exibe mensagem de sucesso ou erro

                    // Redireciona para outra página após o upload bem-sucedido
                    if (data.msg === 'Foto de perfil enviada e atualizada com sucesso.') {
                        window.location.href = './outra-pagina.html';
                    }
                })
                .catch(error => {
                    console.error('Erro ao enviar a foto de perfil:', error);
                    alert('Erro ao enviar a foto de perfil');
                });
        });
    } else {
        console.error('Elemento com ID "profile-form" não encontrado.');
    }
});
