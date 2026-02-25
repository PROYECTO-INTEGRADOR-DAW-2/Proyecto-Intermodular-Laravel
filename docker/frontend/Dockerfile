# Etapa 1: Construcció (Build)
FROM node:20-alpine AS build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Etapa 2: Servir amb Nginx (Producció)
FROM nginx:stable-alpine AS production-stage
# Copiem el build de l'etapa anterior a la carpeta que Nginx sap llegir
COPY --from=build-stage /app/dist /usr/share/nginx/html
# Copiem la teva configuració personalitzada de nginx
COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]