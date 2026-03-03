# LMS Base - Setup Instructions

## Prerequisites

### PHP Installation

```bash
sudo apt update
sudo apt install php-pcntl
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
```

## Installation

### 1. Start Docker Services

```bash
docker compose up -d
```

### 2. Run Database Migrations

```bash
php artisan migrate
```

Or migrate with seeding:

```bash
php artisan migrate:fresh --seed
```

### 3. Start the Development Server

```bash
php artisan serve
```

## Default Credentials

- **Email:** test@example.com
- **Password:** password
