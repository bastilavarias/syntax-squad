FROM node:22.2-alpine

WORKDIR /usr/src/app/syntax-squad/chat-service

COPY /usr/src/app/syntax-squad/chat-service/package.json ./
COPY /usr/src/app/syntax-squad/chat-service/package-lock.json ./

RUN npm install

COPY . .

EXPOSE 3002

CMD ["npm", "run", "dev"]