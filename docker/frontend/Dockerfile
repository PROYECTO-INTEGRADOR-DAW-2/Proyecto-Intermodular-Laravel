FROM node:20-alpine

WORKDIR /app

# Instalar dependencias para que estén disponibles en el contenedor
COPY package*.json ./
RUN npm install

EXPOSE 5173

CMD ["npm", "run", "dev", "--", "--host"]
