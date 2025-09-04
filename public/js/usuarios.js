$(function() {
    function u(path) {
        return window.base_url.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
    }

    // ===== Abrir formulario nuevo usuario =====
    $(document).on("click", "#btnNuevoUsuario", function() {
        $("#contenido-usuarios").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('usuarios/crear'), function(html) {
            $("#contenido-usuarios").html(html);
        }).fail(() => alert(" No se pudo cargar el formulario"));
    });

    // ===== Abrir formulario editar usuario =====
    $(document).on("click", ".btn-editar", function(e) {
        e.preventDefault();
        const id = $(this).data("id");
        $("#contenido-usuarios").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('usuarios/editar/' + id), function(html) {
            $("#contenido-usuarios").html(html);
        }).fail(() => alert(" No se pudo cargar el formulario de edición"));
    });

    // ===== Volver a la lista de usuarios =====
    $(document).on("click", "#btnVolverLista", function() {
        $("#contenido-usuarios").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('usuarios'), function(html) {
            const contenido = $(html).find('#contenido-usuarios').html();
            $("#contenido-usuarios").html(contenido);
        }).fail(() => alert("No se pudo cargar la lista"));
    });

    // ===== Guardar usuario (crear/editar) =====
    $(document).on("submit", "#formUsuario", function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find("button[type=submit]");
        btn.prop("disabled", true);

        $.post(form.attr("action"), form.serialize(), function(res) {
            alert(res.mensaje || 'Operación realizada');
            btn.prop("disabled", false);
            if (res.success) {
                // Recargar lista automáticamente
                $("#btnVolverLista").click();
            }
        }, 'json').fail(() => {
            alert("Error al guardar");
            btn.prop("disabled", false);
        });
    });
});
