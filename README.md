# TaPasandoRD
**Sistema de Reporte de Incidencias para República Dominicana**

TaPasandoRD es una plataforma colaborativa diseñada para reportar, validar y visualizar incidencias en tiempo real, fortaleciendo la transparencia y seguridad ciudadana en República Dominicana.

## Características Principales

- **Sistema de Reportes**: Los usuarios pueden reportar incidencias con ubicación geográfica, descripciones detalladas y multimedia
- **Validación por Moderadores**: Sistema de validación de reportes por usuarios autorizados
- **Visualización en Mapa**: Mapa interactivo que muestra incidencias validadas en tiempo real
- **Autenticación Múltiple**: Soporte para login local, Google OAuth y Microsoft OAuth
- **Panel de Administración**: Gestión de provincias, municipios, barrios y categorías de incidencias
- **Sistema de Roles**: Reportero, Validador y Administrador con permisos específicos

## Tecnologías Utilizadas

### Backend
- **PHP 8.3+** - Lenguaje de programación principal
- **Arkham Framework v1.5.2** - Framework PHP personalizado con arquitectura MVC
- **MySQL** - Base de datos relacional
- **Composer** - Gestor de dependencias

### Frontend
- **Twig** - Motor de plantillas
- **Bootstrap 5.3** - Framework CSS
- **Tailwind CSS** - Utilidades CSS
- **Leaflet.js** - Mapas interactivos
- **Font Awesome** - Iconografía

### OAuth Providers
- **Google OAuth 2.0** - Autenticación con Google
- **Microsoft OAuth 2.0** - Autenticación con Office 365

### Testing
- **PHPUnit 12.x** - Framework de testing
- **GitHub Actions** - CI/CD automatizado

## Requisitos del Sistema

- **PHP** >= 8.1
- **MySQL** >= 5.7 o MariaDB >= 10.3
- **Composer** >= 2.0
- **Extensiones PHP**:
  - pdo
  - pdo_mysql
  - mbstring
  - xml
  - json
  - curl (para OAuth)

## Instalación

### 1. Clonar el Repositorio
```bash
git clone https://github.com/tu-usuario/TaPasandoRD.git
cd TaPasandoRD
```

### 2. Instalar Dependencias
```bash
composer install
```

### 3. Configuración de Base de Datos
Edita el archivo `config/database.json`:
```json
{
  "driver": "mysql",
  "host": "localhost",
  "port": 3306,
  "database": "tapasandord",
  "username": "tu_usuario",
  "password": "tu_contraseña",
  "charset": "utf8mb4",
  "collation": "utf8mb4_unicode_ci"
}
```

### 4. Configuración de Variables de Entorno
Crea un archivo `.env` en la raíz del proyecto:
```env
# OAuth Google
GOOGLE_CLIENT_ID=tu_client_id_google
GOOGLE_CLIENT_SECRET=tu_client_secret_google
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# OAuth Microsoft
MICROSOFT_CLIENT_ID=tu_client_id_microsoft
MICROSOFT_CLIENT_SECRET=tu_client_secret_microsoft
MICROSOFT_REDIRECT_URI=http://localhost:8000/auth/microsoft/callback
```

### 5. Inicializar Base de Datos
```bash
php scripts/setup_db.php
```

### 6. Servidor de Desarrollo
```bash
php -S localhost:8000 -t public
```
Visita http://localhost:8000 en tu navegador.

## Estructura del Proyecto

```
TaPasandoRD/
├── app/
│   ├── Controllers/
│   │   ├── Api/           # Controladores API
│   │   └── Web/           # Controladores Web
│   ├── Models/            # Modelos de datos
│   ├── Repositories/      # Capa de acceso a datos
│   ├── Factories/         # Factories (OAuth)
│   ├── Interfaces/        # Contratos de interfaces
│   ├── Providers/         # Proveedores OAuth
│   └── Strategies/        # Patrones Strategy
├── config/
│   └── database.json     # Configuración de BD
├── public/
│   ├── assets/           # CSS, JS, imágenes
│   └── index.php         # Punto de entrada
├── routes/
│   ├── api/              # Rutas API
│   └── web/              # Rutas Web
├── scripts/
│   ├── incidents_db.sql  # Esquema de BD
│   ├── seed.sql          # Datos de prueba
│   └── setup_db.php      # Script de instalación
├── tests/                # Tests unitarios
├── views/                # Plantillas Twig
└── vendor/               # Dependencias
```

## API Endpoints

### Autenticación
- `POST /api/login` - Iniciar sesión
- `POST /api/register` - Registrar usuario
- `POST /api/logout` - Cerrar sesión
- `GET /auth/{provider}` - OAuth redirect
- `GET /auth/{provider}/callback` - OAuth callback

