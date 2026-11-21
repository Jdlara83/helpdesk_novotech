# Sistema Web de Help Desk — NOVOTECH

Sistema web desarrollado bajo el patrón arquitectónico **Modelo-Vista-Controlador (MVC)** para la gestión, asignación y seguimiento de incidentes y requerimientos técnicos en la empresa **NOVOTECH**.

##  Funcionalidades Principales

* **Autenticación y Control de Acceso (RBAC):** Inicio de sesión seguro con gestión de sesiones y restricción de vistas/acciones según el perfil (*Administrador*, *Analista de Soporte*, *Usuario*).
* **Gestión de Usuarios (CRUD):** Creación, consulta, actualización y activación/desactivación de usuarios del sistema.
* **Gestión y Seguimiento de Tickets:** Radicación de incidentes y solicitudes de servicio, asignación a analistas técnicos, cambio de estados (*Abierto*, *En Proceso*, *Cerrado*), priorización e historial de trazabilidad.
* **Interfaz Dinámica y Reportes:** Tablas interactivas con filtros y búsqueda en tiempo real, alertas visuales de confirmación y paneles de consulta operativa.

##  Tecnologías Utilizadas

* **Backend:** PHP 8.1
* **Base de Datos:** MySQL 8.0 (Conexión mediante PDO con sentencias preparadas)
* **Frontend:** HTML5, CSS3 (modularizado por componente), JavaScript
* **Librerías e Interfaces:** AdminLTE, Bootstrap 5, DataTables, AJAX, SweetAlert2
* **Servidor Local de Desarrollo:** Laragon (Apache)

##  Estructura del Proyecto

├── config/          # Configuración de conexión a base de datos y constantes globales
├── controllers/     # Controladores que procesan las peticiones y conectan Modelo-Vista
├── models/          # Clases y consultas SQL (PDO) para la lógica de negocio
├── views/           # Interfaces de usuario (Login, Dashboard, Usuarios, Tickets)
├── public/          # Recursos estáticos (CSS modularizado, JS, imágenes, plugins)
├── docs/            # Documentación del proyecto y planificación de Sprints (Excel)
└── index.php        # Punto de entrada y enrutador principal
