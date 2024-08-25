FROM node:22.2-alpine

WORKDIR /usr/src/app/syntax-squad/chat-service

COPY package.json ./
COPY package-lock.json ./

RUN npm install

COPY . .

EXPOSE 2001

CMD ["npm", "start"]