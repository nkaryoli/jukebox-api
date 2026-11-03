FROM php:8.5-apache-bookworm

RUN apt-get update \
	&& apt-get install -y --no-install-recommends \
		libpq-dev \
	&& docker-php-ext-install pdo_pgsql pdo_mysql \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

COPY docker/apache/jukebox.conf /etc/apache2/sites-available/jukebox.conf

RUN a2dissite 000-default \
	&& a2ensite jukebox