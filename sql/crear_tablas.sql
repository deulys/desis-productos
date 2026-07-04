-- Crea las tablas necesarias para registrar productos.
-- Ejecutar antes de cargar los datos iniciales.

CREATE TABLE IF NOT EXISTS bodegas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS sucursales (
    id SERIAL PRIMARY KEY,
    bodega_id INTEGER NOT NULL REFERENCES bodegas(id),
    nombre VARCHAR(100) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS monedas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    codigo VARCHAR(10) NOT NULL UNIQUE,
    activo BOOLEAN NOT NULL DEFAULT true
);

CREATE TABLE IF NOT EXISTS productos (
    id SERIAL PRIMARY KEY,
    codigo VARCHAR(15) NOT NULL UNIQUE,
    nombre VARCHAR(50) NOT NULL,
    bodega_id INTEGER NOT NULL REFERENCES bodegas(id),
    sucursal_id INTEGER NOT NULL REFERENCES sucursales(id),
    moneda_id INTEGER NOT NULL REFERENCES monedas(id),
    precio NUMERIC(12, 2) NOT NULL CHECK (precio > 0),
    descripcion TEXT NOT NULL,
    fecha_creacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_codigo_formato CHECK (codigo ~ '^(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9]{5,15}$'),
    CONSTRAINT chk_nombre_largo CHECK (char_length(nombre) BETWEEN 2 AND 50),
    CONSTRAINT chk_descripcion_largo CHECK (char_length(descripcion) BETWEEN 10 AND 1000)
);

CREATE TABLE IF NOT EXISTS producto_materiales (
    id SERIAL PRIMARY KEY,
    producto_id INTEGER NOT NULL REFERENCES productos(id) ON DELETE CASCADE,
    material VARCHAR(30) NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_sucursales_bodega_id ON sucursales(bodega_id);
CREATE INDEX IF NOT EXISTS idx_productos_codigo ON productos(codigo);
