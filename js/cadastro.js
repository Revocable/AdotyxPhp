document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('login-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const urlencoded = new URLSearchParams();

        formData.forEach((value, key) => {
            urlencoded.append(key, value);
        });

        const myHeaders = new Headers();
        myHeaders.append('Content-Type', 'application/x-www-form-urlencoded');
        myHeaders.append('Cookie', 'PHPSESSID=5p2817l0b3cdj6de2fahclhvdv'); // Adicione seu cookie PHPSESSID aqui, se necessário

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
                    Toastify({
                        text: data.msg,
                        duration: 3000,
                        close: true,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#4CAF50'
                    }).showToast();

                    if (data.msg === 'Login realizado com sucesso') {
                        setTimeout(function() {
                            window.location.href = './account.html';
                        }, 2000); // Tempo de espera antes do redirecionamento (2 segundos neste exemplo)
                    }
                } else {
                    Toastify({
                        text: 'Erro ao realizar login',
                        duration: 3000,
                        close: true,
                        gravity: 'top',
                        position: 'right',
                        backgroundColor: '#ff6347'
                    }).showToast();
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                Toastify({
                    text: 'Erro ao realizar login',
                    duration: 3000,
                    close: true,
                    gravity: 'top',
                    position: 'right',
                    backgroundColor: '#ff6347'
                }).showToast();
            });
    });
});
