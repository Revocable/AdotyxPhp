document.addEventListener('DOMContentLoaded', function() {
    const mensagensDiv = document.getElementById('mensagens');
    const formEnviarMensagem = document.getElementById('formEnviarMensagem');
    const listaContatos = document.getElementById('listaContatos');

    // Função para obter contatos do usuário logado
    function obterContatos() {
        fetch('mensagem.php?contatos=true', {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            if (data.msg) {
                console.error(data.msg);
            } else {
                data.forEach(usuario => {
                    const divContato = document.createElement('div');
                    divContato.classList.add('contato');
                    divContato.textContent = usuario.nome;
                    divContato.dataset.idUsuario = usuario.id;
                    divContato.addEventListener('click', function() {
                        const idDestinatario = this.dataset.idUsuario;
                        carregarMensagens(idDestinatario);
                    });
                    listaContatos.appendChild(divContato);
                });
            }
        })
        .catch(error => console.error('Erro ao obter contatos:', error));
    }

    // Carregar contatos ao carregar a página
    obterContatos();

    // Função para carregar mensagens do chat com o usuário selecionado
    function carregarMensagens(idDestinatario) {
        fetch(`mensagem.php?idDestinatario=${idDestinatario}`, {
            method: 'GET'
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); // Exibir resposta da API no console
            
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
                // Rolar a div de mensagens para o final para exibir a última mensagem
                mensagensDiv.scrollTop = mensagensDiv.scrollHeight;
            }
        })
        .catch(error => console.error('Erro ao carregar mensagens:', error));
    }
    
    // Evento de envio de mensagem
    formEnviarMensagem.addEventListener('submit', function(event) {
        event.preventDefault();
        
        const idDestinatario = document.querySelector('.contato.selected')?.dataset.idUsuario;
        const msg = document.getElementById('msgBox').value;

        if (!idDestinatario) {
            console.error('Nenhum destinatário selecionado.');
            return;
        }

        const urlencoded = new URLSearchParams();
        urlencoded.append('idDestinatario', idDestinatario);
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
                    carregarMensagens(idDestinatario);
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

    // Adicionar classe de seleção ao clicar no contato
    listaContatos.addEventListener('click', function(event) {
        const selectedContato = document.querySelector('.contato.selected');
        if (selectedContato) {
            selectedContato.classList.remove('selected');
        }
        const clickedContato = event.target.closest('.contato');
        if (clickedContato) {
            clickedContato.classList.add('selected');
        }
    });

    // Função para iniciar a atualização automática das mensagens a cada 500ms
    function iniciarAtualizacaoMensagens() {
        const idDestinatarioSelecionado = document.querySelector('.contato.selected')?.dataset.idUsuario;
        if (idDestinatarioSelecionado) {
            setInterval(() => {
                carregarMensagens(idDestinatarioSelecionado);
            }, 1500);
        }
    }

    // Iniciar atualização automática após carregar os contatos
    setTimeout(() => {
        iniciarAtualizacaoMensagens();
    }, 1000); // Aguardar um segundo para evitar race conditions
});
