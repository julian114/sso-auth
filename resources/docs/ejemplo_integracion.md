# Guía de Integración: Autenticación con Microsoft 365

Este documento describe cómo integrar la autenticación con Microsoft 365 a través de la API de SSO-AUTH en diferentes sistemas y lenguajes.

## Descripción general del flujo

El flujo de autenticación con Microsoft 365 a través de nuestra API consiste en:

1. Solicitar una URL de autenticación a la API
2. Redirigir al usuario a esa URL para autenticarse con Microsoft
3. Microsoft redirige de vuelta a nuestro sistema después de la autenticación
4. Nuestro sistema procesa la respuesta y redirige a la URL de callback de tu aplicación con los datos de autenticación
5. Tu aplicación recibe y procesa estos datos

## Endpoints de la API

### 1. Obtener URL de autenticación
- **URL**: `POST /api/microsoft/auth-sso-url`
- **Parámetros**:
  - `callback_url`: URL a la que se redirigirá después de la autenticación (obligatorio)
- **Respuesta**:
```json
{
  "status": true,
  "auth_url": "https://login.microsoftonline.com/..."
}
```

### 2. Verificar token
- **URL**: `POST /api/microsoft/verify-token`
- **Parámetros**:
  - `token`: Token de Microsoft a verificar
- **Respuesta**:
```json
{
  "status": true,
  "user": {
    "id": "1",
    "name": "Usuario Ejemplo",
    "email": "usuario@ejemplo.com"
  }
}
```

## Ejemplos de integración

### PHP (con cURL)

```php
<?php
// Solicitar URL de autenticación
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://tu-dominio.com/api/microsoft/auth-sso-url");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'callback_url' => 'https://tu-app.com/auth/callback'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if ($data['status']) {
    // Redirigir al usuario a la URL de autenticación
    header('Location: ' . $data['auth_url']);
    exit;
}
```

### JavaScript (Frontend)

```javascript
// Solicitar URL de autenticación
async function iniciarAutenticacionMicrosoft() {
    try {
        const response = await fetch('https://tu-dominio.com/api/microsoft/auth-sso-url', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                callback_url: 'https://tu-app.com/auth/callback'
            })
        });
        
        const data = await response.json();
        
        if (data.status) {
            // Redirigir al usuario a la URL de autenticación
            window.location.href = data.auth_url;
        } else {
            console.error('Error:', data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}
```

## Procesamiento del callback

Tu aplicación debe implementar un endpoint para recibir el callback con los datos de autenticación. 
La URL que proporcionaste como `callback_url` recibirá un parámetro `auth_data` con la información 
del usuario en formato JSON codificado en base64.

### Ejemplo de procesamiento del callback (PHP)

```php
<?php
// En tu URL de callback
if (isset($_GET['auth_data'])) {
    // Decodificar los datos
    $authData = json_decode(base64_decode($_GET['auth_data']), true);
    
    if ($authData) {
        // Procesar la información del usuario
        $userId = $authData['id'];
        $userName = $authData['name'];
        $userEmail = $authData['email'];
        $token = $authData['token'];
        
        // Aquí puedes crear una sesión, guardar el token, etc.
        $_SESSION['user'] = [
            'id' => $userId,
            'name' => $userName,
            'email' => $userEmail,
            'token' => $token
        ];
        
        // Redirigir al usuario a tu dashboard
        header('Location: /dashboard');
        exit;
    }
} elseif (isset($_GET['auth_error'])) {
    // Manejar errores
    $errorData = json_decode(base64_decode($_GET['auth_error']), true);
    // Mostrar mensaje de error
}
```

## Consideraciones de seguridad

1. Valida siempre la información recibida en el callback.
2. Almacena los tokens de forma segura.
3. Implementa HTTPS en todas las comunicaciones.
4. Considera añadir un mecanismo de verificación para asegurar que el callback proviene de una fuente confiable (por ejemplo, usando un token CSRF o un estado compartido).
