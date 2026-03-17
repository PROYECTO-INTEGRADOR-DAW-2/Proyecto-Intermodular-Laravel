# Etapa 1: Construcció (Build)
# Stage 1: Build stage
FROM node:20-alpine as build-stage
WORKDIR /app
COPY frontend/package*.json ./
RUN npm install
COPY frontend/ .
RUN npm run build

# Stage 2: Production stage
FROM nginx:stable-alpine as production-stage
# Copiem el build de l'etapa anterior a la carpeta que Nginx sap llegir
COPY --from=build-stage /app/dist /usr/share/nginx/html
# Copiem la teva configuració personalitzada de nginx
COPY docker/frontend/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]