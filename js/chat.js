
document.addEventListener('DOMContentLoaded', function() {
    const selectUsuarioSender = document.getElementById('selectUsuarioSender');
    const selectUsuarioReciever = document.getElementById('selectUsuarioReciever');
    const form = document.getElementById('formSelecionarUsuarios');
    
    // Função para obter a lista de usuários
    function obterUsuarios() {
        fetch('http://localhost/Adotyx/mensagem.php', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.msg) {
                console.error(data.msg);
            } else {
                data.forEach(usuario => {
                    const optionReciever = document.createElement('option');
                    optionReciever.value = usuario.id;
                    optionReciever.textContent = usuario.nome;
                    selectUsuarioReciever.appendChild(optionReciever);
                });
            }
        })
        .catch(error => console.error('Erro ao obter usuários:', error));
    }

    // Carregar usuários ao carregar a página
    obterUsuarios();

    // Função para enviar mensagem
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const idSender = selectUsuarioSender.value;
        const idReciever = selectUsuarioReciever.value;
        const msg = document.getElementById('msgBox').value;

        fetch('http://localhost/Adotyx-1/mensagem.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `idSender=${idSender}&idReciever=${idReciever}&msg=${msg}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.confirmacao) {
                console.log(data.confirmacao);
                // Limpar o campo de mensagem após o envio
                document.getElementById('msgBox').value = '';
                // Exibir mensagem de sucesso
                Toastify({
                    text: "Mensagem enviada com sucesso!",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "green",
                }).showToast();
            } else {
                console.error(data.msg);
                // Exibir mensagem de erro
                Toastify({
                    text: "Erro ao enviar mensagem.",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    backgroundColor: "red",
                }).showToast();
            }
        })
        .catch(error => {
            console.error('Erro ao enviar mensagem:', error);
            // Exibir mensagem de erro
            Toastify({
                text: "Erro ao enviar mensagem.",
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "red",
            }).showToast();
        });
    });
});
