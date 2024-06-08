FROM node:22.2-alpine

WORKDIR /usr/src/app/syntax-squad/auth-service

COPY package*.json ./

RUN npm install

COPY . .

EXPOSE 3001

CMD ["sh", "-c", "if [ \"$NODE_ENV\" = \"production\" ]; then npm run start; else npm run dev; fi"]