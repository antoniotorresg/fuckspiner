# FuckSpiner
Plugin para WordPress para leer un feed tanto en español como inglés, spinearlo de forma automatica con la herramienta de spinea.me y crear el post a la categoría asignada

# Instalación
Descargar la carpeta fuckspiner, subirla a WordPress como cualquier otro plugin e instalarlo.
En el menú de la izquierda seleccionar el enlace FuckSpiner:
1. Insertar el email con el que se dió de alta en Spinea.me
2. Insertar la Api Key de Spinea.me
3. Insertar la URL del feed que quiere spinear
4. Seleccionar si esta en inglés (si selecciona no se entiende que esta en español)
5. Insertar la API Yandex Translate en el caso de que el feed este en inglés (https://tech.yandex.com/translate/)
6. Insertar la Id o Ids de las categorías separadas por comas

# Automatismo
Una vez instalado y configurado el plugin configurar el cron en el servidor para que lo ejecute una vez al dia con la siguiente url:
https://sudominio.com/wp-content/plugins/fuckspiner/fuckspiner_cron.php