### Incidencias
- `GET /api/incident` - Listar incidencias
- `GET /api/incident/{id}` - Obtener incidencia específica
- `POST /api/incident` - Crear nueva incidencia
- `GET /api/valid-incident` - Incidencias validadas

### Validación
- `GET /api/validator/get-pending` - Incidencias pendientes
- `POST /api/validator/approve` - Aprobar incidencia
- `POST /api/validator/reject` - Rechazar incidencia
- `GET /api/validator/comments/{id}` - Comentarios de validación

## Roles y Permisos

### Reportero (Role ID: 1)
- Reportar nuevas incidencias
- Ver incidencias validadas
- Ver mapa de incidencias

### Validador (Role ID: 2)
- Todas las funciones de Reportero
- Validar/rechazar incidencias pendientes
- Agregar comentarios de validación

### Administrador (Role ID: 3)
- Todas las funciones anteriores
- Gestionar provincias, municipios y barrios
- Gestionar categorías de incidencias
- Acceso al panel de administración

## Testing

### Ejecutar Tests
```bash
# Todos los tests
composer test

# Tests específicos
vendor/bin/phpunit tests/Repositories/UserRepositoryTest.php
```

### GitHub Actions
El proyecto incluye CI/CD automatizado que:
- Ejecuta tests en PHP 8.3
- Genera reportes JUnit
- Publica resultados en PRs
- Valida dependencias de Composer

## Configuración OAuth

### Google OAuth
1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la Google+ API
4. Crea credenciales OAuth 2.0
5. Agrega tu dominio a las URIs autorizadas

### Microsoft OAuth
1. Ve a [Azure Portal](https://portal.azure.com/)
2. Registra una nueva aplicación
3. Configura permisos de Microsoft Graph
4. Obtén Client ID y Client Secret

## Despliegue

### Opciones de Hosting Gratuito

#### Railway (Recomendado)
- Conecta tu repositorio GitHub
- Agrega servicio MySQL
- Configura variables de entorno
- Deploy automático

#### Render
- Soporte PHP nativo
- Base de datos PostgreSQL incluida
- SSL automático

#### Heroku
- Dyno gratuito disponible
- Add-ons para MySQL
- Fácil escalabilidad

### Variables de Entorno para Producción
```env
# Base de datos
DB_HOST=tu_host_db
DB_DATABASE=tu_base_datos
DB_USERNAME=tu_usuario_db
DB_PASSWORD=tu_contraseña_db

# OAuth (URLs de producción)
GOOGLE_REDIRECT_URI=https://tudominio.com/auth/google/callback
MICROSOFT_REDIRECT_URI=https://tudominio.com/auth/microsoft/callback
```

## Contribución

### Configuración de Desarrollo
1. Fork el repositorio
2. Crea una rama para tu feature: `git checkout -b feature/nueva-funcionalidad`
3. Realiza tus cambios y agrega tests
4. Ejecuta la suite de tests: `composer test`
5. Commit y push: `git commit -m "Agregar nueva funcionalidad"`
6. Crea un Pull Request

### Estándares de Código
- Seguir PSR-12 para estilo de código
- Escribir tests para nuevas funcionalidades
- Documentar cambios en el API
- Usar nombres descriptivos para variables y métodos

### Reporte de Bugs
Al reportar un bug, incluye:
- Versión de PHP
- Pasos para reproducir
- Comportamiento esperado vs actual
- Screenshots si es apropiado

## Licencia

Este proyecto está licenciado bajo la Licencia MIT. Ver el archivo LICENSE para más detalles.

## Autores

**Josué R. Hernández Montero** -Backend Dev
- Email: josuehernandez2314@gmail.com
- GitHub: [@JosueIsOffline](https://github.com/JosueIsOffline)

**Ronny De León** -Frontend Dev
- Email: dleonabreuronny@gmail.com
- GitHub: [@Ronny-Abreu](https://github.com/Ronny-Abreu)

**Jheinel Brown** -Backend Dev
- Email: jheinelbrown@gmail.com
- Github: [@Black-Brown](https://github.com/Black-Brown)

**Keison Jafel** -Backend Dev
- Email: key112806@gmail.com
- Github: [@Keison28](https://github.com/Keison28)

**Edudardo Guzman** -Backend Dev
- Email: eduardo04guz@icloud.com
- Github: [@Keison28](https://github.com/Eduardo04alv)


## Agradecimientos

- Framework Arkham desarrollado internamente
- Comunidad de código abierto por las librerías utilizadas
- Contribuidores y testers del proyecto

## Soporte

Para soporte técnico o preguntas:
1. Revisa la documentación existente
2. Busca en Issues existentes
3. Crea un nuevo Issue con detalles específicos
4. Contacta al maintainer principal

---

**Nota**: Este proyecto está en desarrollo activo. Las características y API pueden cambiar en versiones futuras.
