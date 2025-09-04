<!-- Formulario compacto de registrar persona -->
<div class="container form-container">
    <div class="card form-card">
        <div class="card-header"><h5>Registrar Persona</h5></div>
        <div class="card-body">
            <form id="formPersona">
                <input type="hidden" name="idpersona" id="idpersona">

                <!-- Fila 1 -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label for="apellidos" class="form-label">Apellidos</label>
                        <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                    </div>
                    <div class="col-md-4">
                        <label for="nombres" class="form-label">Nombres</label>
                        <input type="text" class="form-control" id="nombres" name="nombres" required>
                    </div>
                    <div class="col-md-4">
                        <label for="telprimario" class="form-label">Teléfono Principal</label>
                        <input type="text" class="form-control" id="telprimario" name="telprimario" required>
                    </div>
                </div>

                <!-- Fila 2 -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label for="telalternativo" class="form-label">Teléfono Alternativo</label>
                        <input type="text" class="form-control" id="telalternativo" name="telalternativo">
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="col-md-4">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>
                </div>

                <!-- Referencias -->
                <div class="mb-2">
                    <label for="referencias" class="form-label">Referencias</label>
                    <textarea class="form-control" id="referencias" name="referencias"></textarea>
                </div>

                <!-- Ubicación -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label for="departamento" class="form-label">Departamento</label>
                        <select id="departamento" name="departamento" class="form-select">
                            <option value="">Seleccione...</option>
                            <?php if(!empty($departamento)): ?>
                                <?php foreach($departamento as $dep): ?>
                                    <option value="<?= $dep['idDepartamento'] ?>"><?= $dep['departamento'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="provincia" class="form-label">Provincia</label>
                        <select id="provincia" name="provincia" class="form-select">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="distrito" class="form-label">Distrito</label>
                        <select id="distrito" name="iddistrito" class="form-select">
                            <option value="">Seleccione...</option>
                        </select>
                    </div>
                </div>

                <div class="mt-2">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>