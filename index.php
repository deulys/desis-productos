<?php
// Punto de entrada principal de la aplicación.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Producto</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <main class="page-wrapper">
        <section class="product-card">
            <h1>Formulario de Producto</h1>

            <form id="productForm" novalidate>
                <div class="form-grid">
                    <div class="form-group">
                        <label for="codigo">Código</label>
                        <input type="text" id="codigo" name="codigo" maxlength="15" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre" maxlength="50" autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="bodega_id">Bodega</label>
                        <select id="bodega_id" name="bodega_id">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="sucursal_id">Sucursal</label>
                        <select id="sucursal_id" name="sucursal_id">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="moneda_id">Moneda</label>
                        <select id="moneda_id" name="moneda_id">
                            <option value=""></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="precio">Precio</label>
                        <input type="text" id="precio" name="precio" autocomplete="off">
                    </div>
                </div>

                <fieldset class="materials-fieldset">
                    <legend>Material del Producto</legend>
                    <label><input type="checkbox" name="materiales[]" value="Plástico"> Plástico</label>
                    <label><input type="checkbox" name="materiales[]" value="Metal"> Metal</label>
                    <label><input type="checkbox" name="materiales[]" value="Madera"> Madera</label>
                    <label><input type="checkbox" name="materiales[]" value="Vidrio"> Vidrio</label>
                    <label><input type="checkbox" name="materiales[]" value="Textil"> Textil</label>
                </fieldset>

                <div class="form-group full-width">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" maxlength="1000"></textarea>
                </div>

                <div class="actions">
                    <button type="submit" id="saveButton">Guardar Producto</button>
                </div>
            </form>
        </section>
    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>
