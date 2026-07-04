const API_BASE = 'api';

const productForm = document.getElementById('productForm');
const saveButton = document.getElementById('saveButton');
const bodegaSelect = document.getElementById('bodega_id');
const sucursalSelect = document.getElementById('sucursal_id');
const monedaSelect = document.getElementById('moneda_id');

function ajaxRequest(method, url, data = null) {
    // Pequeño wrapper para centralizar las llamadas AJAX nativas del formulario.
    return new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open(method, url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        if (!(data instanceof FormData)) {
            xhr.setRequestHeader('Content-Type', 'application/json; charset=UTF-8');
        }

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;

            let response = null;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (error) {
                reject(new Error('Respuesta inválida del servidor.'));
                return;
            }

            if (xhr.status >= 200 && xhr.status < 300) {
                resolve(response);
            } else {
                reject(response);
            }
        };

        xhr.onerror = function () {
            reject(new Error('No fue posible conectar con el servidor.'));
        };

        xhr.send(data);
    });
}

function clearSelect(selectElement) {
    // Todos los selects deben partir con una opción vacía, tal como pide la prueba.
    selectElement.innerHTML = '';
    const emptyOption = document.createElement('option');
    emptyOption.value = '';
    emptyOption.textContent = '';
    selectElement.appendChild(emptyOption);
}

function fillSelect(selectElement, records) {
    // Recibe registros simples de la API: { id, nombre }.
    clearSelect(selectElement);
    records.forEach((record) => {
        const option = document.createElement('option');
        option.value = record.id;
        option.textContent = record.nombre;
        selectElement.appendChild(option);
    });
}

async function loadInitialData() {
    // Carga los catálogos que aparecen al abrir el formulario.
    try {
        const response = await ajaxRequest('GET', `${API_BASE}/catalogos.php`);
        fillSelect(bodegaSelect, response.data.bodegas);
        fillSelect(monedaSelect, response.data.monedas);
        clearSelect(sucursalSelect);
    } catch (error) {
        alert(error.message || 'Error al cargar los catálogos iniciales.');
    }
}

async function loadSucursalesByBodega(bodegaId) {
    // La sucursal depende de la bodega seleccionada.
    clearSelect(sucursalSelect);
    if (!bodegaId) return;

    try {
        const response = await ajaxRequest('GET', `${API_BASE}/sucursales.php?bodega_id=${encodeURIComponent(bodegaId)}`);
        fillSelect(sucursalSelect, response.data);
    } catch (error) {
        alert(error.message || 'Error al cargar las sucursales.');
    }
}

function getCheckedMaterials() {
    // Convierte los checkboxes marcados en un arreglo de materiales.
    return Array.from(document.querySelectorAll('input[name="materiales[]"]:checked')).map((item) => item.value);
}

function validateForm() {
    // Validaciones en cliente para mostrar mensajes antes de enviar al servidor.
    const codigo = document.getElementById('codigo').value.trim();
    const nombre = document.getElementById('nombre').value.trim();
    const bodegaId = bodegaSelect.value;
    const sucursalId = sucursalSelect.value;
    const monedaId = monedaSelect.value;
    const precio = document.getElementById('precio').value.trim();
    const descripcion = document.getElementById('descripcion').value.trim();
    const materiales = getCheckedMaterials();

    const codigoRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]+$/;
    const precioRegex = /^(?!0+(?:\.0{1,2})?$)\d+(?:\.\d{1,2})?$/;

    if (codigo === '') {
        alert('El código del producto no puede estar en blanco.');
        return false;
    }

    if (!codigoRegex.test(codigo)) {
        alert('El código del producto debe contener letras y números');
        return false;
    }

    if (codigo.length < 5 || codigo.length > 15) {
        alert('El código del producto debe tener entre 5 y 15 caracteres.');
        return false;
    }

    if (nombre === '') {
        alert('El nombre del producto no puede estar en blanco.');
        return false;
    }

    if (nombre.length < 2 || nombre.length > 50) {
        alert('El nombre del producto debe tener entre 2 y 50 caracteres.');
        return false;
    }

    if (!bodegaId) {
        alert('Debe seleccionar una bodega.');
        return false;
    }

    if (!sucursalId) {
        alert('Debe seleccionar una sucursal para la bodega seleccionada.');
        return false;
    }

    if (!monedaId) {
        alert('Debe seleccionar una moneda para el producto.');
        return false;
    }

    if (precio === '') {
        alert('El precio del producto no puede estar en blanco.');
        return false;
    }

    if (!precioRegex.test(precio)) {
        alert('El precio del producto debe ser un número positivo con hasta dos decimales.');
        return false;
    }

    if (materiales.length < 2) {
        alert('Debe seleccionar al menos dos materiales para el producto.');
        return false;
    }

    if (descripcion === '') {
        alert('La descripción del producto no puede estar en blanco.');
        return false;
    }

    if (descripcion.length < 10 || descripcion.length > 1000) {
        alert('La descripción del producto debe tener entre 10 y 1000 caracteres.');
        return false;
    }

    return true;
}

bodegaSelect.addEventListener('change', function () {
    loadSucursalesByBodega(this.value);
});

productForm.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (!validateForm()) return;

    saveButton.disabled = true;
    saveButton.textContent = 'Guardando...';

    try {
        const formData = new FormData(productForm);
        const response = await ajaxRequest('POST', `${API_BASE}/guardar_producto.php`, formData);
        alert(response.message || 'Producto guardado correctamente.');
        productForm.reset();
        clearSelect(sucursalSelect);
    } catch (error) {
        alert(error.message || 'No fue posible guardar el producto.');
    } finally {
        saveButton.disabled = false;
        saveButton.textContent = 'Guardar Producto';
    }
});

document.addEventListener('DOMContentLoaded', loadInitialData);
