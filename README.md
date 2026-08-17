# API Consultorías

API REST para gestión de consultorías, desarrollada con Symfony 6.4.

## Requisitos

- PHP 8.2+
- Composer
- MySQL/PostgreSQL
- Docker (opcional)

## Instalación

```bash
# Clonar el repositorio
git clone https://bitbucket.org/practicas_480/api_consultorias.git
cd api_consultorias

# Instalar dependencias
composer install

# Configurar variables de entorno
cp .env .env.local
# Editar .env.local con tus credenciales de base de datos

# Crear la base de datos
php bin/console doctrine:database:create

# Ejecutar migraciones
php bin/console doctrine:migrations:migrate --no-interaction

# Cargar fixtures (datos de prueba)
php bin/console doctrine:fixtures:load --no-interaction

# Generar claves JWT
php bin/console lexik:jwt:generate-keypair
```

## Ejecución

```bash
# Servidor de desarrollo
symfony serve

# O con PHP built-in server
php -S 127.0.0.1:8000 -t public/
```

## Docker

```bash
# Levantar servicios
docker-compose -f docker-compose-local.yaml up -d
```

## Testing

```bash
# Ejecutar tests unitarios
php bin/phpunit

# Ejecutar tests funcionales
vendor/bin/codecept run functional

# Ejecutar tests API
vendor/bin/codecept run api
```

## Estructura del Proyecto

```
src/
├── Endpoints/API/        # Controladores API (Clean Architecture)
├── Entity/               # Entidades Doctrine
├── Repository/           # Repositorios
├── Form/                 # Formularios
├── SecurityUser/         # Autenticación y autorización
└── Services/             # Servicios de negocio
```

## Arquitectura

El proyecto sigue **Screaming Architecture** (Clean Architecture):

- **Endpoints/API/**: Controladores HTTP organizados por dominio
- **Application/**: Servicios de aplicación (casos de uso)
- **Domain/**: Lógica de negocio y entidades
- **Infrastructure/**: Persistencia, eventos, listeners

## API

La documentación de la API está disponible en:

```
GET /api/doc
```

## Contribuir

1. Crear una rama desde `develop`
2. Hacer commits con convención semántica
3. Abrir Pull Request hacia `develop`

## Licencia

Propietaria - Practicas 480
