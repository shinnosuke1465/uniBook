FROM --platform=linux/amd64 node:24-bullseye

WORKDIR /next

COPY ./next/package*.json /next
RUN npm install

COPY ./next /next

ARG NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY
ENV NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY=$NEXT_PUBLIC_STRIPE_PUBLISHABLE_KEY

RUN npm run build

CMD [ "npm", "run", "start" ]