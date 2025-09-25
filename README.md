# Proyecto CRM para la gestion de Clientes Potenciales.

##  Descripción
Sistema CRM completo para **Delafiber** - empresa de servicios de fibra óptica. Gestiona clientes potenciales, leads, campañas de marketing y usuarios del sistema.

## Características
- **Gestión de Personas** - Base de datos completa de clientes potenciales
- **Sistema de Leads** - Tablero Kanban con etapas de seguimiento
- **Campañas de Marketing** - Control de campañas publicitarias y medios
- **Panel de Control** - Dashboard con KPIs específicos de fibra óptica
- **Gestión de Usuarios** - Control de acceso y roles
- **Tareas y Seguimiento** - Sistema de tareas organizadas

##  Tecnologías
- **Backend**: PHP 8.2 + CodeIgniter 4
- **Frontend**: Bootstrap 5.3, JavaScript ES6+, AJAX
- **Base de Datos**: MySQL
- **Servidor**: Laragon (Apache + MySQL)

## Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/Sandra120704/Delafiber1.git
cd Delafiber1
```

### 2. Instalar dependencias
```bash
# Instalar dependencias PHP (CodeIgniter)
composer install

# Instalar dependencias JavaScript (DataTables, jQuery)
npm install
```

### 3. Configurar base de datos
```bash
# Copiar archivo de configuración desde la plantilla
cp .env.example .env
```

Editar `.env` con tus datos:
```env
database.default.hostname = localhost
database.default.database = delafiber
database.default.username = root
database.default.password = 
```

### 4. Importar base de datos
- Crear base de datos `delafiber`
- Importar `app/Database/db.sql`

### 5. Configurar servidor local
**Laragon**: Agregar `delafiber.test` a hosts automáticamente
**XAMPP/WAMP**: Configurar virtual host para `delafiber.test`

### 6. Acceder al sistema
- URL: `http://delafiber.test`
- Login por defecto: configurar en la base de datos

##  Estructura del Proyecto
```
app/
├── Controllers/     # Controladores del sistema
├── Models/         # Modelos de datos
├── Views/          # Vistas y templates
└── Database/       # Migraciones y SQL

public/
├── js/            # JavaScript organizado por módulos
├── css/           # Estilos CSS
└── assets/        # Recursos estáticos
```

##  Módulos Principales
- **Dashboard** - Panel principal con métricas
- **Personas** - Gestión de clientes potenciales
- **Leads** - Sistema Kanban de seguimiento
- **Campañas** - Control de campañas publicitarias
- **Usuarios** - Administración de usuarios
- **Tareas** - Sistema de tareas y seguimiento

## Desarrollo
```bash
# Verificar sintaxis PHP
php -l app/Controllers/NombreController.php

# Ejecutar en modo desarrollo
php spark serve
```

## Autor
**Sandra** - Desarrollo ...

---
