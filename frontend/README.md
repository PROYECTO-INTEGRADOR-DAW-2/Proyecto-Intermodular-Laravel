# Frontend Documentation - J&A Sports

## Architecture
The frontend is a **Single Page Application (SPA)** built with **Vue 3** and the **Composition API**. It communicates with the Laravel Backend via a RESTful API.

## Tech Stack
- **Framework**: Vue 3
- **Build Tool**: Vite
- **State Management**: Pinia
- **Routing**: Vue Router
- **Styling**: Bootstrap 5 + Sass
- **Validation**: Vee-Validate + Yup
- **HTTP Client**: Axios

## Project Structure
- `src/components`: Reusable UI components (Nav, Footer, Cards).
- `src/views`: Page-level components mapped to routes.
- `src/stores`: Pinia stores for `auth`, `cart`, and `products`.
- `src/services`: API abstraction layer.
- `src/router`: Route definitions and navigation guards.

## Execution in Development
1. Navigate to the `frontend` directory.
2. Install dependencies: `npm install`.
3. Start the dev server: `npm run dev`.
4. Open the URL provided in the console (usually `http://localhost:5173`).

## Build Process
To generate a production-ready bundle:
```bash
npm run build
```
The output will be in the `dist/` directory.

## Deployment Particularities
In production, the frontend is served as static assets by a web server (Nginx/Apache). It is optimized using Vite's bundling process, which includes:
- Tree-shaking.
- Code splitting.
- Asset compression (WebP/AVIF images).
- Lazy loading for routes and images.

## Contextual Help
The application includes several accessibility and help features:
- **Tooltips**: Applied to icons and complex buttons.
- **Form Feedback**: Real-time validation messages for all input fields.
- **Checkout Guides**: Clear steps and visual summaries during the purchase process.
- **Eco-labels**: Visual indicators for sustainable products.
