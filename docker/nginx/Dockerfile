FROM nginx:1.10

ADD config/nginx.conf /etc/nginx/
COPY sites/*.conf /etc/nginx/sites-available/

WORKDIR /var/www

EXPOSE 80 443
