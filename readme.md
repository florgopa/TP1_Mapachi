Mapachi Inmobiliaria

Plataforma de gestión inmobiliaria (venta, alquiler y pozo) desarrollada en PHP nativo + MySQL para el Trabajo Final de Producción Web.

Incluye CRUD completo de propiedades, tres roles de usuario con permisos diferenciados, contratos de alquiler con cuotas y ajuste automático por ICL (API del BCRA), reclamos, comprobantes de pago, y un panel de gestión con sidebar para usuarios logueados.

Instalación en XAMPP
1. Ubicar el proyecto

Copiá la carpeta proyecto dentro de tu htdocs de XAMPP, renombrada como quieras (por ejemplo mapachi o TP1_Mapachi):

C:\xampp\htdocs\mapachi\
2. Ajustar el nombre de la carpeta en la config

Abrí config/config.php y confirmá que BASE_PATH coincida EXACTO con el nombre de la carpeta que usaste:

php
define('BASE_PATH', '/mapachi');
3. Crear la base de datos

Arrancá Apache y MySQL desde el Panel de Control de XAMPP. Entrá a http://localhost/phpmyadmin, pestaña SQL, y pegá todo el contenido de database/schema.sql. Ejecutá.

Esto crea la base mapachi, todas las tablas, y carga datos de prueba: usuarios, 25 propiedades, contratos con cuotas, reclamos y comprobantes.

4. Verificar la conexión

Si tu MySQL tiene contraseña para root (no es el caso por defecto en XAMPP), editá config/database.php y completá la constante PASS.

5. Habilitar mod_rewrite

XAMPP lo trae activado por defecto. Si las URLs no funcionan (404 en todo menos la home), revisá en httpd.conf que esté descomentada la línea:

LoadModule rewrite_module modules/mod_rewrite.so
6. Acceder

Entrá a http://localhost/mapachi/ (o el nombre que le hayas puesto).

Usuarios de prueba

Contraseña 123456 para todos:

Email	Rol
admin@mapachi.com	Administrador
agente@mapachi.com	Agente Inmobiliario
dueno1@mapachi.com	Cliente — Propietario
dueno2@mapachi.com	Cliente — Propietario
cliente@mapachi.com	Cliente — Inquilino
inquilino2@mapachi.com	Cliente — Inquilino