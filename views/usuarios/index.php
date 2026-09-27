<div class="content-wrapper">
    <!-- Encabezado del Módulo -->
    <section class="content-header pt-3 pb-2">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <h1 class="h4 font-weight-bold text-dark">Gestión de Usuarios</h1>
            <button class="btn btn-primary btn-sm" onclick="nuevoUsuario()">
                <i class="fas fa-user-plus mr-1"></i> Nuevo Usuario
            </button>
        </div>
    </section>

    <!-- Tabla DataTables -->
    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body">
                    <table id="tablaUsuarios" class="table table-bordered table-striped table-hover w-100">
                        <thead class="bg-light">
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Perfil</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Se cargará dinámicamente con AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- Modal para Crear / Editar Usuario -->
<div class="modal fade" id="modalUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <form id="formUsuario">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">Nuevo Usuario</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <!-- Campo oculto para detectar si es Crear (vacío) o Editar (con ID) -->
                    <input type="hidden" name="id" id="usuario_id">

                    <!-- Fila 1: Nombre y Documento -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Nombre Completo</label>
                            <input type="text" name="nombre" id="nombre" class="form-control"
                                placeholder="Ej: Juan Pérez" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Documento</label>
                            <input type="text" name="documento" id="documento" class="form-control"
                                placeholder="Ej: 123456789" required>
                        </div>
                    </div>

                    <!-- Fila 2: Correo y Teléfono -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Correo Electrónico</label>
                            <input type="email" name="mail" id="mail" class="form-control"
                                placeholder="correo@ejemplo.com" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Telefono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control"
                                placeholder="Ej: 3001234567" required>
                        </div>
                    </div>

                    <!-- Fila 3: Perfil y Estado -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Perfil / Rol</label>
                            <select name="perfil" id="perfil" class="form-control" required>
                                <option value="1">ADMINISTRADOR</option>
                                <option value="2">USUARIO</option>
                                <option value="3">SOPORTE</option>
                                <option value="4">REQUERIMIENTO</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Estado</label>
                            <select name="estado" id="estado" class="form-control">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fila 4: Contraseña a ancho completo o en su propia sección -->
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label>Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="••••••••">
                            <small class="text-muted d-block mt-1">
                                * Dejar en blanco para conservar la clave actual (en edición) o asignar
                                <code>Admin123</code> por defecto (en creación).
                            </small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="/public/js/usuarios.js"></script>