<p align="center">
  <img src="public/readme/logo-color.png" alt="Logo OleoControl" width="300"/>
</p>

# Laravel API para OleoControl

Este es el backend del proyecto **OleoControl**, una aplicación web desarrollada con Laravel 11 para la gestión integral de almazaras.

El sistema permite gestionar la recepción de aceituna, análisis de laboratorio, liquidaciones de aceite, historial de entregas por agricultor y generación de informes y gráficos estadísticos.  
También ofrece funcionalidades de autenticación, control de acceso por roles (socios, empleados y administradores) y notificaciones por correo electrónico.

Toda la lógica de negocio se expone a través de una API RESTful que es consumida por el frontend (desarrollado en React).





### Características principales de OleoControl:

- **Gestión de Recepción de Aceituna**: Registro de entregas de aceituna por parte de los agricultores, con detalles como el peso y la calidad.
- **Análisis de Laboratorio**: Los técnicos pueden realizar análisis de laboratorio y registrar los resultados de cada entrega de aceituna.
- **Historial de Entradas**: Los agricultores pueden consultar el historial completo de todas las entregas realizadas a la almazara.
- **Generación de Informes y Gráficos**: Los usuarios pueden generar informes detallados sobre la producción de aceite y visualizar gráficos estadísticos, como el rendimiento de la aceituna.
- **Sistema de Liquidación**: Los agricultores pueden solicitar la liquidación de su aceite para su venta, y recibir la transferencia bancaria correspondiente.
- **Notificaciones por Correo Electrónico**: Los usuarios reciben notificaciones por correo electrónico sobre el estado de sus entregas, liquidaciones, y otros eventos relevantes.
- **Autenticación y Control de Roles**: El sistema permite roles de usuario diferenciados (agricultores, operarios, técnicos) con acceso limitado según su función.

### Backend (Laravel):
- **Laravel 11**: Framework PHP utilizado para desarrollar la API RESTful que gestiona toda la lógica de negocio del sistema.
- **Laravel Sanctum**: Para la autenticación de usuarios mediante tokens en la API.
- **Eloquent ORM**: Sistema de mapeo objeto-relacional para interactuar con la base de datos.
- **MySQL**: Base de datos relacional utilizada para almacenar los datos de las entregas, análisis, liquidaciones, etc.
- **Migrations y Seeders**: Para gestionar la estructura de la base de datos y poblarla con datos de ejemplo durante el desarrollo.

### Frontend (React):
- **React**: Librería JavaScript utilizada para construir la interfaz de usuario dinámica y reactiva.
- **Axios**: Cliente HTTP para realizar peticiones a la API RESTful del backend.
- **TailwindCSS**: Framework CSS para crear un diseño moderno y responsivo de manera rápida y eficiente.
- **Vite**: Herramienta de construcción que mejora el desarrollo y la experiencia en React, optimizando el tiempo de recarga.

### Herramientas adicionales:
- **Mailtrap**: Para la gestión de correos electrónicos durante el desarrollo, evitando enviar emails reales.
- **doompdf**: Para la generación de informes en PDF.

### Requisitos del sistema:

- **PHP >= 8.2**: Necesario para ejecutar Laravel 11 y todas sus dependencias.
- **Composer**: Para gestionar las dependencias de PHP.
- **MySQL o MariaDB**: Base de datos relacional para almacenar la información del sistema.
- **Node.js >= 16.0**: Necesario para la instalación y gestión de dependencias de frontend (React).
- **NPM o Yarn**: Herramientas de gestión de paquetes para el frontend.
- **Extensiones de PHP**:
  - `ext-json`
  - `ext-mbstring`
  - `ext-ctype`
  - `ext-fileinfo`
  - `ext-pdo_mysql`
### Instalación del backend (Laravel):

1. **Clonar el repositorio**:

   ```bash
   git clone https://github.com/tu-usuario/oleocontrol-backend.git

   ```

2. Entrar en el directorio del proyecto:

    ```bash
    cd oleocontrol-backend
    ```


3. Instalar las dependencias de PHP con Composer:

    ```bash
    composer install
    ```

4. Configurar el archivo .env:
Copiar el archivo .env.example a .env y configurar las variables de entorno (como base de datos, correo, etc.):

    cp .env.example .env

Editar .env para configurar la conexión a la base de datos y otras variables necesarias.

5. Generar la clave de la aplicación:

    ```bash
    php artisan key:generate
    ```

6. Realizar las migraciones de la base de datos:

    ```bash
    php artisan migrate
    ```

7. (Opcional) Cargar datos de ejemplo:

    ```bash
    php artisan db:seed
    ```

