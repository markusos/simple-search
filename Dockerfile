FROM ubuntu:latest
RUN apt-get update && apt-get install php5 php5-json php5-mysql php5-memcached -y
ADD . /code