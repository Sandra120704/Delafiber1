$(function () {
    // Construir URLs con base_url
    function u(path) {
        if (!window.base_url) { console.error("base_url no definido"); return path; }
        return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
    }

    // ======== Abrir formulario nueva persona ========
    $(document).on("click", "#btnNuevaPersona", function () {
        $("#contenido-persona").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('persona/form'), function (html) {
            $("#contenido-persona").html(html);
            initUbicacion(); // inicializa select dinámicos
        }).fail(() => alert("❌ No se pudo cargar el formulario"));
    });

    // ======== Editar persona ========
    $(document).on("click", ".btn-edit", function () {
        const id = $(this).data("id");
        $("#contenido-persona").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('persona/form/' + id), function (html) {
            $("#contenido-persona").html(html);
            initUbicacion(true); // inicializa select dinámicos con valores
        }).fail(() => alert("❌ No se pudo cargar el formulario"));
    });

    // ======== Volver a la lista ========
    $(document).on("click", "#btnVolverLista", function () {
        recargarLista();
    });

    function recargarLista() {
        $.get(u('personas'), function (html) {
            $("#contenido-persona").html($(html).find('#contenido-persona').html());
        }).fail(() => alert("❌ No se pudo recargar la lista"));
    }

    // ======== Eliminar persona ========
    $(document).on("click", ".btn-delete", function () {
        const id = $(this).data("id");
        if (!confirm("¿Seguro de eliminar esta persona?")) return;
        $.post(u('persona/eliminar'), { idpersona: id }, function (res) {
            alert(res.mensaje || 'Operación realizada');
            if (res.success) recargarLista();
        }, 'json').fail(() => alert("❌ Error al eliminar"));
    });

    // ======== Guardar (crear / editar) ========
    $(document).on("submit", "#formPersona", function (e) {
        e.preventDefault();
        const form = $(this);

        $.post(form.attr("action"), form.serialize(), function (res) {
            alert(res.mensaje || 'Operación realizada');
            if (res.success) recargarLista();
        }, 'json').fail(() => alert("❌ Error al guardar"));
    });

    // ======== Inicializar select cascada ========
    function initUbicacion(withDefaults = false) {
        const $form = $("#formPersona");
        if ($form.length === 0) return;

        const selDep = $form.data('iddepartamento') || '';
        const selProv = $form.data('idprovincia') || '';
        const selDist = $form.data('iddistrito') || '';

        // Si viene valor seleccionado (modo editar)
        if (selDep) {
            $("#departamento").val(selDep);
            cargarProvincias(selDep, selProv).then(() => {
                if (selProv) cargarDistritos(selProv, selDist);
            });
        }

        // Evento cambio Departamento
        $(document).off('change', '#departamento').on('change', '#departamento', function () {
            const idDep = $(this).val();
            $("#provincia").html('<option value="">Cargando...</option>');
            $("#distrito").html('<option value="">Seleccione...</option>');
            if (idDep) cargarProvincias(idDep);
            else $("#provincia").html('<option value="">Seleccione...</option>');
        });

        // Evento cambio Provincia
        $(document).off('change', '#provincia').on('change', '#provincia', function () {
            const idProv = $(this).val();
            $("#distrito").html('<option value="">Cargando...</option>');
            if (idProv) cargarDistritos(idProv);
            else $("#distrito").html('<option value="">Seleccione...</option>');
        });
    }

    // ======== Funciones para cargar Provincias y Distritos ========
    function cargarProvincias(idDep, selected = '') {
        return $.get(u('persona/getProvincias/' + idDep), function (list) {
            const $prov = $("#provincia");
            $prov.empty().append('<option value="">Seleccione...</option>');
            (list || []).forEach(p => {
                const opt = $('<option>').val(p.idprovincia).text(p.provincia);
                if (selected && String(p.idprovincia) === String(selected)) opt.prop('selected', true);
                $prov.append(opt);
            });
        }, 'json').fail(() => alert("❌ No se pudieron cargar las provincias"));
    }

    function cargarDistritos(idProv, selected = '') {
        return $.get(u('persona/getDistritos/' + idProv), function (list) {
            const $dist = $("#distrito");
            $dist.empty().append('<option value="">Seleccione...</option>');
            (list || []).forEach(d => {
                const opt = $('<option>').val(d.iddistrito).text(d.distrito);
                if (selected && String(d.iddistrito) === String(selected)) opt.prop('selected', true);
                $dist.append(opt);
            });
        }, 'json').fail(() => alert("❌ No se pudieron cargar los distritos"));
    }
});
