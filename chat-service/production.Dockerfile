FROM node:22.2-alpine

WORKDIR /usr/src/app/syntax-squad/chat-service

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3001

CMD ["npm", "run", "start"]