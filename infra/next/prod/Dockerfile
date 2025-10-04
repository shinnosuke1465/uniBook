FROM --platform=linux/amd64 node:24-bullseye

WORKDIR /next

COPY ./next/package*.json /next
RUN npm install

COPY ./next /next
RUN npm run build

CMD [ "npm", "run", "start" ]