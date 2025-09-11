document.addEventListener('DOMContentLoaded', function () {
    const origenSelect = document.getElementById('origenSelect');
    const campaniaDiv = document.getElementById('campaniaDiv');
    const campSelect = document.getElementById('campaniaSelect');
    const referenteDiv = document.getElementById('referenteDiv');
    const referidoInput = document.getElementById('referido_por');

    function actualizarDivs() {
        if (!origenSelect) return;
        const tipo = origenSelect.options[origenSelect.selectedIndex]?.getAttribute('data-tipo') || '';

        if (tipo === 'campania') {
            campaniaDiv.style.display = 'block';
            referenteDiv.style.display = 'none';
            campSelect.required = true;
            referidoInput.required = false;
        } else if (tipo === 'referido') {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'block';
            campSelect.required = false;
            referidoInput.required = true;
        } else {
            campaniaDiv.style.display = 'none';
            referenteDiv.style.display = 'none';
            campSelect.required = false;
            referidoInput.required = false;
        }
    }

    // Inicializar al cargar
    actualizarDivs();

    // Cambiar dinámicamente
    if (origenSelect) {
        origenSelect.addEventListener('change', actualizarDivs);
    }
});
