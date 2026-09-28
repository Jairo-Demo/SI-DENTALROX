# DentalRox - Sistema de Gestión Odontológica (Proyecto SI1)

Sistema web integral para la administración clínica, gestión de pacientes, especialidades, auditoría y control de accesos para consultorios odontológicos desarrollado bajo el patrón de arquitectura **MVC (Modelo-Vista-Controlador)** en **Laravel**.

---

## 🛠️ Requisitos Previos

Antes de comenzar, asegúrate de tener instalado en tu computadora:

* **PHP:** >= 8.2 (incluido en XAMPP, Laragon o standalone con extensiones `pdo_mysql`, `mbstring`, `openssl`).
* **Composer:** Gestor de paquetes de PHP ([getcomposer.org](https://getcomposer.org/)).
* **Node.js & NPM:** Versión 18 o 20 LTS ([nodejs.org](https://nodejs.org/)).
* **MySQL / MariaDB:** Servidor de base de datos local (mediante XAMPP, Laragon o MySQL Server).
* **Git:** Para clonar el repositorio.

---

## 🚀 Pasos de Instalación y Configuración

Sigue estos pasos en tu terminal (PowerShell o CMD) para levantar el proyecto desde cero:

### 1. Clonar el repositorio y entrar a la carpeta
```bash
git clone <URL_DEL_REPOSITORIO>
cd ProyectoSI1
```

### 2. Instalar dependencias de PHP (Backend)
```bash
composer install
```

### 3. Instalar dependencias de Node.js (Frontend)
```bash
npm install
```

### 4. Configurar el archivo de entorno (`.env`)
Copia el archivo de ejemplo `.env.example` para crear tu `.env`:

* **En Windows (PowerShell / CMD):**
  ```bash
  copy .env.example .env
  ```
* **En Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

### 5. Generar la clave de la aplicación
```bash
php artisan key:generate
```

### 6. Configurar la Base de Datos
Abre el archivo `.env` con tu editor de código y verifica las credenciales de tu servidor MySQL local:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dentalrox
DB_USERNAME=root
DB_PASSWORD=
```
*(Nota: Si usas XAMPP en el puerto `3307`, cambia `DB_PORT=3307`).*

### 7. Crear la Base de Datos en MySQL
Abre **phpMyAdmin** (o tu gestor MySQL) y crea una nueva base de datos llamada:
* **Nombre de la base de datos:** `dentalrox`
* **Cotejamiento (Collation):** `utf8mb4_unicode_ci`

### 8. Ejecutar las Migraciones y Seeders Iniciales
Este comando creará las 29 tablas relacionales y poblará los datos iniciales (especialidades y usuarios de prueba):
```bash
php artisan migrate --seed
```

### 9. Compilar los estilos y recursos del Frontend
```bash
npm run build
```

---

## ▶️ Ejecutar el Sistema

Inicia el servidor de desarrollo de Laravel:

```bash
php artisan serve
```

Luego abre tu navegador web e ingresa a:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 🔑 Credenciales de Acceso Iniciales (Usuarios de Prueba)

| Rol | Usuario | Contraseña | Permisos |
| :--- | :--- | :--- | :--- |
| **Administrador** | `admin` | `Admin123*` | Acceso completo (Usuarios, Seguridad, Bitácora, Especialidades, Pacientes) |
| **Odontólogo** | `odontologo1` | `Odonto123*` | Acceso clínico y gestión de pacientes |
