# Vue frontend + Laravel backend

Estructura propuesta:

- `frontend/` — Aplicación Vue (Vite) con Vue Router y Axios.
- `backend/` — Instrucciones para crear un proyecto Laravel y exponer una ruta API de ejemplo.

Pasos rápidos frontend:

```bash
cd frontend
npm install
npm run dev
```

El frontend espera que el backend Laravel esté en `http://localhost:8000` y exponga rutas bajo `/api`.

Instrucciones rápidas para crear el backend Laravel:

```bash
# desde la carpeta raíz del proyecto
composer create-project laravel/laravel backend
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

Añadir una ruta API de ejemplo en `routes/api.php`:

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/example', function (Request $request) {
    return response()->json(['message' => 'Hola desde Laravel']);
});
```

Habilitar CORS (Laravel 9+ ya incluye middleware CORS). Asegúrate en `config/cors.php` permitir origen `http://localhost:5173` o `*` durante desarrollo.

Después, abre `http://localhost:5173` para ver la app Vue y prueba el botón "Obtener mensaje" en la vista Home.
