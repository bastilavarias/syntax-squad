FROM node:22.2-alpine

WORKDIR .

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3000

CMD [ "node", "server.js" ]