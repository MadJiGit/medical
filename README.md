# Medical Consumables Sample Distribution

Bilingual website (BG/EN) for requesting free samples of medical consumables.

## Technologies

- **PHP 8.2+** / **Symfony 7.3**
- **MySQL/MariaDB**
- **Bootstrap 4**
- **Doctrine ORM**

## Features

- Product catalog (ostomy bags, accessories, etc.)
- Categorization by manufacturers and types
- Multilingual support (Bulgarian/English)
- Contact form with GDPR compliance
- Free sample request system
- FAQ and informational pages

## Installation

```bash
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console app:import-products
```

## Configuration

Copy `.env` to `.env.local` and configure:
- DATABASE_URL
- MAILER_DSN

## Production Deployment

```bash
composer install --no-dev --optimize-autoloader
php bin/console cache:clear --env=prod
php bin/console doctrine:migrations:migrate --no-interaction
```

Web root should point to `/public` directory.
