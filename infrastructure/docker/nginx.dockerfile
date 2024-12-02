FROM nginx:alpine

WORKDIR /usr/share/nginx/html

# Remove the default content from the Nginx default web root
RUN rm -rf /usr/share/nginx/html/*

ADD ./nginx.conf /etc/nginx/conf.d/
