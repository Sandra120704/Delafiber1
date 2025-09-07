document.addEventListener('DOMContentLoaded', () => {
    const btnsDetalle = document.querySelectorAll('.btn-detalle');
    const modalBody = document.getElementById('detalleMedios');
    const detalleModal = new bootstrap.Modal(document.getElementById('detalleCampanaModal'));

    btnsDetalle.forEach(btn => {
        btn.addEventListener('click', async () => {
            const idcampania = btn.dataset.id;

            modalBody.innerHTML = '<tr><td colspan="3">Cargando...</td></tr>';
            detalleModal.show();

            try {
                const res = await fetch(`/campana/medios/${idcampania}`);
                if(!res.ok) throw new Error('Error al cargar los medios');
                
                const data = await res.json();
                
                if(data.length === 0){
                    modalBody.innerHTML = '<tr><td colspan="3">No hay medios registrados para esta campaña</td></tr>';
                    return;
                }

                modalBody.innerHTML = '';
                data.forEach(item => {
                    const row = `<tr>
                        <td>${item.nombre}</td>
                        <td>S/ ${parseFloat(item.inversion).toFixed(2)}</td>
                        <td>${item.leads}</td>
                    </tr>`;
                    modalBody.innerHTML += row;
                });

            } catch(err){
                console.error(err);
                modalBody.innerHTML = '<tr><td colspan="3">Error al cargar los datos</td></tr>';
            }
        });
    });
});
