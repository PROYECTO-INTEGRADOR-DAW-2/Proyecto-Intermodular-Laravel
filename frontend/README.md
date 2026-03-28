# Documentación del Frontend - J&A Sports

## Arquitectura
El frontend es una **Single Page Application (SPA)** construida con **Vue 3** y la **Composition API**. Se comunica con el Backend de Laravel a través de una API RESTful.

## Stack Tecnológico
- **Framework**: Vue 3
- **Herramienta de Construcción**: Vite
- **Gestión de Estado**: Pinia
- **Enrutamiento**: Vue Router
- **Estilos**: Bootstrap 5
- **Validación**: Vee-Validate + Yup
- **Cliente HTTP**: Axios

## Estructura del Proyecto
- `src/components`: Componentes de interfaz de usuario reutilizables (Nav, Footer, Cards).
- `src/views`: Componentes a nivel de página mapeados a rutas.
- `src/stores`: Almacenes de Pinia para `auth`, `cart` y `products`.
- `src/services`: Capa de abstracción de la API.
- `src/router`: Definiciones de rutas y protectores de navegación (guards).

## Ejecución en Desarrollo
1. Navega al directorio `frontend`.
2. Instala las dependencias: `npm install`.
3. Inicia el servidor de desarrollo: `npm run dev`.
4. Abre la URL proporcionada en la consola (usualmente `http://localhost:5173`).

NOTA: todos estos pasos los realiza automaticamente el comando make setup en un rama de desarrollo

## Proceso de Construcción (Build)
Para generar un paquete listo para producción:
```bash
npm run build
```
La salida estará en el directorio `dist/`.

## Particularidades del Despliegue
En producción, el frontend se sirve como activos estáticos por un servidor web (Nginx/Apache). Está optimizado utilizando el proceso de empaquetado de Vite, que incluye:
- Tree-shaking.
- División de código (Code splitting).
- Compresión de activos (imágenes WebP/AVIF).
- Carga perezosa (Lazy loading) para rutas e imágenes.

## Ayuda Contextual
La aplicación incluye varias funciones de accesibilidad y ayuda:
- **Tooltips**: Aplicados a iconos y botones complejos.
- **Retroalimentación de Formularios**: Mensajes de validación en tiempo real para todos los campos de entrada.
- **Guías de Pago**: Pasos claros y resúmenes visuales durante el proceso de compra.
- **Eco-etiquetas**: Indicadores visuales para productos sostenibles.
