# LMS Base - Setup Instructions

## Prerequisites

### PHP Installation

```bash
sudo apt update
sudo apt install php-pcntl
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"
```

### Optional Tools

```bash
curl -fsSL https://bun.com/install | bash
npm install -g @kilocode/cli
curl -fsSL https://opencode.ai/install | bash
```

## Installation

### 1. Database Setup

#### MySQL

If using MySQL, start the Docker services:

```bash
docker compose up -d
```

#### SQLite

If using SQLite, update your `.env` file and create the database file:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/home/faaris/projects/tih/lms-base0/database/database.sqlite
```

```bash
touch database/database.sqlite
```

### 2. Install Dependencies

```bash
composer install
npm install
php artisan key:generate
```

### 3. Run Database Migrations

```bash
php artisan migrate
```

Or migrate with seeding:

```bash
php artisan migrate:fresh --seed
```

### 4. Start the Development Server

```bash
composer dev
```

## Default Credentials

- **Email:** `admin@example.com`
- **Password:** `password`
