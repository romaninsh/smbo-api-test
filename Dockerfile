FROM ubuntu:latest
MAINTAINER Romans <me@nearly.guru>


RUN echo cache clean 2
RUN apt-get update
RUN apt-get -y upgrade
RUN DEBIAN_FRONTEND=noninteractive apt-get -y install \

        apache2 \
        libapache2-mod-php5 \
        php5-mysql  \
        git \
        php5-curl \
        nullmailer bsd-mailx \
        && rm -rf /var/lib/apt/lists/*

RUN sed -i "s/variables_order.*/variables_order = \"EGPCS\"/g" /etc/php5/apache2/php.ini
RUN sed -i "s/variables_order.*/variables_order = \"EGPCS\"/g" /etc/php5/cli/php.ini
RUN sed -i "s/post_max_size.*/post_max_size = 50M/g" /etc/php5/apache2/php.ini
RUN sed -i "s/upload_max_filesize.*/upload_max_filesize = 50M/g" /etc/php5/apache2/php.ini

RUN a2enmod rewrite
RUN a2enmod headers

RUN ln -fs /usr/share/zoneinfo/Europe/London /etc/localtime


# Basic setup
RUN mkdir -p /app && rm -fr /var/www/html && ln -s /app /var/www/html
ADD . /app

# Configure and start apache
ADD files/run.sh /run.sh
ADD files/vhost.conf /etc/apache2/sites-enabled/000-default.conf

ADD https://getcomposer.org/installer /app/installer
RUN cd /app; php installer; php composer.phar install --no-dev

EXPOSE 80
WORKDIR /app
CMD /run.sh
