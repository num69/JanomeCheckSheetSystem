FROM php:7.4-fpm-buster

ENV ACCEPT_EULA=Y
ENV DEBIAN_FRONTEND=noninteractive

# Fix Debian Buster repository 404
RUN sed -i \
    's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' \
    /etc/apt/sources.list \
 && sed -i \
    's|http://security.debian.org/debian-security|http://archive.debian.org/debian-security|g' \
    /etc/apt/sources.list \
 && sed -i \
    '/buster-updates/d' \
    /etc/apt/sources.list \
 && echo \
    'Acquire::Check-Valid-Until "false";' \
    > /etc/apt/apt.conf.d/99no-check-valid-until

# Install dependencies and PHP Zip extension
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
    $PHPIZE_DEPS \
    curl \
    git \
    gnupg2 \
    apt-transport-https \
    ca-certificates \
    unixodbc \
    unixodbc-dev \
    libgssapi-krb5-2 \
    zip \
    unzip \
    zlib1g-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
 && docker-php-ext-install zip \
 && rm -rf /var/lib/apt/lists/*

# Allow legacy TLS negotiation for older SQL Server instances
RUN sed -i \
    's/^\s*MinProtocol\s*=.*/MinProtocol = TLSv1/' \
    /etc/ssl/openssl.cnf \
 && sed -i \
    's/^\s*CipherString\s*=.*/CipherString = DEFAULT@SECLEVEL=1/' \
    /etc/ssl/openssl.cnf

# Install Microsoft ODBC Driver
# and PHP SQL Server extensions
RUN curl -fsSL \
    https://packages.microsoft.com/keys/microsoft.asc \
    | apt-key add - \
 && curl -fsSL \
    https://packages.microsoft.com/config/debian/10/prod.list \
    > /etc/apt/sources.list.d/mssql-release.list \
 && apt-get update \
 && ACCEPT_EULA=Y \
    apt-get install -y --no-install-recommends \
    msodbcsql17 \
    mssql-tools \
 && pecl install \
    https://pecl.php.net/get/sqlsrv-5.10.1.tgz \
 && pecl install \
    https://pecl.php.net/get/pdo_sqlsrv-5.10.1.tgz \
 && docker-php-ext-enable \
    sqlsrv \
    pdo_sqlsrv \
 && rm -rf /var/lib/apt/lists/*