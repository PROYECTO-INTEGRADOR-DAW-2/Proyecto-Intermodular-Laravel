# J&A Sports - Global Project Documentation

Welcome to the **J&A Sports** official repository. This project is a modern e-commerce platform developed as an Intermodular Project. It integrates a PHP Backend (Laravel) and a Vue.js Frontend into a robust, cloud-native solution.

---

## 📂 Documentation Structure
The documentation is organized into several modules for clarity:

### 🌐 Global & Transversal
- **[Global Architecture](DOCS/GLOBAL_ARCHITECTURE.md)**: System design, data flow, and technology stack.
- **[Infrastructure & CI/CD](DOCS/INFRASTRUCTURE.md)**: AWS Cloud setup, Docker environments, and automated pipelines.
- **[Contribution Guidelines](DOCS/CONTRIBUTION.md)**: Branch strategy, code style, and team organization.

### 👤 User Information
- **[User Manual](DOCS/USER_MANUAL.md)**: How to navigate, shop, and manage the application.
- **[Test Credentials](DOCS/USER_MANUAL.md#6-test-credentials)**: Accounts for system validation.

---

## 🚀 Quick Start (Development)

This project uses **Docker Compose** to manage independent environments.

1. **Setup Backend**:
   ```bash
   docker compose --profile app up -d
   ```
   Access: `http://localhost:8000`

2. **Setup Frontend**:
   ```bash
   cd frontend
   npm install
   npm run dev
   ```
   Access: Port provided by Vite in console.

3. **Running Tests**:
   ```bash
   docker compose --profile test up -d
   ```

---

## ☁️ Infrastructure at a Glance (AWS)
- **Domain**: `projecteXX.ddaw.es`
- **Computing**: Dockerized EC2 instances.
- **Database**: AWS RDS (MySQL) with Multi-AZ.
- **Entry**: Application Load Balancer (ALB) + HTTPS (Let's Encrypt).
- **Security**: Sanctum Auth + Role-based Access Control (RBAC).

---

## 🛠 Tech Stack
- **Backend**: Laravel 11, Sanctum, MySQL, Redis.
- **Frontend**: Vue 3, Pinia, Bootstrap 5, Vee-Validate.
- **Automation**: N8N Webhooks, GitHub Actions.
- **API Docs**: Swagger (accessible at `/api/documentation`).

---

## ✅ Sprint Status & Deliverables
The project has successfully completed Sprints 1 through 6, implementing:
- [x] Product CRUD & Search.
- [x] Cart & Checkout system.
- [x] OAuth2 (Google) Integration.
- [x] Automated Role Management.
- [x] Intelligent Webhooks (N8N) for eco-friendly communications.
- [x] Full Infrastructure in AWS.

---
*For more detailed information, please refer to the files in the [DOCS/](DOCS/) directory.*
### Variables de entorno necesarias (sin secretos)
El archivo `.env` controla el entorno del proyecto. Aquí se muestran algunas variables clave (modificando u ocultando las claves privadas):
```env
APP_NAME="J&A Sports"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=appstore_db
DB_USERNAME=sail
# DB_PASSWORD="..."

# Configuración de Mailer (Mails genéricos/N8N)
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025

# Configuración OAuth (Google) - Credenciales necesarias en local si se testea el login social
GOOGLE_CLIENT_ID="..."
# GOOGLE_CLIENT_SECRET="..."
GOOGLE_REDIRECT_URL="http://localhost/api/oauth/google/callback"

# Webhooks Externos (N8N Automation)
N8N_CONTACT_WEBHOOK_URL="..."
N8N_ORDER_WEBHOOK_URL="..."
```

### Estructura de carpetas y arquitectura
El proyecto se organiza bajo una estructura Mono-Repositorio que contiene el API de Laravel y el cliente independiente de Vue dentro de la misma raíz:
*   `/app`: Lógica principal del Backend (Modelos, Controladores de la API, Canales de Broadcasting).
*   `/database`: Migraciones, *Seeders* y *Factories* configuradas con la estructura relacional.
*   `/routes/api.php`: Rutas dedicadas exclusivamente a la API del proyecto.
*   `/frontend`: El núcleo integro de la aplicación cliente en Vue (Single Page Application).
    *   `src/components/`: Partes reutilizables de la interfaz de usuario (Navegación, Botones y Checkouts).
    *   `src/views/`: Las páginas completas visibles agrupadas por ruta (Home, Carrito, Roles administrativos).
    *   `src/stores/`: Gestión de estado de almacenamiento en Pinia (`cart.js`, `auth.js`).

### API básica (endpoints clave)
La accesibilidad de los recursos por parte del Front está diseñada siguiendo los principios REST. Algunos *endpoints* esenciales documentados:
*   `GET /api/products`: Obtiene el catálogo entero y activo de productos.
*   `GET /api/products/{id}`: Obtiene el detalle específico de un producto.
*   `POST /api/orders`: Registra un nuevo pedido a la base de datos tras la pasarela del Front.
*   `POST /api/login` y `POST /api/register`: Métodos tradicionales de autenticación y gestión de usuarios devueltos usando Sanctum Auth.
*   `GET /api/oauth/google/redirect`: Inicia el flujo de inicio de sesión social seguro a través de Google.

### Roles y Permisos
El sistema clasifica el tráfico web del Front limitando las vistas con middleware y *composables* (useRole):
*   **Guest (Invitado):** Puede ver el catálogo y tramitar en el carrito los productos.
*   **Cliente Autenticado:** Conserva compras históricas, tramita el Checkout y guarda un seguimiento.
*   **Administrador:** Tiene acceso único al panel `/admin`. Desde aquí puede visualizar gráficos de resúmenes de tienda, auditar los roles de todos los usuarios (concediendo privilegios a empleados) y modificar el estado visual de los pedidos de toda la plataforma a través de su propia API `/api/admin/orders`.

---

## 3️⃣ Manual de usuario

Esta sección te guiará para realizar un uso de principio a fin de la aplicación en caso de ser tu primera vez.

### Registro e Inicio de Sesión
Puedes registrarte mediante correo electrónico en el apartado **"Acceder"** de la barra superior. Alternativamente, para mayor comodidad, puedes utilizar automáticamente tu cuenta de **Google** de forma segura para realizar el acceso social sin rellenar formularios en todo momento.
La página asegurará correctamente tus campos informativos evaluando el nombre y las contraseñas sin recargar.

### Navegación por el Catálogo y Búsqueda
*   **Filtros Globales:** Utiliza la barra superior para cambiar las listas del catálogo rápidamente ("Hombre", "Mujer").
*   **Búsqueda Rápida:** En el propio menú, escribe las primeras letras del nombre de unas zapatillas o una prenda para ubicarte directamente tras pulsar el icono de Búsqueda roja.

### Carrito de Compras y Proceso de Compra *(Checkout)*
1. Haz click encima del nombre de un producto para seleccionar un diseño a gusto que quieras ver (cambiando la talla predefinida y decidiendo qué cantidad de unidades del producto necesitas añadir). Acto seguido presiona `Añadir al Carrito`.
2. Una vez estés conforme, dirigete a tu "Cesta/Carrito" (icono superior). Verás a simple vista y completamente claro una tabla que suma individualmente todos los precios incluyendo los impuestos e incidencias.
3. El botón del Checkout tramitará el listado y procederá a descontarlo de la base de existencias (stock). Recibirás automáticamente un **correo electrónico muy visual directamente en tu bandeja de entrada** si has proporcionado el permiso para el cliente N8N y eres un usuario autenticado de la base.

### Funciones de Administración
Si el registro fue bajo la insignia **Admin**, verás reflejado siempre junto a tu nombre la chapa roja respectiva y se habilitará tu link particular hacia el **"Panel Admin"**. Aquí dentro hallarás listados expandibles funcionales:
* Tablas con el tráfico comercial de cobro (para controlar y actualizar qué artículos se encuentran Listos para Enviar, Cancelados o Atendidos).
* Las jerarquías y borrados de usuarios internos.

### Resolución de Problemas Frecuentes y Centro de Soporte (FAQ)
Cualquier incidente técnico o de envíos posee a pie de página siempre su respectiva vía legal y de **Ayuda**, un apartado exclusivo que enviará la incidencia con tu mensaje, el equipo técnico mediante la propia API y notificará pertinentemente a nuestra bandeja interna privada utilizando los Webhooks automatizados provilegiados. 

---

## 4️⃣ Sesión de presentación al cliente (demo)
La evolución continua del proyecto se engloba en los diferentes **Sprints** logrados por el abanico profesional (arrancando con el modelaje ER de la BD, transicionando a las lógicas reactivas Vue y a las interfaces robustas, garantizando entornos integradores por Docker para el aseguramiento remoto y rematando tras los Sprints 5 y 6 la automatización externa o procesos Single Sign On - OAuth2).
El repositorio quedará estructurado limpiamente conteniendo *Tags/Releases* en control de versiones en las ramas secundarias.

---

## ✅ Checklist de Entregables — Sprint 5 y Sprint 6

L'aplicació ha completat amb èxit tots els objectius presentats a les memòries pedagògiques (format V):

### 🔗 C1 — Integración externa (OAuth2) (DWES)
- [x] Integración con **1 servicio externo** con OAuth2 (Google Workspace).
- [x] Endpoints implementados:
  - [x] `GET /api/oauth/google/redirect`
  - [x] `GET /api/oauth/google/callback`
- [x] Tokens administrados y protegidos con seguridad por el back de Sanctum.
- [x] Migración en MySQL adaptando la columna a identificadores de Google (`google_id`).
- [x] Vista principal nativa en Vue Router `/oauth/callback` que intercepta por props el query result para logear al visitante en local Storage y redirigirlo exitosamente.

### 📚 C2 — Documentación API con Swagger / OpenAPI (DWES)
- [x] Framework OpenAPI / L5-Swagger con anotaciones integradas sobre Controladores dedicados.

### ✨ C3 — Mejoras avanzadas en Vue (DWEC)
- [x] Listados fluidos y **filtros** reactivos sin recargar.
- [x] Formularios de la aplicación refactorizados mediante las librerías punteras **Vee-Validate + Yup** para emitir validaciones restrictivas (Emails corporativos o longitudes prohibidas de nombres tanto en Contacto como Register).

### 🎨 C4 — Diseño final y accesibilidad (DIW)
- [x] Interfaz de Usuario e imagen corporativa consistente en su paleta visual (Blanco / Rojos / Grises oscuros en barras).
- [x] Implementación semántica HTML moderna, respetando jerarquías claras para las fichas de los artículos.

### 🤖 C5 — Mejora digital / “inteligente” (DIG)
- [x] **Webhooks inteligentes por IA (N8N Automation):** Automatización del backend para integrarse a flujos basados en eventos, reenviando los correos electrónicos estéticos y formateados dinámicamente según la iteración propia del JSON mandado (`Customer Name, Images array...`), abstrayendo al servidor de PHP de realizar las comunicaciones de correo por sí sola y aligerando el peso de red.

### 🌱 C6 — Sostenibilidad (ASG + ecodiseño) (SOST)
- [x] Creación de apartados explicativos hacia el ecodiseño ecológico local dentro del espacio `SostenibilidadView.vue` del Footer.

---
**Agradecimientos conjuntos por el largo y provechoso trabajo desarrollado a lo largo de este Proyecto Intermodular.**