8. Iniciar el servidor de desarrollo:

    ```bash
    php artisan serve
    ```

Esto levantará el servidor en http://localhost:8000.
## Uso/Ejemplos

El backend de **OleoControl** expone una API RESTful que puede ser consumida por cualquier cliente HTTP (por ejemplo, el frontend de React). A continuación se muestran algunos ejemplos de cómo utilizar los endpoints más comunes:

#### 1. Autenticación de usuarios (Login)

**Endpoint:** `POST /api/login`

**Ejemplo de cuerpo de solicitud:**
```json
{
  "username": "AdminPruebas",
  "password": "contraseña_segura"
}
```

**Respuesta (si es exitoso):**
```json
{
  "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

#### 2. Obtener el historial de entregas de un socio

**Endpoint:** `GET /api/member/1/entries`

**Ejemplo de respouesta:**
```json
[
  {
    "id": 1,
    "entry_date": "2025-03-05",
    "olive_quantity": 5000,
    "oil_quantity": "",
    "analysis_status": "Pendiente",
    "member": [
        "id": 25,
        "name": "Juan Pérez",
        "member_number": 1025,
        "member_email": "juan_perez@email.com",
    }
  },
  {
    "id": 2,
    "entry_date": "2025-03-18",
    "olive_quantity": 3650,
    "oil_quantity": "",
    "analysis_status": "Pendiente",
    "member": [
        "id": 25,
        "name": "Juan Pérez",
        "member_number": 1025,
        "member_email": "juan_perez@email.com",
    }
  },
]
```






## Estructura del proyecto

La estructura de carpetas de este proyecto sigue la convención estándar de Laravel. A continuación se describen las principales carpetas y archivos:

    oleocontrol-backend/
    ├── app/                        # Contiene el código fuente de la aplicación
    │   ├── Http/                   # Controladores, middleware, peticiones HTTP
    │   ├── Models/                 # Modelos de Eloquent (entidades)
    │   └── Providers/              # Proveedores de servicio (módulos)
    ├── bootstrap/                  # Archivos de inicio de Laravel
    │   └── app.php                 # Carga de configuración inicial
    ├── config/                     # Archivos de configuración (base de datos, correo, etc.)
    ├── database/                   # Migraciones y semillas de base de datos
    │   ├── migrations/             # Migraciones de tablas de la base de datos
    │   └── seeders/                # Archivos para poblar la base de datos
    ├── public/                     # Archivos públicos accesibles por el navegador (CSS, JS, imágenes, etc.)
    │   └── index.php               # Punto de entrada al sistema
    ├── resources/                  # Archivos de vistas y recursos
    │   └── lang/                   # Archivos de traducción
    ├── routes/                     # Definición de rutas de la API
    │   └── api.php                 # Rutas de la API (definición de endpoints)
    ├── storage/                    # Archivos generados (logs, caché, etc.)
    ├── tests/                      # Pruebas automatizadas
    │   └── Feature/                # Pruebas de funcionalidades
    ├── .env                        # Archivo de configuración de entorno (base de datos, correo, etc.)
    ├── artisan                     # Herramienta de línea de comandos de Laravel
    ├── composer.json               # Dependencias de Composer
    └── phpunit.xml                 # Configuración de pruebas unitarias




### Descripción de carpetas importantes:

- **app/Models**: Contiene los modelos de Eloquent, que representan las entidades del sistema como `Member`, `Entry`, `Analysis`, `Settlement`, etc.
- **app/Http/Controllers**: Los controladores gestionan la lógica detrás de cada endpoint de la API. Aquí se encuentran los controladores como `EntryController`, `AnalysisController`, etc.
- **database/migrations**: Las migraciones definen la estructura de las tablas de la base de datos, por ejemplo, `create_entries_table.php` o `create_users_table.php`.
- **routes/api.php**: Define todas las rutas de la API, donde se especifican los métodos HTTP y las funciones de los controladores que se ejecutarán.
- **storage/logs**: Aquí se guardan los logs del sistema, lo que permite depurar errores y registrar eventos importantes.


## API Reference

#### Iniciar sesión (Login)

```http
  POST /api/login
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `username` | `string` | **Required**. Nombre de usuario |
| `password` | `string` | **Required**. Contraseña |

Respuesta:

