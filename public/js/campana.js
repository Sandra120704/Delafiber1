$(function () {

    // Función para construir URLs correctamente
    function u(path) {
        if (!window.base_url) { console.error("base_url no definido"); return path; }
        return base_url.replace(/\/+$/, '') + '/' + path.replace(/^\/+/, '');
    }

    // ===== Abrir formulario nueva campaña =====
    $(document).on("click", "#btnNuevaCampana", function () {
      e.preventDefault(); 
        $("#contenido-campanas").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('campana/form'), function (html) {
            $("#contenido-campanas").html(html);
        }).fail(() => alert("No se pudo cargar el formulario"));
    });

    // ===== Editar campaña =====
    $(document).on("click", ".btn-edit", function () {
      e.preventDefault(); 
        const id = $(this).data("id");
        $("#contenido-campanas").html('<div class="text-center p-5">Cargando...</div>');
        $.get(u('Campanas/crear/' + id), function (html) {
            $("#contenido-campanas").html(html);
        }).fail(() => alert("No se pudo cargar el formulario"));
    });

    // ===== Guardar (crear/actualizar) =====
    $(document).on("submit", "#formCampana", function (e) {
        e.preventDefault();
        const form = $(this);

        $.post(form.attr("action"), form.serialize(), function (res) {
            alert(res.mensaje || 'Operación realizada');
            if (res.success) recargarLista();
        }, 'json').fail(() => alert("Error al guardar"));
    });

    // ===== Eliminar campaña =====
    $(document).on("click", ".btn-delete", function () {
        const id = $(this).data("id");
        if (!confirm("¿Seguro de eliminar esta campaña?")) return;

        $.post(u('campana/eliminar'), { idcampania: id }, function (res) {
            alert(res.mensaje || 'Operación realizada');
            if (res.success) recargarLista();
        }, 'json').fail(() => alert(" Error al eliminar"));
    });

    // ===== Recargar lista =====
    function recargarLista() {
        $.get(u('campanas'), function (html) {
            // Reemplaza solo el contenido dinámico
            $("#contenido-campanas").html($(html).find('#contenido-campanas').html());
        }).fail(() => alert(" No se pudo recargar la lista"));
    }
    // Cambiar estado activo/inactivo
    $(document).on("click", ".btn-estado", function(){
        const btn = $(this);
        const id = btn.data("id");
        const nuevoEstado = btn.data("estado") === 'activo' ? 'inactivo' : 'activo';
        
        $.post(base_url + "campana/cambiarEstado", {idcampania: id, estado: nuevoEstado}, function(res){
            if(res.success){
                btn.data("estado", nuevoEstado)
                   .removeClass("btn-success btn-danger")
                   .addClass(nuevoEstado === 'activo' ? 'btn-success' : 'btn-danger')
                   .text(nuevoEstado.charAt(0).toUpperCase() + nuevoEstado.slice(1));
            }
        }, 'json').fail(() => alert("❌ Error al cambiar estado"));
    });

});

