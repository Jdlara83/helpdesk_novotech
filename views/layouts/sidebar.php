<?php
// Lee el perfil guardado en la sesión activa sin forzar valores
$id_perfil = isset($_SESSION['id_perfil']) ? intval($_SESSION['id_perfil']) : 2;
$nombre_usuario = $_SESSION['usuario'] ?? $_SESSION['mail'] ?? 'Usuario';
?>

<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index.php?controller=dashboard&action=index" class="brand-link">
        <span class="brand-text font-weight-light pl-2"><strong>Help Desk</strong> NOVOTECH</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Panel de Usuario con Ícono -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
            <div class="image text-white pl-2">
                <i class="fas fa-user-circle fa-2x text-primary"></i>
            </div>
            <div class="info pl-2">
                <a href="#" class="d-block font-weight-bold"><?= htmlspecialchars($nombre_usuario) ?></a>
            </div>
        </div>

        <!-- Menú Principal -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

                <!-- 1. INICIO (Todos los perfiles) -->
                <li class="nav-item">
                    <a href="index.php?controller=dashboard&action=index" class="nav-link active">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Inicio</p>
                    </a>
                </li>

                <!-- 2. ADMINISTRACION (Solo ADMINISTRADOR = 1) -->
                <?php if ($id_perfil === 1): ?>
                    <li class="nav-header">ADMINISTRACION</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Administracion <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="index.php?controller=usuario&action=index" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Panel Usuarios</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=responsable&action=index" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Asignar Responsables</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 3. TICKET SOLICITUD (Todos los perfiles: 1, 2, 3, 4) -->
                <li class="nav-header font-weight-bold text-uppercase">Ticket</li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-ticket-alt"></i>
                        <p>Tickets <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview pl-3">
                        <!-- <li class="nav-item">
                            <a href="index.php?controller=ticket&action=index" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Ticket</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="index.php?controller=ticket&action=index" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mis Tickets</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 4. REQUERIMIENTO SOLICITUD (Todos los perfiles: 1, 2, 3, 4) -->
                <li class="nav-header">REQUERIMIENTO</li>
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-list-alt"></i>
                        <p>Requerimientos <i class="right fas fa-angle-left"></i></p>
                    </a>
                    <ul class="nav nav-treeview pl-3">
                        <!-- <li class="nav-item">
                            <a href="index.php?controller=requerimiento&action=crear" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Crear Requerimiento</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="index.php?controller=requerimiento&action=index" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Mis Requerimientos</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- 5. TICKET GESTION (ADMINISTRADOR = 1 y SOPORTE = 3) -->
                <?php if ($id_perfil === 1 || $id_perfil === 3): ?>
                    <li class="nav-header">SOPORTE</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-headset"></i>
                            <p>Gestionar Ticket <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="index.php?controller=ticket&action=gestion" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bolsa Tickets</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=ticket&action=pendientes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tickets Pendientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=ticket&action=realizados" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Tickets Realizados</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 6. REQUERIMIENTO GESTION (ADMINISTRADOR = 1 y REQUERIMIENTO = 4) -->
                <?php if ($id_perfil === 1 || $id_perfil === 4): ?>
                    <li class="nav-header">REQUERIMIENTO GESTION</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>Gestionar Requerimiento <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="index.php?controller=requerimiento&action=gestion" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Bolsa Requerimientos</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=requerimiento&action=pendientes" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Requerimientos Pendientes</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="index.php?controller=requerimiento&action=realizados" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Requerimientos Realizados</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

                <!-- 7. REPORTES (ADMINISTRADOR = 1, SOPORTE = 3 y REQUERIMIENTO = 4) -->
                <?php if ($id_perfil === 1 || $id_perfil === 3 || $id_perfil === 4): ?>
                    <li class="nav-header">REPORTES</li>
                    <li class="nav-item has-treeview">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p>Reportes <i class="right fas fa-angle-left"></i></p>
                        </a>
                        <ul class="nav nav-treeview pl-3">
                            <li class="nav-item">
                                <a href="index.php?controller=reporte&action=index" class="nav-link">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Reportes General</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>

            </ul>
        </nav>
    </div>
</aside>