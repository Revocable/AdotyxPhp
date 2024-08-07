document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.getElementById('profile-form');
    const saveProfileBtn = document.getElementById('save-profile-btn');
    
    saveProfileBtn.addEventListener('click', function(event) {
        event.preventDefault(); // Evita o envio padrão do formulário
        
        const formData = new FormData(profileForm);
        
        fetch('upload.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Erro ao enviar a foto de perfil');
            }
            return response.json();
        })
        .then(data => {
            Toastify({
                text: 'Foto de perfil atualizada com sucesso',
                backgroundColor: 'linear-gradient(to right, #00b09b, #96c93d)',
                className: 'info',
            }).showToast();
            
            if (data.msg && data.msg.foto_perfil) {
                const fotoPerfilPath = data.msg.foto_perfil;
                
                // Exemplo: definir a imagem de perfil no elemento HTML
                const profilePicture = document.getElementById('profile-picture');
                if (profilePicture) {
                    profilePicture.src = fotoPerfilPath;
                    profilePicture.alt = 'Foto de Perfil';
                }
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Toastify({
                text: 'Erro ao atualizar a foto de perfil',
                backgroundColor: 'linear-gradient(to right, #ff5f6d, #ffc371)',
                className: 'error',
            }).showToast();
        });
    });
    
    // Carregar a foto de perfil assim que a página carregar
    function getFotoPerfil() {
        fetch('usuario_foto_perfil.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao obter a foto de perfil');
                }
                return response.json();
            })
            .then(data => {
                if (data.msg && data.msg.foto_perfil) {
                    const fotoPerfilPath = data.msg.foto_perfil;
                    
                    // Exemplo: definir a imagem de perfil no elemento HTML
                    const profilePicture = document.getElementById('profile-picture');
                    if (profilePicture) {
                        profilePicture.src = fotoPerfilPath;
                        profilePicture.alt = 'Foto de Perfil';
                    }
                } else {
                    console.log('Foto de perfil não encontrada para o usuário');
                }
            })
            .catch(error => {
                console.error('Erro ao obter a foto de perfil:', error);
            });
    }
    
    // Chama a função para obter o caminho da foto de perfil assim que a página carregar
    getFotoPerfil();
});
