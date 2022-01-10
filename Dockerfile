#FROM php:8.0-apache
#RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
#RUN apt-get update && apt-get upgrade -y

FROM node as task37api
WORKDIR /usr/app
RUN apt update && apt install npm -y && apt install --reinstall nodejs -y
RUN npm install cors mysql express url 
COPY ./script .

FROM node as sankeyapi
WORKDIR /usr/app
RUN apt update && apt install npm -y && apt install --reinstall nodejs -y
RUN npm install cors mysql express url
COPY ./script .

FROM php:8.0-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN apt-get update && apt-get upgrade -y
