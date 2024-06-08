FROM node:22.2-alpine

WORKDIR /usr/src/app/syntax-squad/auth-service

COPY /usr/src/app/syntax-squad/auth-service/package.json ./
COPY /usr/src/app/syntax-squad/auth-service/package-lock.json ./

RUN npm install

COPY . .

EXPOSE 3001

CMD ["npm", "start"]