200 OK:

    {
        {
        "access_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
        "token_type": "Bearer",
        "user": {
            "id": 1,
            "name": "Juan Pérez",
            "email": "juan.perez@example.com",
            "roles": ["admin", "member"]
        }
    }


401 Unauthorized:

    {
    "status": "error",
    "message": "Credenciales incorrectas."
    }

403 Forbidden:

    {
    "status": "error",
    "message": "Esta cuenta está desactivada."
    }

#### Obtener las entregas de un socio

```http
  GET /api/enteis/{memberId}/entries
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `memberId`      | `int` | **Required**. IId de socio |

Respuesta:

200 OK:

    {
    "status": "success",
    "data": [
        {
        "id": 1,
        "entry_date": "2025-04-10",
        "olive_quantity": 500,
        "oil_quantity": 100,
        "analysis_status": "completed",
        "member": {
            "id": 1,
            "name": "Juan Pérez",
            "member_number": "001",
            "member_email": "juan.perez@example.com"
            }
        }
    ]
}


#### Crear una liquidación de aceite

```http
  POST /api/settlements
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `string` | **Required**. Id of item to fetch |

201 Created:

    {
        "status": "success",
        "data": {
            "id": 1,
            "settlement_date": "2025-04-15",
            "amount": 100,
            "price": 2.5,
            "settlement_status": "completed",
            "oil": {
                "oil_id": 1,
                "name": "Aceite Extra Virgen",
                "price": 2.5
            },
            "member": {
                "member_id": 1,
                "name": "Juan Pérez",
                "member_number": "001",
                "member_email": "juan.perez@example.com"
            }
        }
    }

400 Bad Request:

    {
    "status": "error",
    "message": "No hay suficiente aceite disponible para esta liquidación. Disponible: 50 litros."
    }

500 Internal Server Error:

    {
    "status": "error",
    "message": "Error al crear la liquidación: {detalle del error}"
    }


### Función de ejemplo para crear una liquidación:

    public function storeAvailable(StoreSettlementRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            $totalInventory = OilInventory::where('member_id', $validated['member_id'])
                ->where('oil_id', $validated['oil_id'])
                ->sum('quantity');

            $totalSettled = OilSettlement::where('member_id', $validated['member_id'])
                ->where('oil_id', $validated['oil_id'])
                ->sum('amount');

            $availableOil = $totalInventory - $totalSettled;

            if ($availableOil < $validated['amount']) {
                DB::rollBack();
                return response()->json([
                    'status' => 'error',
                    'message' => 'No hay suficiente aceite disponible para esta liquidación. Disponible: ' . $availableOil . ' litros.'
                ], 400);
            }

            $settlement = Settlement::create([
                'settlement_date' => $validated['settlement_date'],
                'oil_id' => $validated['oil_id'],
                'amount' => $validated['amount'],
                'price' => $validated['price'],
                'settlement_status' => $validated['settlement_status'],
                'member_id' => $validated['member_id'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'data' => new SettlementResource($settlement)
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la liquidación: ' . $e->getMessage()
            ], 500);
        }
    }







## Tests

#### Descripción

El proyecto utiliza PHPUnit para las pruebas unitarias y de integración, lo que permite garantizar el correcto funcionamiento de la API y sus endpoints. A continuación, se describe cómo ejecutar las pruebas y qué puedes esperar de ellas.

#### Requisitos

- Tener PHP y Composer instalados en el sistema.

- Haber configurado correctamente la base de datos de pruebas en el archivo .env.

#### Ejecutar pruebas

1. Asegúrate de que las dependencias están instaladas:

```bash
composer install
```

2. Ejecuta las pruebas unitarias de PHPUnit:
```bash
php artisan test
```



## Documentación

La documentación completa de la API está disponible a través de Swagger. Puedes acceder a ella directamente desde el siguiente enlace:

[Documentación](https://oleocontrol-back-production-rw.up.railway.app/api/documentation)

Documentación Swagger de OleoControl
¿Cómo acceder a la documentación?

    Inicia el servidor de tu aplicación Laravel.

    Accede a la URL donde se sirve Swagger, por lo general se encuentra en:
    http://localhost:8000/api/documentation
    (O ajusta la URL de acuerdo a la configuración de tu servidor).

Funcionalidades documentadas

La documentación incluye detalles sobre los siguientes puntos:

    EndPoints: Lista completa de los endpoints disponibles.

    Parámetros: Descripción de los parámetros requeridos por cada endpoint.

    Respuestas: Estructura de las respuestas devueltas por la API.

    Códigos de estado: Códigos de respuesta HTTP para los diferentes casos.


## Licencia

Este proyecto está bajo licencia privada. No se permite la redistribución, modificación o uso comercial sin el consentimiento explícito de los autores.

Para obtener más información sobre el uso, modificación o distribución del código, por favor contacta con el propietario del proyecto.


## Autores

Este proyecto fue desarrollado por:

- [@BiLLY-1983](https://www.github.com/BiLLY-1983)

Pedro Jesús Berzosa Ogallar

Desarrollador principal y responsable del desarrollo de la aplicación OleoControl.
