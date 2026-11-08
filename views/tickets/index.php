<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Gestión de Tickets / Solicitudes</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalNuevoTicket">
                        <i class="fas fa-plus"></i> Nuevo Ticket
                    </button>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary card-outline">
                <div class="card-body">
                    <table id="tablaTickets" class="table table-bordered table-striped dt-responsive nowrap"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th># ID</th>
                                <th>Título / Asunto</th>
                                <th>Solicitante</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal para Registrar Ticket -->
<div class="modal fade" id="modalNuevoTicket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-ticket-alt"></i> Registrar Nuevo Ticket</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formNuevoTicket" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card card-outline card-primary mb-3">
                                <div class="card-header">
                                    <h3 class="card-title">Información Básica</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Categoría Soporte / Tipo Solicitud</label>
                                        <select name="id_categoria_soporte" id="id_categoria_soporte"
                                            class="form-control" required>
                                            <option value="">-- Seleccione una opción --</option>
                                            <?php if (isset($categoriasPermitidas) && !empty($categoriasPermitidas)): ?>
                                                <?php foreach ($categoriasPermitidas as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="1">SOFTWARE</option>
                                                <option value="2">INFRAESTRUCTURA</option>
                                                <option value="3">REQUERIMIENTO</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="card card-outline card-info">
                                <div class="card-header">
                                    <h3 class="card-title">Información Contacto</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Detalle Ubicación</label>
                                        <textarea name="detalle_ubicacion" class="form-control" rows="3"
                                            placeholder="Ej: Oficina 302, Sede Central" required></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Detalle Ticket</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Asunto Ticket</label>
                                        <input type="text" name="asunto_ticket" class="form-control"
                                            placeholder="Escriba el asunto..." required>
                                    </div>
                                    <div class="form-group">
                                        <label>Prioridad Ticket</label>
                                        <select name="prioridad" class="form-control" required>
                                            <option value="">-- Seleccione una opción --</option>
                                            <option value="Baja">Baja</option>
                                            <option value="Media" selected>Media</option>
                                            <option value="Alta">Alta</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Detalle Ticket</label>
                                        <textarea name="detalle_ticket" class="form-control" rows="4"
                                            placeholder="Describa detalladamente su solicitud..." required></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 form-group">
                                            <label>Imágenes</label>
                                            <input type="file" name="imagenes[]" class="form-control-file"
                                                accept="image/*" multiple>
                                        </div>
                                        <div class="col-md-6 form-group">
                                            <label>Archivos / Documentos</label>
                                            <input type="file" name="archivos[]" class="form-control-file" multiple>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Crear Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/tickets.js"></script>