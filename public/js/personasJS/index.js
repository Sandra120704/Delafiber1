btnBuscar?.addEventListener('click', async () => {
    const dni = dniInput.value.trim();
    if (!dni || dni.length !== 8 || isNaN(dni)) {
        Swal.fire({ 
            icon: 'warning', 
            text: 'Ingrese un DNI válido de 8 dígitos',
            toast: true, 
            position: 'bottom-end', 
            timer: 3000, 
            showConfirmButton: false 
        });
        return;
    }

    buscando.classList.remove('d-none');
    btnBuscar.disabled = true;

    try {
        const res = await fetch(`${BASE_URL}personas/buscardni?q=${dni}`);
        if (!res.ok) throw new Error('Error en la consulta');

        const data = await res.json();

        if (!data.success) {
            // No existe en BD ni en la api  de DeColecta
            apellidosInput.value = '';
            nombresInput.value = '';
            Swal.fire({
                icon: 'info',
                text: data.message || 'No se encontró la persona',
                toast: true,
                position: 'bottom-end',
                timer: 3000,
                showConfirmButton: false
            });
            return;
        }

        if (data.existe) {
            // Caso 1: Ya está en la BD
            Swal.fire({
                icon: 'info',
                title: 'Persona ya registrada',
                text: `El DNI ${dni} ya está registrado como: ${data.nombres} ${data.apepaterno} ${data.apematerno}`,
                toast: true,
                position: 'bottom-end',
                timer: 4000,
                showConfirmButton: false
            });

            // Limpio el form para que no pueda registrarlo de nuevo
            apellidosInput.value = '';
            nombresInput.value = '';

        } else {
            // Verificar  que No existe en BD
            apellidosInput.value = `${data.apepaterno} ${data.apematerno}`;
            nombresInput.value = data.nombres;

            Swal.fire({
                icon: 'success',
                text: 'Persona encontrada. Complete y guarde para registrarla.',
                toast: true,
                position: 'bottom-end',
                timer: 3000,
                showConfirmButton: false
            });
        }

    } catch (err) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al consultar el DNI',
            toast: true,
            position: 'bottom-end',
            timer: 3000,
            showConfirmButton: false
        });
    } finally {
        buscando.classList.add('d-none');
        btnBuscar.disabled = false;
    }
});