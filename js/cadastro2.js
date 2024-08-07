document.getElementById('cadastroForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const urlencoded = new URLSearchParams();

    formData.forEach((value, key) => {
        urlencoded.append(key, value);
    });

    // Adicionando o campo 'signup' para diferenciar entre cadastro e login
    urlencoded.append('signup', 'true');

    const myHeaders = new Headers();
    myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');
    console.log(urlencoded);
    const requestOptions = {
        method: 'POST',
        headers: myHeaders,
        body: urlencoded,
        redirect: 'follow'
    };

    fetch('usuario.php', requestOptions)
        .then(response => response.json())
        .then(data => {
            if (data.msg) {
                alert(data.msg);
                if (data.msg === 'Cadastro realizado com sucesso') {
                    window.location.href = './login.html';
                }
            } else {
                alert('Erro desconhecido.');
            }
        })
        .catch(error => console.error('Erro:', error));
});
