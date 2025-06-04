# Configuración de Microsoft OAuth para la API de Autenticación

Este documento describe los pasos necesarios para configurar Microsoft OAuth para que funcione con la API de autenticación.

## Variables de entorno requeridas

Asegúrate de configurar las siguientes variables en tu archivo `.env`:

```
# Microsoft OAuth
MICROSOFT_CLIENT_ID=your-microsoft-client-id
MICROSOFT_CLIENT_SECRET=your-microsoft-client-secret
MICROSOFT_REDIRECT_URI="${APP_URL}/api/microsoft/callback-sso-url"
MICROSOFT_TENANT_ID=common
```

## Registro de la aplicación en Azure AD

1. Inicia sesión en el [Portal de Azure](https://portal.azure.com/).
2. Navega a "Azure Active Directory" > "Registros de aplicaciones".
3. Haz clic en "Nuevo registro".
4. Proporciona un nombre para tu aplicación.
5. Selecciona los tipos de cuenta compatibles (normalmente "Cuentas en cualquier directorio organizativo (cualquier directorio de Azure AD - multiinquilino) y cuentas personales de Microsoft").
6. En la sección "URI de redirección", establece el tipo a "Web" y la URL a `https://tu-dominio.com/api/microsoft/callback-sso-url`.
7. Haz clic en "Registrar".

## Configuración de permisos

Una vez registrada la aplicación:

1. En la sección "Autenticación", asegúrate de que "Tokens de acceso" y "Tokens de ID" estén seleccionados bajo "Concesiones implícitas y flujos híbridos".
2. En la sección "Certificados y secretos", crea un nuevo secreto de cliente y cópialo (este será tu `MICROSOFT_CLIENT_SECRET`).
3. En la sección "Permisos de API", añade los siguientes permisos:
   - Microsoft Graph > User.Read (delegado)
   - Cualquier otro permiso que necesites para tu aplicación

## Configuración en tu aplicación Laravel

1. Asegúrate de tener instalado el paquete Laravel Socialite:
```bash
composer require laravel/socialite
```

2. Configura el driver de Microsoft en `config/services.php`:
```php
'microsoft' => [
    'client_id' => env('MICROSOFT_CLIENT_ID'),
    'client_secret' => env('MICROSOFT_CLIENT_SECRET'),
    'redirect' => env('MICROSOFT_REDIRECT_URI', env('APP_URL') . '/api/microsoft/callback-sso-url'),
],
```

3. Añade el driver de Microsoft a la configuración de Socialite en `config/app.php`:
```php
'providers' => [
    // ...
    Laravel\Socialite\SocialiteServiceProvider::class,
],

'aliases' => [
    // ...
    'Socialite' => Laravel\Socialite\Facades\Socialite::class,
],
```

## Verificación de la configuración

Para verificar que la configuración es correcta:

1. Asegúrate de que tu aplicación esté accesible desde internet (o usa una herramienta como ngrok para pruebas locales).
2. Intenta obtener una URL de autenticación a través de la API:
```bash
curl -X POST -H "Content-Type: application/json" -d '{"callback_url":"https://tu-app.com/callback"}' https://tu-dominio.com/api/microsoft/auth-sso-url
```
3. Si recibes una URL válida, la configuración es correcta.

## Solución de problemas comunes

### Error: AADSTS700051 (La respuesta no contiene un parámetro 'state' obligatorio)

Asegúrate de que el parámetro 'state' se está manejando correctamente en tu aplicación. Laravel Socialite lo maneja automáticamente si no estás usando el modo 'stateless'.

### Error: AADSTS650056 (El valor de 'redirect_uri' no coincide con los URIs de redirección preconfigurados)

Asegúrate de que el URI de redirección que proporcionas en tu aplicación coincide exactamente con el configurado en Azure AD, incluyendo el protocolo (http/https), la ruta completa y cualquier puerto. 