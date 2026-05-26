document.addEventListener('DOMContentLoaded', () => {
    console.log("Sistema de Parqueadero cargado correctamente.");

    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (form.classList.contains('confirmar-accion')) {
                if (!confirm('¿Estás seguro de realizar esta acción?')) {
                    e.preventDefault();
                }
            }
        });
    });
});

function actualizarReloj() {
    const ahora = new Date();
    const elementoReloj = document.getElementById('reloj');
    if (elementoReloj) {
        elementoReloj.innerText = ahora.toLocaleTimeString();
    }
}
setInterval(actualizarReloj, 1000);