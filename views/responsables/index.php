<div class="content-wrapper">
    <!-- Encabezado del Módulo -->
    <section class="content-header pt-3 pb-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 font-weight-bold text-dark">Gestión de Responsables de Proyecto</h1>
            <button class="btn btn-primary btn-sm" onclick="nuevoResponsable()">
                <i class="fas fa-plus-circle mr-1"></i> Agregar Responsable
            </button>
        </div>
    </section>

    <!-- Tabla DataTables -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary shadow-sm">
                <div class="card-body">
                    <table id="tablaResponsables" class="table table-bordered table-striped table-hover w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Categoría Soporte</th>
                                <th>Proyecto Soporte</th>
                                <th>Usuario Responsable</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se cargará dinámicamente vía AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal Agregar Responsable -->
<div class="modal fade" id="modalResponsable" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <!-- El form debe ENVOLVER todo el contenido incluyendo el footer -->
            <form id="formResponsable">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">Agregar Responsable de Proyecto</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="responsable_id">

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-secondary">Categoría Soporte</label>
                            <select name="id_categoria_soporte" id="id_categoria_soporte" class="form-control" required>
                                <option value="">-- Seleccione una opción --</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-secondary">Proyecto Soporte</label>
                            <select name="id_proyecto_soporte" id="id_proyecto_soporte" class="form-control" required>
                                <option value="">-- Seleccione un proyecto --</option>
                            </select>
                        </div>

                        <div class="col-md-4 form-group">
                            <label class="font-weight-bold text-secondary">Usuario Responsable</label>
                            <select name="id_usuario" id="id_usuario" class="form-control" required>
                                <option value="">-- Seleccione un usuario --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-dismiss="modal">Cancelar</button>
                    <!-- Le asignamos un ID propio al botón para vincular el evento por si el submit falla -->
                    <button type="submit" id="btnGuardarResponsable" class="btn btn-primary px-4">Asignar
                        Responsable</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Carga del JavaScript específico del módulo -->
<script src="js/responsables.js"></script>