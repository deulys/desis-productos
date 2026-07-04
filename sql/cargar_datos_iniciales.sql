-- Datos base para que los selects del formulario tengan opciones al iniciar.

INSERT INTO bodegas (nombre) VALUES
('Bodega 1'),
('Bodega 2'),
('Bodega 3')
ON CONFLICT DO NOTHING;

INSERT INTO sucursales (bodega_id, nombre) VALUES
(1, 'Sucursal 1'),
(1, 'Sucursal 2'),
(2, 'Sucursal 3'),
(2, 'Sucursal 4'),
(3, 'Sucursal 5')
ON CONFLICT DO NOTHING;

INSERT INTO monedas (nombre, codigo) VALUES
('PESO CHILENO', 'CLP'),
('DÓLAR', 'USD'),
('EURO', 'EUR')
ON CONFLICT (codigo) DO NOTHING;
