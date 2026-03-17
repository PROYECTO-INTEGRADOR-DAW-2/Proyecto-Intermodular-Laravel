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

# Instalar OpenSSL y utilidades de Apache para htpasswd
RUN apk add --no-cache openssl apache2-utils

# Generar certificados auto-firmados
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/nginx-selfsigned.key \
    -out /etc/ssl/certs/nginx-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Batoi/OU=DAW/CN=projectegrupb.es" \
    -addext "subjectAltName = DNS:projectegrupb.es, DNS:app.projectegrupb.es, DNS:backup.projectegrupb.es"

# Crear archivo .htpasswd para los backups (admin:admin123)
RUN htpasswd -bc /etc/nginx/.htpasswd admin admin123

# Copiem el build de l'etapa anterior a la carpeta que Nginx sap llegir
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Copiem la teva configuració personalitzada de nginx
COPY docker/frontend/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80 443
CMD ["nginx", "-g", "daemon off;"]