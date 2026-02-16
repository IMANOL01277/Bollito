# 📘 Documentación Técnica - Sistema Mi Bollito

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Características Principales](#características-principales)
3. [Arquitectura del Sistema](#arquitectura-del-sistema)
4. [Requisitos del Sistema](#requisitos-del-sistema)
5. [Instalación y Configuración](#instalación-y-configuración)
6. [Estructura del Proyecto](#estructura-del-proyecto)
7. [Módulos del Sistema](#módulos-del-sistema)
8. [Base de Datos](#base-de-datos)
9. [API y Endpoints](#api-y-endpoints)
10. [Seguridad](#seguridad)
11. [Guía de Uso](#guía-de-uso)
12. [Mantenimiento y Solución de Problemas](#mantenimiento-y-solución-de-problemas)
13. [Roadmap y Mejoras Futuras](#roadmap-y-mejoras-futuras)

---

## 🎯 Introducción

**Mi Bollito** es un sistema integral de gestión empresarial diseñado para pequeños y medianos negocios de venta de productos. El sistema proporciona herramientas completas para la administración de inventarios, control de ventas, gestión de proveedores, análisis financiero y seguimiento de operaciones.

### Objetivo del Proyecto

Ofrecer una solución accesible, moderna y eficiente para la gestión completa de un negocio, facilitando:
- Control preciso del inventario y stock
- Seguimiento de movimientos financieros
- Gestión de personal y roles de usuario
- Análisis de rentabilidad y estadísticas
- Administración de domicilios y devoluciones

### Tecnologías Utilizadas

- **Backend**: PHP 8.2
- **Base de Datos**: PostgreSQL 15
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Framework CSS**: Bootstrap 5.3.3
- **Iconos**: Bootstrap Icons 1.11.3
- **Servidor Web**: Apache 2.4
- **Containerización**: Docker & Docker Compose

---

## ✨ Características Principales

### 1. Gestión de Inventario
- ✅ CRUD completo de productos
- ✅ Control de stock en tiempo real
- ✅ Alertas automáticas de stock bajo
- ✅ Categorización de productos
- ✅ Gestión de precios (compra y venta)
- ✅ Cálculo automático de márgenes de ganancia
- ✅ Asociación con proveedores

### 2. Movimientos de Inventario
- ✅ Registro de entradas y salidas
- ✅ Historial completo de movimientos
- ✅ Cálculo automático de inversión e ingresos
- ✅ Seguimiento de costos y ganancias
- ✅ Análisis de rentabilidad por producto

### 3. Sistema de Usuarios
- ✅ Autenticación segura con hashing de contraseñas
- ✅ Roles diferenciados (Administrador y Empleado)
- ✅ Control de acceso basado en roles
- ✅ Gestión de permisos

### 4. Promociones
- ✅ Creación de descuentos por producto
- ✅ Programación de fechas de vigencia
- ✅ Cálculo automático de precios promocionales
- ✅ Indicadores visuales de promociones activas

### 5. Domicilios
- ✅ Registro de entregas a domicilio
- ✅ Asignación de conductores
- ✅ Control de matrícula de vehículos
- ✅ Descuento automático de inventario
- ✅ Registro de movimientos de venta

### 6. Devoluciones
- ✅ Registro de devoluciones con motivos
- ✅ Reintegro automático al inventario
- ✅ Seguimiento de valores devueltos
- ✅ Estadísticas de devoluciones

### 7. Gestión de Proveedores
- ✅ Base de datos de proveedores
- ✅ Información de contacto
- ✅ Asociación con productos
- ✅ Seguimiento de productos por proveedor

### 8. Vendedores Ambulantes
- ✅ Registro de vendedores
- ✅ Asignación de zonas
- ✅ Vinculación con usuarios del sistema

### 9. Categorías
- ✅ Organización de productos
- ✅ Contador de productos por categoría
- ✅ Gestión flexible de categorías

### 10. Análisis y Reportes
- ✅ Dashboard con estadísticas en tiempo real
- ✅ Gráficos interactivos
- ✅ Resumen de últimos 30 días
- ✅ Cálculo de ganancia potencial
- ✅ Análisis de valor de inventario
- ✅ Generador de reportes personalizados

---

## 🏗️ Arquitectura del Sistema

### Patrón de Arquitectura

El sistema implementa una arquitectura **MVC (Model-View-Controller)** adaptada:

```
┌─────────────────────────────────────────────────────────────┐
│                        FRONTEND                             │
│  (HTML + CSS + JavaScript + Bootstrap)                      │
│  - Panel de administración                                  │
│  - Interfaces de usuario                                    │
│  - Componentes interactivos                                 │
└──────────────────┬──────────────────────────────────────────┘
                   │ AJAX/Fetch API
                   ↓
┌─────────────────────────────────────────────────────────────┐
│                      API ENDPOINTS                          │
│  (PHP - Carpeta /ajax/)                                     │
│  - Procesamiento de solicitudes                             │
│  - Validación de datos                                      │
│  - Lógica de negocio                                        │
└──────────────────┬──────────────────────────────────────────┘
                   │ pg_query / pg_query_params
                   ↓
┌─────────────────────────────────────────────────────────────┐
│                   BASE DE DATOS                             │
│  (PostgreSQL)                                               │
│  - Almacenamiento persistente                               │
│  - Integridad referencial                                   │
│  - Transacciones ACID                                       │
└─────────────────────────────────────────────────────────────┘
```

### Componentes Principales

#### 1. Capa de Presentación (Frontend)
- **Ubicación**: Archivos `.php` en raíz
- **Responsabilidad**: Interfaz de usuario, visualización de datos
- **Tecnologías**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Características**:
  - Diseño responsive
  - Animaciones CSS
  - Validación de formularios en cliente
  - Actualización dinámica sin recargar página

#### 2. Capa de Lógica (Backend)
- **Ubicación**: Carpeta `/ajax/`
- **Responsabilidad**: Procesamiento de datos, validaciones, lógica de negocio
- **Tecnologías**: PHP 8.2
- **Características**:
  - Endpoints RESTful
  - Respuestas JSON
  - Manejo de errores
  - Validaciones de seguridad

#### 3. Capa de Datos (Database)
- **Ubicación**: PostgreSQL (Supabase)
- **Responsabilidad**: Persistencia, integridad de datos
- **Características**:
  - Relaciones entre tablas
  - Constraints y validaciones
  - Transacciones
  - Índices para optimización

---

## 💻 Requisitos del Sistema

### Requisitos de Software

#### Para Desarrollo Local
- **PHP**: >= 8.2
- **PostgreSQL**: >= 15.0
- **Servidor Web**: Apache 2.4 o Nginx
- **Docker**: >= 20.10 (opcional)
- **Docker Compose**: >= 2.0 (opcional)
- **Node.js**: >= 16.0 (para herramientas de desarrollo)
- **Composer**: >= 2.0 (para dependencias PHP)

#### Extensiones PHP Requeridas
```ini
php-pgsql
php-pdo
php-pdo_pgsql
php-json
php-mbstring
php-session
```

### Requisitos de Hardware

#### Mínimos
- **CPU**: 1 core
- **RAM**: 512 MB
- **Almacenamiento**: 1 GB

#### Recomendados
- **CPU**: 2 cores
- **RAM**: 2 GB
- **Almacenamiento**: 5 GB

---

## 🚀 Instalación y Configuración

### Opción 1: Instalación con Docker (Recomendada)

#### Paso 1: Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/mi-bollito.git
cd mi-bollito
```

#### Paso 2: Configurar Variables de Entorno
```bash
# Crear archivo .env (opcional)
cp .env.example .env

# Editar variables de conexión si es necesario
nano .env
```

#### Paso 3: Levantar los Contenedores
```bash
docker-compose up -d
```

#### Paso 4: Acceder a la Aplicación
Abrir navegador en: `http://localhost:8080`

### Opción 2: Instalación Manual

#### Paso 1: Instalar Dependencias
```bash
# En Ubuntu/Debian
sudo apt update
sudo apt install php8.2 php8.2-pgsql php8.2-pdo apache2 postgresql-15

# En macOS con Homebrew
brew install php@8.2 postgresql@15 apache2
```

#### Paso 2: Configurar PostgreSQL
```sql
-- Conectar a PostgreSQL
psql -U postgres

-- Crear base de datos
CREATE DATABASE bd_mibollito;

-- Crear usuario
CREATE USER mibollito_user WITH PASSWORD 'tu_password_seguro';

-- Otorgar privilegios
GRANT ALL PRIVILEGES ON DATABASE bd_mibollito TO mibollito_user;

-- Salir
\q
```

#### Paso 3: Importar Esquema de Base de Datos
```bash
psql -U mibollito_user -d bd_mibollito -f bd_mibollito_postgresql.sql
```

#### Paso 4: Configurar PHP
Editar `conexion.php`:
```php
$host = 'localhost';
$port = '5432';
$dbname = 'bd_mibollito';
$user = 'mibollito_user';
$password = 'tu_password_seguro';
```

#### Paso 5: Configurar Apache
```bash
# Copiar archivos al directorio web
sudo cp -r /ruta/al/proyecto /var/www/html/mibollito

# Configurar permisos
sudo chown -R www-data:www-data /var/www/html/mibollito
sudo chmod -R 755 /var/www/html/mibollito

# Reiniciar Apache
sudo systemctl restart apache2
```

#### Paso 6: Acceder
Abrir navegador en: `http://localhost/mibollito`

### Opción 3: Despliegue en Supabase (Producción)

La aplicación está configurada para funcionar con Supabase:

#### Paso 1: Crear Proyecto en Supabase
1. Ir a [supabase.com](https://supabase.com)
2. Crear nuevo proyecto
3. Anotar credenciales de conexión

#### Paso 2: Configurar conexion.php
```php
$host = 'aws-0-us-west-2.pooler.supabase.com';
$port = '5432';
$dbname = 'postgres';
$user = 'postgres.tu_proyecto';
$password = 'tu_password_supabase';
```

#### Paso 3: Importar Base de Datos
Usar el SQL Editor de Supabase para ejecutar el schema.

---

## 📁 Estructura del Proyecto

```
mi-bollito/
│
├── 📄 index.php                    # Redirección a login
├── 📄 login.php                    # Página de inicio de sesión
├── 📄 registro.php                 # Página de registro (deshabilitada)
├── 📄 validar_login.php           # Procesamiento de login
├── 📄 guardar_registro.php        # Procesamiento de registro
├── 📄 logout.php                   # Cierre de sesión
├── 📄 conexion.php                 # Configuración de BD
│
├── 📂 includes/                    # Componentes compartidos
│   ├── 📄 header.php              # Header y sidebar
│   └── 📄 footer.php              # Footer
│
├── 📂 ajax/                        # API Endpoints
│   ├── 📄 productos.php           # CRUD productos
│   ├── 📄 categorias.php          # CRUD categorías
│   ├── 📄 proveedores.php         # CRUD proveedores
│   ├── 📄 usuarios.php            # CRUD usuarios
│   ├── 📄 promociones.php         # CRUD promociones
│   ├── 📄 vendedores.php          # CRUD vendedores
│   ├── 📄 domicilios.php          # CRUD domicilios
│   ├── 📄 devoluciones.php        # CRUD devoluciones
│   └── 📄 movimientos.php         # Movimientos y estadísticas
│
├── 📂 assets/                      # Recursos estáticos
│   └── 📂 js/
│       └── 📄 main.js             # Funciones JavaScript globales
│
├── 📂 scripts/                     # Scripts auxiliares
│   └── (scripts de utilidad)
│
├── 📄 panel.php                    # Dashboard principal
├── 📄 inventario.php              # Gestión de inventario
├── 📄 estadisticas.php            # Reportes y gráficos
├── 📄 domicilios.php              # Gestión de domicilios
├── 📄 devoluciones.php            # Gestión de devoluciones
├── 📄 reportes.php                # Generador de reportes
├── 📄 usuarios.php                # Gestión de usuarios (admin)
├── 📄 categorias.php              # Gestión de categorías (admin)
├── 📄 proveedores.php             # Gestión de proveedores (admin)
├── 📄 vendedores.php              # Gestión de vendedores (admin)
├── 📄 promociones.php             # Gestión de promociones (admin)
│
├── 📄 Dockerfile                   # Configuración Docker PHP
├── 📄 docker-compose.yml          # Orquestación de contenedores
├── 📄 render.yaml                 # Configuración para Render
└── 📄 bd_mibollito_postgresql.sql # Schema de base de datos
```

---

## 🔧 Módulos del Sistema

### 1. Módulo de Autenticación

**Archivos**: `login.php`, `validar_login.php`, `logout.php`

#### Funcionalidades
- Login con correo y contraseña
- Validación de credenciales
- Hashing de contraseñas con `password_hash()`
- Manejo de sesiones seguras
- Regeneración de ID de sesión
- Mensajes de error y éxito

#### Flujo de Autenticación
```
Usuario ingresa credenciales
         ↓
validar_login.php
         ↓
Busca usuario en BD
         ↓
Verifica contraseña (password_verify)
         ↓
Crea sesión + asigna variables
         ↓
Redirige a panel.php
```

---

### 2. Módulo de Inventario

**Archivos**: `inventario.php`, `ajax/productos.php`

#### Funcionalidades
- **Crear producto**: nombre, descripción, precios, stock, categoría, proveedor
- **Editar producto**: actualización completa de datos
- **Eliminar producto**: elimina producto y movimientos asociados
- **Listar productos**: tabla con información completa
- **Filtrar por categoría y proveedor**
- **Alertas de stock bajo**: notificaciones automáticas
- **Cálculo de márgenes**: muestra ganancia por producto

#### Endpoint: `ajax/productos.php`

##### Actions Disponibles

**`list`** - Listar todos los productos
```javascript
fetch('ajax/productos.php?action=list')
```
Respuesta:
```json
{
  "success": true,
  "products": [
    {
      "id_producto": 1,
      "nombre": "Producto A",
      "precio_compra": 1000,
      "precio_venta": 1500,
      "stock": 50,
      "categoria": "Bebidas",
      "proveedor": "Proveedor XYZ"
    }
  ]
}
```

**`get`** - Obtener un producto específico
```javascript
fetch('ajax/productos.php?action=get&id=1')
```

**`create`** - Crear nuevo producto
```javascript
const formData = new FormData();
formData.append('action', 'create');
formData.append('nombre', 'Producto Nuevo');
formData.append('precio_compra', '1000');
formData.append('precio_venta', '1500');
formData.append('stock', '100');
formData.append('id_categoria', '1');

fetch('ajax/productos.php', { method: 'POST', body: formData });
```

**`update`** - Actualizar producto
**`delete`** - Eliminar producto
**`categories`** - Listar categorías
**`proveedores`** - Listar proveedores
**`check_stock`** - Verificar productos con stock bajo

---

### 3. Módulo de Movimientos

**Archivos**: `estadisticas.php`, `ajax/movimientos.php`

#### Funcionalidades
- Registro automático de entradas y salidas
- Cálculo de inversión (entradas × precio_compra)
- Cálculo de ingresos (salidas × precio_venta)
- Cálculo de ganancias reales
- Historial de movimientos con filtros
- Gráficos interactivos con Chart.js

#### Tipos de Movimientos
- **Entrada**: Compra de productos, devoluciones
- **Salida**: Ventas, domicilios

#### Endpoint: `ajax/movimientos.php`

**`resumen`** - Estadísticas de últimos 30 días
```json
{
  "success": true,
  "resumen": {
    "inversion": 50000,
    "ingresos": 75000,
    "costo_ventas": 45000,
    "ganancia": 30000,
    "margen": 40.0
  }
}
```

**`list`** - Listar movimientos con detalles
**`dashboard`** - Estadísticas del panel principal

---

### 4. Módulo de Domicilios

**Archivos**: `domicilios.php`, `ajax/domicilios.php`

#### Funcionalidades
- Registro de entregas
- Asignación de conductor y vehículo
- Selección de producto y cantidad
- Descuento automático de inventario
- Cálculo de ganancia por domicilio
- Registro de movimiento de salida

#### Proceso de Domicilio
```
1. Usuario selecciona producto y cantidad
2. Sistema verifica stock disponible
3. Sistema calcula valor de venta y ganancia
4. Usuario registra conductor y matrícula
5. Sistema descuenta stock
6. Sistema registra movimiento de salida
7. Sistema guarda domicilio en BD
```

---

### 5. Módulo de Devoluciones

**Archivos**: `devoluciones.php`, `ajax/devoluciones.php`

#### Funcionalidades
- Registro de devoluciones con motivo
- Reintegro automático al inventario
- Movimiento de entrada automático
- Estadísticas de devoluciones
- Valor total devuelto

#### Motivos de Devolución
- Producto dañado
- Producto vencido
- Error en el pedido
- Cliente insatisfecho
- Defecto de fábrica
- Otro

---

### 6. Módulo de Promociones

**Archivos**: `promociones.php`, `ajax/promociones.php`

#### Funcionalidades
- Creación de descuentos porcentuales
- Fechas de inicio y fin
- Validación de períodos
- Cálculo automático de precio promocional
- Estados: activa, pendiente, expirada
- Indicadores visuales en inventario

#### Validaciones
- Descuento entre 1% y 99%
- Fecha fin > fecha inicio
- Sin solapamiento de promociones por producto

---

### 7. Módulo de Usuarios

**Archivos**: `usuarios.php`, `ajax/usuarios.php`

#### Roles del Sistema

**Administrador**
- Acceso completo al sistema
- Gestión de usuarios
- Configuración de categorías, proveedores, vendedores
- Creación de promociones
- Acceso a todos los módulos

**Empleado**
- Gestión de inventario
- Registro de domicilios
- Registro de devoluciones
- Visualización de estadísticas
- Sin acceso a módulos administrativos

#### Funcionalidades
- CRUD de usuarios
- Asignación de roles
- Cambio de contraseñas
- Validación de seguridad

---

### 8. Módulo de Categorías

**Archivos**: `categorias.php`, `ajax/categorias.php`

#### Funcionalidades
- CRUD de categorías
- Contador de productos por categoría
- Desvinculación automática al eliminar

---

### 9. Módulo de Proveedores

**Archivos**: `proveedores.php`, `ajax/proveedores.php`

#### Funcionalidades
- Gestión de información de contacto
- Asociación con productos
- Contador de productos por proveedor

---

### 10. Módulo de Vendedores

**Archivos**: `vendedores.php`, `ajax/vendedores.php`

#### Funcionalidades
- Registro de vendedores ambulantes
- Asignación de zonas
- Vinculación con usuarios del sistema

---

### 11. Módulo de Reportes

**Archivos**: `reportes.php`

#### Tipos de Reportes Disponibles
1. **Inventario Completo**: Todos los productos con valoración
2. **Movimientos**: Entradas y salidas por período
3. **Ventas y Ganancias**: Análisis de rentabilidad
4. **Stock Bajo**: Productos que necesitan reabastecimiento
5. **Proveedores**: Listado con productos asociados
6. **Devoluciones**: Historial con motivos

#### Formatos de Exportación
- Excel (XLSX)
- PDF
- Vista previa en pantalla

---

## 🗄️ Base de Datos

### Modelo de Datos

#### Diagrama ER (Entidad-Relación)

```
┌─────────────┐       ┌──────────────┐       ┌─────────────┐
│  usuarios   │       │  productos   │       │ categorias  │
├─────────────┤       ├──────────────┤       ├─────────────┤
│ id_usuario  │───┐   │ id_producto  │   ┌───│id_categoria │
│ nombre      │   │   │ nombre       │   │   │ nombre      │
│ correo      │   │   │ precio_compra│   │   │ descripcion │
│ contrasena  │   │   │ precio_venta │   │   └─────────────┘
│ rol         │   │   │ stock        │   │
└─────────────┘   │   │ id_categoria │───┘
                  │   │ id_proveedor │───┐
                  │   └──────────────┘   │
                  │                      │
                  │   ┌──────────────┐   │   ┌─────────────┐
                  │   │ movimientos_ │   │   │ proveedores │
                  │   │  inventario  │   │   ├─────────────┤
                  │   ├──────────────┤   └───│id_proveedor │
                  │   │ id_movimiento│       │ nombre      │
                  │   │ id_producto  │───┐   │ contacto    │
                  │   │ tipo         │   │   │ telefono    │
                  │   │ cantidad     │   │   └─────────────┘
                  │   │ precio_unit  │   │
                  │   └──────────────┘   │
                  │                      │
                  │   ┌──────────────┐   │
                  └───│ devoluciones │   │
                      ├──────────────┤   │
                      │id_devolucion │   │
                      │ id_producto  │───┘
                      │ cantidad     │
                      │ motivo       │
                      │ id_usuario   │
                      └──────────────┘
```

### Tablas Principales

#### 1. usuarios
```sql
CREATE TABLE usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(100) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    rol VARCHAR(20) DEFAULT 'empleado',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 2. productos
```sql
CREATE TABLE productos (
    id_producto SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_compra DECIMAL(10,2) NOT NULL,
    precio_venta DECIMAL(10,2) NOT NULL,
    stock INTEGER DEFAULT 0,
    id_categoria INTEGER REFERENCES categorias(id_categoria),
    id_proveedor INTEGER REFERENCES proveedores(id_proveedor),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 3. categorias
```sql
CREATE TABLE categorias (
    id_categoria SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 4. proveedores
```sql
CREATE TABLE proveedores (
    id_proveedor SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    direccion TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 5. movimientos_inventario
```sql
CREATE TABLE movimientos_inventario (
    id_movimiento SERIAL PRIMARY KEY,
    id_producto INTEGER REFERENCES productos(id_producto),
    tipo VARCHAR(20) NOT NULL, -- 'entrada' o 'salida'
    cantidad INTEGER NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    descripcion TEXT,
    fecha_movimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 6. devoluciones
```sql
CREATE TABLE devoluciones (
    id_devolucion SERIAL PRIMARY KEY,
    id_producto INTEGER REFERENCES productos(id_producto),
    cantidad INTEGER NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    observaciones TEXT,
    precio_unitario DECIMAL(10,2) NOT NULL,
    id_usuario INTEGER REFERENCES usuarios(id_usuario),
    fecha_devolucion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 7. promociones
```sql
CREATE TABLE promociones (
    id_promocion SERIAL PRIMARY KEY,
    id_producto INTEGER REFERENCES productos(id_producto),
    descuento DECIMAL(5,2) NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 8. domicilios
```sql
CREATE TABLE domicilios (
    id_domicilio SERIAL PRIMARY KEY,
    conductor_responsable VARCHAR(100) NOT NULL,
    matricula_vehiculo VARCHAR(20) NOT NULL,
    producto VARCHAR(100) NOT NULL,
    observaciones TEXT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### 9. vendedores_ambulantes
```sql
CREATE TABLE vendedores_ambulantes (
    id_vendedor SERIAL PRIMARY KEY,
    id_usuario INTEGER REFERENCES usuarios(id_usuario),
    zona VARCHAR(100) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## 🔌 API y Endpoints

### Convenciones

Todos los endpoints siguen el patrón:
```
ajax/{modulo}.php?action={accion}
```

### Formato de Respuesta

Todas las respuestas son JSON:
```json
{
  "success": true,
  "message": "Operación exitosa",
  "data": { }
}
```

### Lista de Endpoints

#### Productos (`ajax/productos.php`)
- `GET ?action=list` - Listar productos
- `GET ?action=get&id={id}` - Obtener producto
- `GET ?action=categories` - Listar categorías
- `GET ?action=proveedores` - Listar proveedores
- `GET ?action=check_stock` - Productos con stock bajo
- `POST action=create` - Crear producto
- `POST action=update` - Actualizar producto
- `POST action=delete` - Eliminar producto

#### Categorías (`ajax/categorias.php`)
- `GET ?action=list` - Listar categorías
- `POST action=create` - Crear categoría
- `POST action=update` - Actualizar categoría
- `POST action=delete` - Eliminar categoría

#### Proveedores (`ajax/proveedores.php`)
- `GET ?action=list` - Listar proveedores
- `POST action=create` - Crear proveedor
- `POST action=update` - Actualizar proveedor
- `POST action=delete` - Eliminar proveedor

#### Usuarios (`ajax/usuarios.php`)
- `GET ?action=list` - Listar usuarios
- `POST action=create` - Crear usuario
- `POST action=update` - Actualizar usuario
- `POST action=delete` - Eliminar usuario

#### Promociones (`ajax/promociones.php`)
- `GET ?action=list` - Listar promociones
- `GET ?action=activas` - Listar promociones activas
- `POST action=create` - Crear promoción
- `POST action=update` - Actualizar promoción
- `POST action=delete` - Eliminar promoción

#### Devoluciones (`ajax/devoluciones.php`)
- `GET ?action=list` - Listar devoluciones
- `GET ?action=resumen` - Resumen de devoluciones
- `POST action=create` - Crear devolución
- `POST action=delete` - Eliminar devolución

#### Movimientos (`ajax/movimientos.php`)
- `GET ?action=list` - Listar movimientos
- `GET ?action=resumen` - Resumen financiero
- `GET ?action=dashboard` - Estadísticas del dashboard

#### Vendedores (`ajax/vendedores.php`)
- `GET ?action=list` - Listar vendedores
- `POST action=create` - Crear vendedor
- `POST action=update` - Actualizar vendedor
- `POST action=delete` - Eliminar vendedor

---

## 🔒 Seguridad

### Medidas Implementadas

#### 1. Autenticación y Sesiones
- ✅ Contraseñas hasheadas con `password_hash()` (bcrypt)
- ✅ Validación con `password_verify()`
- ✅ Regeneración de ID de sesión tras login
- ✅ Variables de sesión para control de acceso
- ✅ Destrucción completa de sesión en logout

#### 2. Control de Acceso
- ✅ Verificación de sesión en cada página
- ✅ Control basado en roles (RBAC)
- ✅ Redirección automática si no autenticado
- ✅ Restricción de módulos según rol

#### 3. Validación de Datos
- ✅ Validación en cliente (JavaScript)
- ✅ Validación en servidor (PHP)
- ✅ Prepared statements (pg_query_params)
- ✅ Escapado de strings (pg_escape_string)
- ✅ Validación de tipos de datos

#### 4. SQL Injection Prevention
```php
// ❌ MAL - Vulnerable a SQL Injection
$query = "SELECT * FROM usuarios WHERE correo = '$correo'";

// ✅ BIEN - Uso de prepared statements
$query = "SELECT * FROM usuarios WHERE correo = $1";
$result = pg_query_params($conn, $query, [$correo]);
```

#### 5. XSS Prevention
```php
// Escapar output en HTML
echo htmlspecialchars($user_input);

// O usar funciones de sanitización
$clean = filter_var($input, FILTER_SANITIZE_STRING);
```

#### 6. CSRF Protection
- Headers de respuesta JSON
- Verificación de origen de requests
- Tokens de sesión

### Recomendaciones Adicionales

#### Para Producción
1. **HTTPS obligatorio**: Configurar SSL/TLS
2. **Límites de intentos**: Implementar rate limiting
3. **Logs de seguridad**: Registrar accesos y errores
4. **Backups automáticos**: Programar respaldos de BD
5. **Actualizar dependencias**: Mantener PHP y PostgreSQL actualizados
6. **Variables de entorno**: No hardcodear credenciales
7. **Firewall**: Configurar reglas de acceso a BD

---

## 📖 Guía de Uso

### Primer Acceso al Sistema

#### 1. Crear Usuario Administrador (Manual)
```sql
-- Conectar a PostgreSQL
psql -U postgres -d bd_mibollito

-- Crear usuario admin (contraseña: Admin123!)
INSERT INTO usuarios (nombre, correo, contrasena, rol) 
VALUES (
  'Administrador', 
  'admin@mibollito.com', 
  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
  'administrador'
);
```

#### 2. Iniciar Sesión
1. Ir a `login.php`
2. Ingresar credenciales
3. Acceder al dashboard

### Flujo de Trabajo Típico

#### Configuración Inicial (Solo una vez)

**1. Crear Categorías**
- Ir a **Administración → Categorías**
- Crear categorías como: Bebidas, Snacks, Dulces, etc.

**2. Registrar Proveedores**
- Ir a **Administración → Proveedores**
- Agregar proveedores con información de contacto

**3. Crear Usuarios Adicionales**
- Ir a **Administración → Usuarios**
- Crear empleados según sea necesario

#### Operación Diaria

**1. Registrar Productos Nuevos**
- Ir a **Inventario**
- Crear producto con todos los datos
- El sistema registra automáticamente el movimiento de entrada

**2. Actualizar Stock (Compras)**
- Editar producto
- Modificar cantidad de stock
- El sistema registra el movimiento automáticamente

**3. Registrar Ventas (Domicilios)**
- Ir a **Domicilios**
- Crear nuevo domicilio
- Seleccionar producto y cantidad
- Sistema descuenta stock automáticamente

**4. Procesar Devoluciones**
- Ir a **Devoluciones**
- Registrar devolución con motivo
- Sistema reintegra stock automáticamente

**5. Crear Promociones**
- Ir a **Administración → Promociones**
- Seleccionar producto y descuento
- Definir fechas de vigencia

**6. Revisar Estadísticas**
- Ir a **Estadísticas**
- Ver gráficos y resúmenes
- Analizar rentabilidad

**7. Generar Reportes**
- Ir a **Reportes**
- Seleccionar tipo de reporte
- Aplicar filtros
- Exportar o visualizar

---

## 🛠️ Mantenimiento y Solución de Problemas

### Problemas Comunes

#### 1. Error de Conexión a Base de Datos

**Síntoma**: "Error de conexión: Connection failed"

**Solución**:
```bash
# Verificar que PostgreSQL esté corriendo
sudo systemctl status postgresql

# Verificar credenciales en conexion.php
# Revisar logs de PostgreSQL
sudo tail -f /var/log/postgresql/postgresql-15-main.log
```

#### 2. Sesión No Inicia

**Síntoma**: Redirige siempre a login

**Solución**:
```php
// Verificar permisos de carpeta de sesiones
sudo chmod 777 /var/lib/php/sessions

// O configurar ruta personalizada en php.ini
session.save_path = "/tmp"
```

#### 3. Stock No Se Actualiza

**Síntoma**: El stock no cambia al crear domicilio

**Solución**:
- Verificar que el producto exista
- Revisar permisos de usuario en BD
- Verificar transacciones en movimientos_inventario

#### 4. Promociones No Se Muestran

**Síntoma**: Badge de promoción no aparece

**Solución**:
- Verificar fechas de promoción
- Revisar endpoint `ajax/promociones.php?action=activas`
- Verificar JavaScript en consola del navegador

### Mantenimiento de Base de Datos

#### Backup Manual
```bash
# Backup completo
pg_dump -U mibollito_user bd_mibollito > backup_$(date +%Y%m%d).sql

# Restaurar backup
psql -U mibollito_user bd_mibollito < backup_20250101.sql
```

#### Limpieza de Datos

```sql
-- Eliminar movimientos antiguos (más de 1 año)
DELETE FROM movimientos_inventario 
WHERE fecha_movimiento < CURRENT_DATE - INTERVAL '1 year';

-- Eliminar promociones expiradas
DELETE FROM promociones 
WHERE fecha_fin < CURRENT_DATE - INTERVAL '6 months';
```

#### Optimización

```sql
-- Actualizar estadísticas de tablas
ANALYZE productos;
ANALYZE movimientos_inventario;

-- Reindexar
REINDEX TABLE productos;
REINDEX TABLE movimientos_inventario;

-- Vacuum
VACUUM FULL;
```

### Logs y Debug

#### Habilitar Logs de PHP
```ini
; En php.ini
error_reporting = E_ALL
display_errors = On
log_errors = On
error_log = /var/log/php_errors.log
```

#### Habilitar Logs de PostgreSQL
```ini
; En postgresql.conf
logging_collector = on
log_directory = 'pg_log'
log_filename = 'postgresql-%Y-%m-%d_%H%M%S.log'
log_statement = 'all'
```

---

## 🚀 Roadmap y Mejoras Futuras

### Versión 2.0 (Corto Plazo)

#### Mejoras de Funcionalidad
- [ ] **Código de barras**: Lector y generador de códigos
- [ ] **Facturación electrónica**: Integración con DIAN (Colombia)
- [ ] **Múltiples sucursales**: Gestión multi-tienda
- [ ] **Punto de venta (POS)**: Interfaz de caja rápida
- [ ] **Inventario por lotes**: Control de vencimientos
- [ ] **Alertas por correo**: Notificaciones automáticas

#### Mejoras Técnicas
- [ ] **API RESTful completa**: Documentación con Swagger
- [ ] **WebSockets**: Actualizaciones en tiempo real
- [ ] **Tests automatizados**: PHPUnit y Jest
- [ ] **CI/CD**: Pipeline de despliegue automático
- [ ] **Docker Compose mejorado**: Nginx + PHP-FPM
- [ ] **Caché**: Redis para optimización

### Versión 3.0 (Mediano Plazo)

#### Nuevos Módulos
- [ ] **App móvil**: React Native o Flutter
- [ ] **Gestión de clientes**: CRM integrado
- [ ] **Programa de fidelidad**: Sistema de puntos
- [ ] **Pedidos online**: E-commerce integrado
- [ ] **Análisis predictivo**: ML para forecasting
- [ ] **Integración contable**: Sincronización con software contable

#### Mejoras de UX/UI
- [ ] **Temas personalizables**: Dark mode y temas custom
- [ ] **Dashboard personalizable**: Widgets arrastrables
- [ ] **Reportes avanzados**: Más formatos y gráficos
- [ ] **Accesibilidad**: WCAG 2.1 AA compliance
- [ ] **PWA**: Funcionamiento offline

### Versión 4.0 (Largo Plazo)

#### Inteligencia de Negocio
- [ ] **BI Dashboard**: Análisis avanzado con KPIs
- [ ] **Recomendaciones IA**: Sugerencias de compra
- [ ] **Detección de fraude**: Algoritmos de anomalías
- [ ] **Optimización de precios**: Pricing dinámico
- [ ] **Predicción de demanda**: Forecasting con ML

#### Escalabilidad
- [ ] **Microservicios**: Arquitectura distribuida
- [ ] **Kubernetes**: Orquestación de contenedores
- [ ] **Multi-tenancy**: SaaS completo
- [ ] **API Gateway**: Gestión centralizada de APIs
- [ ] **Event sourcing**: Sistema basado en eventos

---

## 📞 Soporte y Contacto

### Recursos

- **Documentación**: Esta guía
- **Repositorio**: GitHub (privado)
- **Issues**: Sistema de tickets
- **Wiki**: Documentación adicional

### Contribuir

Si deseas contribuir al proyecto:

1. Fork del repositorio
2. Crear branch de feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit de cambios (`git commit -am 'Agrega nueva funcionalidad'`)
4. Push al branch (`git push origin feature/nueva-funcionalidad`)
5. Crear Pull Request

### Licencia

Este proyecto es privado y propietario. Todos los derechos reservados.

---

## 📝 Notas Finales

### Créditos

**Desarrollado por**: [Tu Nombre]
**Versión**: 1.0.0
**Fecha**: Febrero 2025
**Stack**: PHP 8.2 + PostgreSQL 15 + Bootstrap 5

### Agradecimientos

- Bootstrap por el framework CSS
- Bootstrap Icons por los iconos
- Chart.js por los gráficos
- Supabase por el hosting de BD
- Docker por la containerización

---

**© 2025 Mi Bollito - Sistema de Gestión Empresarial**
