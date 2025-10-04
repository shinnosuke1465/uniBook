FROM --platform=linux/amd64 nginx:1.28

WORKDIR /var/www/html

COPY /src/public /var/www/html/public
COPY /infra/web/prod/default.conf /etc/nginx/conf.d/default.conf