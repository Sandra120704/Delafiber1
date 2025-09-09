document.addEventListener('DOMContentLoaded', () => {

  document.querySelectorAll(".btn-convertir-lead").forEach(btn => {
      btn.addEventListener("click", function() {
          const idpersona = this.dataset.id;

          fetch(`${base_url}leads/crear/${idpersona}`)
            .then(res => res.text())
            .then(html => {
                const modal = document.getElementById("leadModal");
                modal.querySelector('.modal-content').innerHTML = html;
                const bootstrapModal = new bootstrap.Modal(modal);
                bootstrapModal.show();

                // Inicializar selección de etapa dentro del modal
                const etapaBtns = modal.querySelectorAll('.etapa-btn');
                const etapaInput = modal.querySelector('#idetapa');

                etapaBtns.forEach(b => {
                    b.addEventListener('click', () => {
                        etapaBtns.forEach(btn => btn.classList.remove('active'));
                        b.classList.add('active');
                        etapaInput.value = b.dataset.etapa;
                    });
                });

                // Validación del form dentro del modal
                const formLead = modal.querySelector('#form-lead');
                formLead.addEventListener('submit', function(e){
                    if(!etapaInput.value){
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Seleccione una etapa',
                        });
                    }
                });

            }).catch(err => console.error('Error al cargar modal:', err));
      });
  });

});
