# Prueba técnica PandoraFMS

Este proyecto es una aplicación web desarrollada como parte de una prueba técnica.

Incluye dos funcionalidades principales:

    - Decodificación de puntuaciones: Un sistema que permite interpretar puntuaciones de usuarios a partir de un formato codificado.

    - Gestión de citas: Una aplicación para realizar y registrar reservas de citas de pacientes.

La aplicación se ejecuta en un contenedor Docker con un entorno LAMP (Linux, Apache, MySQL y PHP).

A continuación se detallan los pasos necesarios para su instalación y puesta en marcha.

## Requisitos previos

Antes de comenzar, asegúrate de tener instalados los siguientes programas en tu sistema:

- **Docker** (y **Docker Compose**) - Para crear y gestionar los contenedores.
- **Git** - Para clonar el repositorio.

## Pasos para desplegar el proyecto

### 1. Clonar el repositorio

Clona el repositorio del proyecto en una carpeta dentro de `C:/`:

```bash
git clone https://github.com/merguijarrog/pandora_prueba_tecnica C:/pandora_prueba_tecnica
```

### 2. Configurar fichero .env

1. Renombrar fichero example.env a .env en la raíz del proyecto.
2. Introducir credenciales de la base de datos en el fichero '.env':
   ```
    DB_HOST=db
    DB_NAME=pandora_prueba_tecnica
    DB_USER=root
    DB_PASSWORD=yourpassword
   ``` 
  Esto es para facilitar el despliegue del ejercicio, nunca se debería exponer ningún tipo de credencial en ningún fichero.

### 3. Construir y levantar los contenedores

1. Abrir una terminal pulsando win + R y escribiendo cmd.
2. Ejecutar 'cd C:/pandora_prueba_tecnica/docker' y seguidamente 'docker-compose up --build'

El servicio debería estar disponible en 'http://localhost:8080' una vez levantado. 
Para acceder a los ejercicios la ruta es: 'http://localhost:8080/public/index.php', aparecerán dos botones para acceder a cada uno de los ejercicios.
