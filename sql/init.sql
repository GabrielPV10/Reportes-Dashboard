-- Usar la base de datos correcta
USE reporte_db;
-- #####################################################################
-- ## Script para la Creación de la BD del Dashboard (Versión Multitenant) ##
-- #####################################################################
-- *********************************************************************
-- SECCIÓN 1: TABLAS DE SEGURIDAD Y GESTIÓN
-- *********************************************************************

-- Tabla para definir las compañías o "inquilinos" del sistema
CREATE TABLE DimCompania (
    compania_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_compania VARCHAR(255) NOT NULL UNIQUE
);

-- Tabla para definir los roles del sistema
CREATE TABLE Roles (
    rol_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla para almacenar los usuarios y sus credenciales
CREATE TABLE Usuarios (
    usuario_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_completo VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol_id INT,
    compania_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES Roles(rol_id),
    FOREIGN KEY (compania_id) REFERENCES DimCompania(compania_id)
);

-- *********************************************************************
-- SECCIÓN 2: TABLAS DE DIMENSIONES (EL CONTEXTO DE LOS DATOS)
-- *********************************************************************

CREATE TABLE DimTiempo (
    fecha_id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE NOT NULL UNIQUE,
    anio INT NOT NULL,
    mes INT NOT NULL,
    nombre_mes VARCHAR(20) NOT NULL,
    dia INT NOT NULL,
    nombre_dia VARCHAR(20) NOT NULL,
    trimestre INT NOT NULL
);

CREATE TABLE DimUbicacion (
    ubicacion_id INT PRIMARY KEY AUTO_INCREMENT,
    tienda VARCHAR(100),
    ciudad VARCHAR(100) NOT NULL,
    pais VARCHAR(100) NOT NULL
);

CREATE TABLE DimProducto (
    producto_id INT PRIMARY KEY AUTO_INCREMENT,
    sku VARCHAR(50),
    nombre_producto VARCHAR(255) NOT NULL,
    categoria VARCHAR(100),
    marca VARCHAR(100),
    compania_id INT,
    FOREIGN KEY (compania_id) REFERENCES DimCompania(compania_id)
);

CREATE TABLE DimCliente (
    cliente_id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_cliente VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    tipo_cliente VARCHAR(50),
    compania_id INT,
    FOREIGN KEY (compania_id) REFERENCES DimCompania(compania_id)
);

-- *********************************************************************
-- SECCIÓN 3: TABLA DE HECHOS (LAS MÉTRICAS NUMÉRICAS)
-- *********************************************************************

CREATE TABLE FactVentas (
    venta_id BIGINT PRIMARY KEY AUTO_INCREMENT,
    fecha_id INT,
    producto_id INT,
    cliente_id INT,
    ubicacion_id INT,
    compania_id INT,
    cantidad_vendida INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    monto_total DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (fecha_id) REFERENCES DimTiempo(fecha_id),
    FOREIGN KEY (producto_id) REFERENCES DimProducto(producto_id),
    FOREIGN KEY (cliente_id) REFERENCES DimCliente(cliente_id),
    FOREIGN KEY (ubicacion_id) REFERENCES DimUbicacion(ubicacion_id),
    FOREIGN KEY (compania_id) REFERENCES DimCompania(compania_id)
);

-- *********************************************************************
-- SECCIÓN 4: INSERCIÓN DE DATOS INICIALES NECESARIOS
-- *********************************************************************

-- Insertar los roles que aparecerán en el formulario de registro
INSERT INTO Roles (rol_id, nombre_rol) VALUES (1, 'Administrador'), (2, 'Analista de Datos');
-- --- Fin del Script ---