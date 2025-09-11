export function initLeadsForm(modalEl) {
    const origenSelect = modalEl.querySelector('#origenSelect');
    const campaniaDiv = modalEl.querySelector('#campaniaDiv');
    const campSelect = modalEl.querySelector('#campaniaSelect');
    const referenteDiv = modalEl.querySelector('#referenteDiv');
    const referidoInput = modalEl.querySelector('#referido_por');

    if (!origenSelect) return;

    function actualizarDivs() {
        const tipo = origenSelect.options[origenSelect.selectedIndex]?.getAttribute('data-tipo') || '';

        if (tipo === 'campaña' || tipo === 'campania') {
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

    // Inicializar
    actualizarDivs();

    // Evento dinámico
    origenSelect.addEventListener('change', actualizarDivs);

    // Limpiar modal al cerrarlo
    modalEl.addEventListener('hidden.bs.modal', () => {
        campaniaDiv.style.display = 'none';
        referenteDiv.style.display = 'none';
        campSelect.required = false;
        referidoInput.required = false;
        origenSelect.value = '';
        campSelect.value = '';
        referidoInput.value = '';
    });
}
