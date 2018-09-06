FROM php:5.6-cli
MAINTAINER Meritoo <github@meritoo.pl>

#
# Tools & libraries
#
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        vim \
        git \
        zip \
        unzip \
        zlib1g-dev \
        libicu-dev \
    && apt-get clean \
    && rm -rf \
        /var/lib/apt/lists/* \
        /tmp/* \
        /var/tmp/*

#
# PHP extensions
#
RUN docker-php-ext-install \
    zip \
    intl \
    mbstring

#
# PHP extensions (PECL):
# - Xdebug
#
RUN pecl install \
        xdebug-2.5.5 \
    && docker-php-ext-enable \
        xdebug

COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

#
# PHP configuration:
# - default configuration
# - timezone
#
COPY php.ini /usr/local/etc/php/php.ini
ARG TIMEZONE
RUN ln -snf /usr/share/zoneinfo/${TIMEZONE} /etc/localtime \
    && echo ${TIMEZONE} > /etc/timezone \
    && printf '[PHP]\ndate.timezone = "%s"\n' ${TIMEZONE} > /usr/local/etc/php/conf.d/tzone.ini \
    && "date"

#
# Phing
#
RUN pear channel-discover pear.phing.info \
    && pear install [--alldeps] phing/phing

#
# Composer - environment variables:
# - disable warning about running commands as root/super user
# - disable automatic clearing of sudo sessions
#
# More:
# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
#
ENV COMPOSER_ALLOW_SUPERUSER 1

#
# Composer + https://packagist.org/packages/hirak/prestissimo package
#
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === \
        '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo \
        'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer global require \
        --no-plugins \
        --no-scripts \
        --no-progress \
        --no-suggest \
        --no-interaction \
        --prefer-dist \
        --optimize-autoloader \
        --classmap-authoritative \
        hirak/prestissimo \
    && rm -rf ~/.composer/cache/* \
    && composer clear-cache \
    && composer --version

#
# Bash
#
RUN sed -i 's/^# export/export/g; \
            s/^# alias/alias/g;' ~/.bashrc \
    && echo 'COLUMNS=200'"\n" >> ~/.bashrc

#
# Use project-related binaries globally
#
ENV PATH="/project/vendor/bin:${PATH}"

WORKDIR /project
