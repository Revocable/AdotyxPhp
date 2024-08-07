document.addEventListener('DOMContentLoaded', function() {
    const selectUsuarioReciever = document.getElementById('selectUsuarioReciever');
    const form = document.getElementById('formSelecionarUsuarios');
    const mensagensDiv = document.getElementById('mensagens');

    // Função para obter a lista de usuários
    function obterUsuarios() {
        fetch('mensagem.php', {
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
                // Iniciar atualização automática das mensagens
                iniciarAtualizacaoMensagens();
            }
        })
        .catch(error => console.error('Erro ao obter usuários:', error));
    }

    // Carregar usuários ao carregar a página
    obterUsuarios();

    // Função para enviar mensagem
    form.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const idReciever = selectUsuarioReciever.value;
        const msg = document.getElementById('msgBox').value;

        const urlencoded = new URLSearchParams();
        urlencoded.append('idDestinatario', idReciever);
        urlencoded.append('msg', msg);

        const myHeaders = new Headers();
        myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');

        const requestOptions = {
            method: 'POST',
            headers: myHeaders,
            body: urlencoded,
            redirect: 'follow'
        };

        fetch('mensagem.php', requestOptions)
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
                    // Recarregar mensagens após enviar a mensagem
                    recarregarMensagens(idReciever);
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

    // Função para recarregar mensagens
    function recarregarMensagens(idDestinatario) {
        fetch(`mensagem.php?idDestinatario=${idDestinatario}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.msg) {
                console.error(data.msg);
            } else {
                mensagensDiv.innerHTML = ''; // Limpar mensagens existentes
                data.forEach(mensagem => {
                    const divMensagem = document.createElement('div');
                    divMensagem.classList.add('mensagem');
                    divMensagem.innerHTML = `<strong>${mensagem.nomeRemetente}</strong>: ${mensagem.conteudo} (${mensagem.data_envio})`;
                    mensagensDiv.appendChild(divMensagem);
                });
            }
        })
        .catch(error => console.error('Erro ao recarregar mensagens:', error));
    }

    // Função para iniciar a atualização automática das mensagens a cada 500ms
    function iniciarAtualizacaoMensagens() {
        const idDestinatarioSelecionado = selectUsuarioReciever.value;
        setInterval(() => {
            recarregarMensagens(idDestinatarioSelecionado);
        }, 500);
    }
});
