FROM dunglas/frankenphp:1-php8.3

ARG USER=www-data
ARG USER_UID=1000
ARG USER_GID=1000

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN mkdir -p /var/www/.cache && chown -R ${USER}:${USER} /var/www/.cache

RUN \
 usermod -u ${USER_UID} ${USER} && groupmod -g ${USER_GID} ${USER} && \
 setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp && \
 chown -R ${USER}:${USER} /data/caddy && chown -R ${USER}:${USER} /config/caddy

RUN \
 apt-get update && \
 apt-get -y install --no-install-recommends nano unzip curl && \
 apt-get clean && \
 rm -rf /var/lib/apt/lists/*

RUN install-php-extensions \
	gd \
	intl \
	zip \
	opcache \
    apcu \
    pdo_pgsql

USER ${USER}