/**
 * app.js - Parqueadero Boyacá
 */

// ─── Cronómetro en tiempo real ────────────────────────────
function actualizarTiempos() {
    document.querySelectorAll('.tiempo[data-entrada]').forEach(cel => {
        const entrada  = new Date(cel.dataset.entrada.replace(' ', 'T'));
        const ahora    = new Date();
        const diff     = Math.floor((ahora - entrada) / 1000);

        const dias     = Math.floor(diff / 86400);
        const horas    = Math.floor((diff % 86400) / 3600);
        const minutos  = Math.floor((diff % 3600) / 60);
        const segundos = diff % 60;

        if (dias > 0) {
            cel.textContent = `${dias}d ${pad(horas)}h ${pad(minutos)}m ${pad(segundos)}s`;
        } else {
            cel.textContent = `${pad(horas)}h ${pad(minutos)}m ${pad(segundos)}s`;
        }

        cel.style.color = horas >= 3 ? '#C62828' : horas >= 1 ? '#E65100' : '#2E7D32';
        cel.style.fontWeight = '600';
    });
}

function pad(n) { return String(n).padStart(2, '0'); }

if (document.querySelector('.tiempo')) {
    actualizarTiempos();
    setInterval(actualizarTiempos, 1000);
}

// ─── Confirmar salida ─────────────────────────────────────
document.querySelectorAll('form[action="salida.php"]').forEach(form => {
    form.addEventListener('submit', e => {
        const placa = form.closest('tr')?.querySelector('.placa')?.textContent || '';
        if (!confirm(`¿Confirma la salida del vehículo ${placa}?`)) {
            e.preventDefault();
        }
    });
});

// ─── Placa a mayúsculas ───────────────────────────────────
document.querySelectorAll('input[name="placa"]').forEach(input => {
    input.addEventListener('input', () => {
        input.value = input.value.toUpperCase();
    });
});

// ─── Filtro de búsqueda en tabla ──────────────────────────
function initBuscador() {
    const input = document.getElementById('buscar');
    if (!input) return;

    input.addEventListener('keyup', () => {
        const texto = input.value.toLowerCase().trim();
        document.querySelectorAll('.tabla tbody tr').forEach(row => {
            const coincide = texto === '' || row.textContent.toLowerCase().includes(texto);
            row.classList.toggle('fila-oculta', !coincide);
        });
    });
}
initBuscador();

// ─── Loading overlay ──────────────────────────────────────
(function() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.innerHTML = '<div class="spinner"></div><p>Cargando...</p>';
    document.body.appendChild(overlay);

    // Ocultar al cargar la página
    window.addEventListener('load', () => overlay.classList.remove('show'));
    setTimeout(() => overlay.classList.remove('show'), 500);

    // Mostrar al hacer clic en enlaces de navegación
    document.querySelectorAll('nav a, .btn').forEach(link => {
        link.addEventListener('click', () => {
            if (link.getAttribute('href') && !link.getAttribute('target')) {
                overlay.classList.add('show');
            }
        });
    });

    // Mostrar al enviar formularios
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            overlay.classList.add('show');
        });
    });
})();

// ─── Botón imprimir ───────────────────────────────────────
document.getElementById('btn-imprimir')?.addEventListener('click', () => {
    window.print();
});