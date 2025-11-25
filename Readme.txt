	Dashboard de Reportes con Gráficos Dinámicos
Este proyecto es una herramienta de inteligencia de negocios (BI) desarrollada en PHP, MySQL y Docker.
Permite a los administradores importar datos de ventas desde archivos Excel y a los analistas visualizar
estos datos a través de un dashboard con gráficos interactivos y filtros dinámicos.

	Características
#Autenticación por Roles: Diferencia entre perfiles de Administrador y Analista de Datos.
#Arquitectura Multitenant: Aísla los datos por compañía, permitiendo que cada organización vea únicamente su propia información.
#Importación de Datos: Permite a los administradores cargar reportes de ventas a través de archivos Excel, con validación para evitar datos duplicados.
#Visualización Dinámica: Dashboard interactivo con filtros y múltiples tipos de gráficos (Barras, Pastel, Línea, etc.) usando Chart.js.

	Prerrequisitos
Para ejecutar este proyecto, necesitas tener instalados:
##Docker
##Docker Compose (generalmente viene incluido con Docker Desktop)

	Pasos para la Instalación
Sigue estos pasos para levantar el entorno de desarrollo localmente:

1. Clonar el Repositorio
Abre tu terminal y clona este proyecto en tu máquina.

git clone https://github.com/tu-usuario/nombre-del-repositorio.git
cd nombre-del-repositorio
2. Construir y Levantar los Contenedores
Este único comando creará las imágenes, instalará las dependencias de Composer, iniciará los contenedores y configurará la base de datos automáticamente.

docker-compose up -d --build
(Espera a que termine el proceso. La primera vez puede tardar unos minutos).

3. ¡Listo!
La aplicación ya está completamente configurada y funcionando. Puedes acceder a ella en tu navegador en la siguiente dirección:

http://localhost

Datos de Acceso a la BD (opcional, para depuración):

Host: 127.0.0.1
Puerto: 3310
Usuario: root
Contraseña: 12345678 (la definida en docker-compose.yml)
Base de Datos: reporte_db