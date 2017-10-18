FROM php:5.6-cli

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
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

#
# PHP extensions
#
RUN docker-php-ext-install \
    zip \
    intl \
    mbstring

#
# PHP configuration:
# - default configuration
# - timezone
#
COPY php.ini /usr/local/etc/php/php.ini
ARG TIMEZONE
RUN echo "\n""date.timezone = $TIMEZONE""\n" >> /usr/local/etc/php/php.ini

#
# Xdebug
#
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

#
# Phing
#
RUN pear channel-discover pear.phing.info \
    && pear install [--alldeps] phing/phing

#
# Composer + https://packagist.org/packages/hirak/prestissimo package
#
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('SHA384', 'composer-setup.php') === \
    '544e09ee996cdf60ece3804abc52599c22b1f40f4323403c44d44fdfdd586475ca9813a858088ffbc1f233e9b180f061') { echo \
    'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && composer global require --no-plugins --no-scripts hirak/prestissimo \
    && rm -rf /root/.composer/cache/*

#
# Bash
#
RUN sed -i 's/^# export/export/g' /root/.bashrc \
    && sed -i 's/^# alias/alias/g' /root/.bashrc \
    && echo "\n"'export PATH=/project/vendor/bin:$PATH'"\n" >> /root/.bashrc
