# Exportador CSV a Base de Datos

**Exportador CSV a Base de Datos** es una aplicación web que permite importar archivos `.csv` a bases de datos MySQL de manera eficiente y automatizada. Ofrece una interfaz intuitiva para seleccionar bases de datos y tablas, con capacidades inteligentes de mapeo de datos y operaciones de inserción/actualización.

---

## Descripción

Esta herramienta facilita la carga de archivos CSV a bases de datos MySQL mediante una interfaz web moderna. Entre sus características destacan:

* **Descubrimiento Dinámico de Bases de Datos**: Detecta y lista automáticamente las bases de datos disponibles.
* **Introspección de Esquemas de Tablas**: Muestra las tablas disponibles basándose en la base de datos seleccionada.
* **Importación Inteligente de Datos**: Soporta operaciones de inserción y actualización basadas en la detección de claves primarias.
* **Carga de Múltiples Archivos**: Permite procesar varios archivos CSV simultáneamente.
* **Mapeo de Columnas**: Asocia automáticamente los encabezados del CSV con las columnas de la base de datos.
* **Interfaz de Arrastrar y Soltar**: Facilita la carga de archivos mediante una interfaz moderna y amigable.

---

## Características Principales

* **Detección Automática de Claves Primarias**: Utiliza consultas a `INFORMATION_SCHEMA` para identificar claves primarias y realizar operaciones de upsert.
* **Validación de Esquemas**: Compara los encabezados del CSV con las columnas reales de la base de datos para asegurar la integridad de los datos.
* **Análisis Flexible de CSV**: Soporta formatos delimitados por comas y punto y coma, manejando adecuadamente las comillas escapadas.
* **Procesamiento por Lotes**: Maneja múltiples archivos mediante el uso de arreglos en la entrada de archivos.
* **Protección contra Inyecciones SQL**: Emplea `mysqli::real_escape_string()` para sanitizar todos los datos proporcionados por el usuario.

---

## Arquitectura del Sistema

El sistema sigue una arquitectura web de tres capas, separando claramente la presentación, la lógica de aplicación y el almacenamiento de datos.

---

## Estructura del Proyecto

```
Exportador_csv_a_base_de_datos/
├── app_csv_a_bd/
│   ├── index.html                # Interfaz web principal
│   ├── script.js                 # Lógica del frontend (JS)
│   └── styles.css                # Estilos de la interfaz
│   ├── configbd.php              # Configuración de conexión a la base de datos
│   ├── importar.php              # Script para procesar e importar los archivos CSV
│   ├── obtener_bases_datos.php   # Devuelve la lista de bases de datos disponibles
│   ├── obtener_tablas.php        # Devuelve las tablas de la base seleccionada
├── LICENSE                        # Licencia del proyecto
└── README.md                      # Documentación principal del proyecto

```

---

### Componentes Principales

* **Frontend**:
  * `index.html` – Proporciona la interfaz de usuario para la carga de archivos CSV y la selección dinámica de bases de datos y tablas.
  * `script.js` – Contiene la lógica del frontend, gestionando eventos de usuario, peticiones AJAX para obtener bases de datos y tablas, y actualización dinámica de la interfaz.
  * `styles.css` – Define los estilos visuales y diseño para una experiencia de usuario amigable y moderna.

* **Backend**:
  * `configbd.php` – Archivo de configuración que contiene los parámetros y credenciales para la conexión a la base de datos MySQL.
  * `importar.php` – Procesa los archivos CSV recibidos, realiza la validación, mapeo y carga de datos en la base de datos.
  * `obtener_bases_datos.php` – Devuelve mediante consulta a MySQL la lista actualizada de bases de datos disponibles en el servidor.
  * `obtener_tablas.php` – Recibe la base de datos seleccionada y devuelve la lista de tablas que contiene para su selección.

* **Base de Datos**:
  * MySQL – Sistema gestor de bases de datos que almacena los datos importados y proporciona la estructura y esquema sobre la cual se realizan las operaciones de inserción y actualización.

---

## Requisitos

* **Servidor Web**: Apache, Nginx o similar con soporte para PHP.
* **PHP**: Versión 7.0 o superior.
* **Base de Datos**: MySQL o MariaDB.

---

## Instalación

1. **Clonar el Repositorio**:

   ```bash
   git clone https://github.com/MrGL1TCH/Exportador_csv_a_base_de_datos.git
   ```

2. **Configurar el Entorno**:

   * Asegúrate de que tu servidor web esté configurado correctamente y tenga acceso a PHP y MySQL.
   * Coloca la carpeta `app_csv_a_bd` en el directorio raíz de tu servidor web.

3. **Configurar la Conexión a la Base de Datos**:

   * Abre el archivo `importar.php` y ajusta las credenciales de conexión a tu base de datos:

     ```php
     $conexion = new mysqli("localhost", "usuario", "contraseña", "basedatos");
     ```

---

## Uso

1. **Acceder a la Interfaz Web**:

   * Navega a `http://localhost/app_csv_a_bd/index.html` en tu navegador.

2. **Cargar Archivos CSV**:

   * Utiliza la interfaz para arrastrar y soltar o seleccionar los archivos CSV que deseas importar.

3. **Seleccionar Base de Datos y Tabla**:

   * La aplicación detectará automáticamente las bases de datos y tablas disponibles. Selecciona la tabla de destino para cada archivo.

4. **Iniciar la Importación**:

   * Haz clic en el botón de importación para comenzar el proceso. La aplicación mostrará mensajes de éxito o error según corresponda.

---

## Notas Adicionales

* **Formato de los Archivos CSV**:

  * Asegúrate de que los archivos CSV tengan encabezados que coincidan con los nombres de las columnas en la base de datos.
  * Los archivos deben estar codificados en UTF-8 para evitar problemas de caracteres.

* **Seguridad**:

  * Aunque se implementan medidas básicas de seguridad, se recomienda validar y sanitizar los datos adicionales según las necesidades específicas de tu entorno.

---

## Licencia

Este proyecto está licenciado bajo la Licencia MIT. Consulta el archivo [LICENSE](./LICENSE) para más detalles.

---

## Contribuciones

¡Las contribuciones son bienvenidas! Si deseas mejorar esta herramienta o agregar nuevas funcionalidades, no dudes en enviar un pull request o abrir un issue para discutir tus ideas.

---

## Contacto

Para preguntas o sugerencias, puedes contactarme a través de:
GitHub: [MrGL1TCH](https://github.com/MrGL1TCH)
