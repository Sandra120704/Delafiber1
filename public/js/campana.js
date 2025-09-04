$(function () {

    // ===== Base URL global =====
    if (!window.base_url) {
        console.error("base_url no definido");
        window.base_url = ""; // fallback
    }

    function u(path) {
        return window.base_url.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
    }

    // ===== Abrir formulario nueva campaña =====
    $(document).on("click", "#btnNuevaCampana", function (e) {
        e.preventDefault();
        $("#contenido-campanas").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('campana/form'), function (html) {
            $("#contenido-campanas").html(html);
        }).fail(() => alert("No se pudo cargar el formulario"));
    });

    // ===== Editar campaña =====
    $(document).on("click", ".btn-edit", function (e) {
        e.preventDefault();
        const id = $(this).data("id");
        $("#contenido-campanas").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('campana/form/' + id), function (html) {
            $("#contenido-campanas").html(html);
        }).fail(() => alert(" No se pudo cargar el formulario"));
    });

    // ===== Guardar campaña (crear/editar) =====
    $(document).on("submit", "#formCampana", function (e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find("button[type=submit]");
        btn.prop("disabled", true); // desactiva botón mientras envía

        // Validar que haya un medio seleccionado
        const medioSeleccionado = form.find('input[name="medio"]:checked').val();
        if (!medioSeleccionado) {
            alert("Debes seleccionar un medio de difusión");
            btn.prop("disabled", false);
            return;
        }

        $.post(form.attr("action"), form.serialize(), function (res) {
            alert(res.mensaje || 'Operación realizada');
            btn.prop("disabled", false); // reactivar botón
            if (res.success) recargarLista();
        }, 'json').fail(() => {
            alert("Error al guardar");
            btn.prop("disabled", false);
        });
    });

    // ===== Eliminar campaña =====
    $(document).on("click", ".btn-delete", function () {
        const id = $(this).data("id");
        if (!confirm("¿Seguro de eliminar esta campaña?")) return;

        $.post(u('campana/eliminar'), { idcampania: id }, function (res) {
            alert(res.mensaje || 'Operación realizada');
            if (res.success) recargarLista();
        }, 'json').fail(() => alert("Error al eliminar"));
    });

    // ===== Cambiar estado =====
    $(document).on("click", ".btn-estado", function () {
        const btn = $(this);
        const id = btn.data("id");
        const nuevoEstado = btn.data("estado") === 'activo' ? 'inactivo' : 'activo';

        $.post(u('campana/cambiarEstado'), { idcampania: id, estado: nuevoEstado }, function (res) {
            if (res.success) {
                btn.text(nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1));
                btn.toggleClass('btn-success btn-secondary');
                btn.data("estado", nuevoEstado);
            } else alert(res.mensaje);
        }, "json").fail(() => alert(" Error al cambiar estado"));
    });

    // ===== Recargar lista =====
    function recargarLista() {
        $.get(u('campanas'), function (html) {
            const nuevoContenido = $(html).find('#contenido-campanas').html();
            $("#contenido-campanas").fadeOut(200, function () {
                $(this).html(nuevoContenido).fadeIn(200);
            });
        }).fail(() => alert(" No se pudo recargar la lista"));
    }

});
