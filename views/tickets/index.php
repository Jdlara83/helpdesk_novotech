<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Mis Tickets</h1>
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
                                <th>Categoria Soporte</th>
                                <th>Proyecto Soporte</th>
                                <th>Asunto Ticket</th>
                                <th>Prioridad Ticket</th>
                                <th>Fecha Ticket</th>
                                <th>Estado Soporte Ticket</th>
                                <th>Estado Ticket</th>
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

<!-- 1. Modal para Registrar Ticket (MANTENIDO INTACTO) -->
<div class="modal fade" id="modalNuevoTicket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-ticket-alt"></i> Registrar Nuevo Ticket</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
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
                                        <label>Categoría Soporte</label>
                                        <select name="id_categoria_soporte" id="id_categoria_soporte"
                                            class="form-control" required>
                                            <option value="">-- Seleccione --</option>
                                            <?php if (isset($categoriasPermitidas) && !empty($categoriasPermitidas)): ?>
                                                <?php foreach ($categoriasPermitidas as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="1">SOFTWARE</option>
                                                <option value="2">INFRAESTRUCTURA</option>
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
                                            required></textarea>
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
                                        <input type="text" name="asunto_ticket" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Prioridad Ticket</label>
                                        <select name="prioridad" class="form-control" required>
                                            <option value="Baja">Baja</option>
                                            <option value="Media" selected>Media</option>
                                            <option value="Alta">Alta</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Detalle Ticket</label>
                                        <textarea name="detalle_ticket" class="form-control" rows="4"
                                            required></textarea>
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

<!-- 2. Modal Ver Detalle (Ojo Verde) -->
<div class="modal fade" id="modalVerDetalleTicket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-eye"></i> Detalle del Ticket <span
                        id="ver_id_ticket"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 border-right">
                        <p class="mb-1"><strong>Categoría:</strong> <span id="ver_categoria" class="text-muted"></span>
                        </p>
                        <p class="mb-1"><strong>Proyecto:</strong> <span id="ver_proyecto" class="text-muted"></span>
                        </p>
                        <p class="mb-1"><strong>Prioridad:</strong> <span id="ver_prioridad"></span></p>
                        <p class="mb-1"><strong>Estado:</strong> <span id="ver_estado"></span></p>
                        <hr>
                        <p class="mb-1"><strong>Solicitante:</strong> <span id="ver_solicitante"
                                class="text-muted"></span></p>
                        <p class="mb-1"><strong>Ubicación:</strong> <span id="ver_ubicacion" class="text-muted"></span>
                        </p>
                        <p class="mb-1"><strong>Fecha Creación:</strong> <span id="ver_fecha" class="text-muted"></span>
                        </p>
                    </div>
                    <div class="col-md-7">
                        <h6 class="font-weight-bold text-dark" id="ver_asunto"></h6>
                        <hr>
                        <label class="text-secondary">Descripción de la Solicitud:</label>
                        <div class="p-2 bg-light rounded mb-3 border" id="ver_detalle"
                            style="max-height: 150px; overflow-y: auto;"></div>
                        <label class="text-secondary">Evidencias Cargadas:</label>
                        <div id="ver_archivos_cargados" class="d-flex flex-wrap gap-2"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- 3. Modal Seguimiento (Globo Azul) -->
<div class="modal fade" id="modalSeguimientoTicket" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-comments"></i> Seguimiento Ticket <span
                        id="seg_id_ticket"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <!-- Resumen conciso -->
                <div class="alert alert-light border mb-3">
                    <strong>Asunto:</strong> <span id="seg_asunto"></span> |
                    <strong>Categoría:</strong> <span id="seg_categoria"></span>
                </div>

                <!-- Historial del chat -->
                <div class="card card-outline card-info mb-3">
                    <div class="card-header p-2">
                        <h3 class="card-title"><i class="fas fa-history"></i> Historial de Conversación</h3>
                    </div>
                    <div class="card-body p-3" id="ver_historial_seguimiento"
                        style="max-height: 220px; overflow-y: auto; background-color: #f8f9fa;">
                    </div>
                </div>

                <!-- Escribir nuevo seguimiento -->
                <form id="formNuevoSeguimiento" enctype="multipart/form-data">
                    <input type="hidden" name="id_ticket" id="seg_input_id_ticket">
                    <div class="form-group">
                        <label for="observacion_seguimiento">Agregar Observación / Mensaje</label>
                        <textarea name="observacion" id="observacion_seguimiento" class="form-control" rows="3"
                            placeholder="Escriba su mensaje aquí..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Adjuntar Archivo (Opcional)</label>
                        <input type="file" name="archivos_seguimiento[]" class="form-control-file" multiple>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-paper-plane"></i> Guardar
                        Seguimiento</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="js/tickets.js"></script>