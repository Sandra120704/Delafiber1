$(function () {
  const base_url = "http://delafiber.test/";

  function u(path) {
    return (base_url.endsWith('/') ? base_url : base_url + '/') + path.replace(/^\/+/, '');
  }

  function initKanbanCards() {
    $(".kanban-card").attr("draggable", true);

    $(".kanban-column").each(function(){
      this.addEventListener('dragover', e => e.preventDefault());
      this.addEventListener('drop', function(e){
        e.preventDefault();
        const cardId = e.dataTransfer.getData("text");
        const card = document.getElementById(cardId);
        if(!card) return;

        this.appendChild(card);

        const idlead = card.dataset.id;
        const newEtapa = this.dataset.etapa;

        $.post(u('lead/actualizarEtapa'), { idlead, idetapa: newEtapa })
          .done(res => { if(!res.success) Swal.fire('Error', res.message, 'error'); })
          .fail(()=> Swal.fire('Error','No se pudo actualizar la etapa','error'));
      });
    });

    $(".kanban-card").on("dragstart", function(e){
      e.originalEvent.dataTransfer.setData("text", this.id);
    });
  }

  initKanbanCards();
});
