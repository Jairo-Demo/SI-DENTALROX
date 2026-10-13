FROM php:8.2-cli

# Instalar dependencias del sistema y extensiones requeridas por Laravel
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm

# Limpiar cache de apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar extensiones de PHP requeridas para MySQL y Laravel
RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip

# Instalar Composer oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /app

# Copiar el codigo del proyecto
COPY . .

# Instalar paquetes de PHP y compilar estilos frontend con Vite
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
RUN npm install && npm run build

# Permisos para storage y cache
RUN chmod -R 777 storage bootstrap/cache

# Puerto que asigna Render dinamicamente
EXPOSE 10000

# Ejecutar migraciones y seeders automaticamente al arrancar, luego iniciar el servidor
CMD php artisan migrate --force && php artisan db:seed --force && php artisan serve --host=0.0.0.0 --port=10000
