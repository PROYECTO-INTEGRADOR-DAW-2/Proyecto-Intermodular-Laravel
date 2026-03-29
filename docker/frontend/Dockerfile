# Solo usamos Nginx, ya no necesitamos Node en el servidor
FROM nginx:stable-alpine as production-stage

# Instalar utilidades básicas
RUN apk add --no-cache openssl apache2-utils

# Generar certificados auto-firmados (Igual que antes)
RUN openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
    -keyout /etc/ssl/private/nginx-selfsigned.key \
    -out /etc/ssl/certs/nginx-selfsigned.crt \
    -subj "/C=ES/ST=Alicante/L=Alcoi/O=Batoi/OU=DAW/CN=projectegrupb.es" \
    -addext "subjectAltName = DNS:projectegrupb.es, DNS:app.projectegrupb.es, DNS:backup.projectegrupb.es"

RUN htpasswd -bc /etc/nginx/.htpasswd admin admin123

# COPIAMOS EL DIST QUE HEMOS SUBIDO DESDE GITHUB (ya no hay multi-stage)
COPY frontend/dist /usr/share/nginx/html
COPY docker/frontend/nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80 443
CMD ["nginx", "-g", "daemon off;"]
