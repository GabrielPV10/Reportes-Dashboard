# Dashboard de Reportes con Gráficos Dinámicos

Este proyecto es una herramienta de inteligencia de negocios (BI) desarrollada en PHP, MySQL y Docker. Permite a los administradores importar datos de ventas desde archivos Excel y a los analistas visualizar estos datos a través de un dashboard con gráficos interactivos y filtros dinámicos.

## Características

-   **Autenticación por Roles:** Diferencia entre perfiles de **Administrador** y **Analista de Datos**.
-   **Arquitectura Multitenant:** Aísla los datos por compañía, permitiendo que cada organización vea únicamente su propia información.
-   **Importación de Datos:** Permite a los administradores cargar reportes de ventas a través de archivos Excel, con validación para evitar datos duplicados.
-   **Visualización Dinámica:** Dashboard interactivo con filtros y múltiples tipos de gráficos (Barras, Pastel, Línea, etc.) usando **Chart.js**.

## Prerrequisitos

Para ejecutar este proyecto, necesitas tener instalados:

-   [Docker](https://www.docker.com/products/docker-desktop/)
-   Docker Compose (generalmente viene incluido con Docker Desktop)

## Pasos para la Instalación

Sigue estos pasos para levantar el entorno de desarrollo localmente:

    1. Clonar el Repositorio
Abre tu terminal y clona este proyecto en tu máquina.
git clone [https://github.com/tu-usuario/nombre-del-repositorio.git](https://github.com/tu-usuario/nombre-del-repositorio.git)
cd nombre-del-repositorio

    2. Construir y Levantar los Contenedores
Este comando creará las imágenes de Docker personalizadas y encenderá todos los servicios (Nginx, PHP, MySQL) en segundo plano.
docker-compose up -d --build
    3. Instalar Dependencias de PHP (Composer)
Una vez que los contenedores estén corriendo, necesitamos instalar las librerías de PHP que el proyecto necesita (como la que lee archivos de Excel).
# Entrar a la línea de comandos del contenedor de PHP
docker-compose exec php bash
# Instalar las librerías definidas en composer.json y composer.lock
composer install
# Salir del contenedor
exit
    4. Configurar la Base de Datos
El contenedor de la base de datos ya está corriendo, pero su estructura interna está vacía.
Conéctate a la base de datos usando tu cliente SQL preferido (MySQL Workbench, DBeaver, etc.) con los siguientes datos:
Host: 127.0.0.1
Puerto: 3310
Usuario: root
Contraseña: 127345678 (o la que hayas definido en docker-compose.yml)
Abre el archivo sql/init.sql que se encuentra en este repositorio.
Copia y pega todo su contenido en una nueva ventana de consulta y ejecútalo. Esto creará todas las tablas y relaciones necesarias.

    5. ¡Listo!
La aplicación ya está completamente configurada y funcionando. Puedes acceder a ella en tu navegador en la siguiente dirección:

http://localhost