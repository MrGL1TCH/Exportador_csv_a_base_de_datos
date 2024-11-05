// Obtener los elementos del DOM (Document Object Model)
const databaseSelect = document.getElementById('database');
const tableSelect = document.getElementById('table');
const fileInput = document.getElementById('archivos');
const mainForm = document.getElementById('main-form');
const dropZone = document.getElementById('drop-zone');
const dropZoneText = document.getElementById('drop-zone-text');
const fileList = document.createElement('ul');
fileList.className = 'file-list';
dropZone.appendChild(fileList);

// Función para obtener las bases de datos
async function fetchDatabases() {
    try {
        // Realizar una solicitud para obtener las bases de datos
        const response = await fetch('obtener_bases_datos.php');
        const databases = await response.json();

        // Limpiar las opciones del desplegable de bases de datos y añadir una opción predeterminada
        databaseSelect.innerHTML = `<option value="">Selecciona una base de datos</option>`;

        // Manejar errores
        if (databases.error) {
            alert(`Error: ${databases.error}`);
        } else {
            // Añadir las bases de datos al desplegable
            databases.forEach(db => {
                const option = document.createElement('option');
                option.value = db;
                option.textContent = db;
                databaseSelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error("Error al obtener las bases de datos:", error);
    }
}

// Evento para obtener las tablas de la base de datos seleccionada
databaseSelect.addEventListener('change', async () => {
    const database = databaseSelect.value;
    if (database) {
        // Crear un FormData para enviar la base de datos seleccionada
        const formData = new FormData();
        formData.append('database', database);

        // Realizar una solicitud para obtener las tablas
        const response = await fetch('obtener_tablas.php', {
            method: 'POST',
            body: formData
        });
        const tables = await response.json();

        // Limpiar las opciones del desplegable de tablas y añadir una opción predeterminada
        tableSelect.innerHTML = `<option value="">Selecciona una tabla</option>`;

        // Añadir las tablas al desplegable
        tables.forEach(table => {
            const option = document.createElement('option');
            option.value = table;
            option.textContent = table;
            tableSelect.appendChild(option);
        });
        tableSelect.disabled = false;
    } else {
        // Deshabilitar el desplegable de tablas si no se selecciona una base de datos
        tableSelect.innerHTML = `<option value="">Selecciona una tabla</option>`;
        tableSelect.disabled = true;
    }
});

// Llamar a la función para obtener las bases de datos cuando la página se haya cargado
window.addEventListener('DOMContentLoaded', fetchDatabases);

// Manejar el evento de arrastrar y soltar para la zona de archivos
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('drag-over');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('drag-over');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    fileInput.files = e.dataTransfer.files;
    updateFileList();
});

// Permitir clic en la zona de arrastre para seleccionar archivos
dropZone.addEventListener('click', () => {
    fileInput.click();
});

// Manejar el evento de cambio en el input de archivos
fileInput.addEventListener('change', updateFileList);

function updateFileList() {
    fileList.innerHTML = '';
    if (fileInput.files.length > 0) {
        dropZoneText.style.display = 'none';
    } else {
        dropZoneText.style.display = 'block';
    }
    for (let i = 0; i < fileInput.files.length; i++) {
        const li = document.createElement('li');
        li.textContent = fileInput.files[i].name;
        fileList.appendChild(li);
    }
    // Mantener el tamaño de la zona de arrastre
    dropZone.style.height = '200px';
}


// Manejar el envío del formulario
mainForm.addEventListener('submit', async function(event) {
    event.preventDefault(); // Prevenir el envío del formulario por defecto

    const formData = new FormData(mainForm);

    try {
        const response = await fetch('importar.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.text();

        if (response.ok) {
            alert("Datos cargados exitosamente");
        } else {
            alert("Falló el proceso: " + result);
        }
    } catch (error) {
        alert("Error durante la carga: " + error.message);
    }
});
