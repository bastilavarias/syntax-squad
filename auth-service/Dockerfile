FROM node:22.2-alpine

WORKDIR /usr/src/app/syntax-squad/auth-service

COPY package.json ./
COPY package-lock.json ./

RUN npm install

COPY . .

EXPOSE 3001

CMD ["npm", "run", "dev"]