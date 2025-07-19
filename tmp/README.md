# IELTS Platform

IELTS.com.au saytiga o'xshash, lekin o'zgartirilgan dizaynli IELTS tayyorgarlik platformasi.

## Xususiyatlari

- **Listening Tests** - 4 qismli audio testlar
- **Academic Reading** - Akademik o'qish testlari
- **Academic Writing** - Yozish testlari (Task 1 va Task 2)
- **General Training Reading** - Umumiy treninglar

## O'rnatish

```bash
# Dependencies o'rnatish
composer install
npm install

# Environment o'rnatish
cp .env.example .env
php artisan key:generate

# Database o'rnatish
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Serverni ishga tushirish
php artisan serve
```

## Texnologiyalar

- **Backend**: Laravel 12.x
- **Frontend**: Blade templates + TailwindCSS + Alpine.js
- **Database**: SQLite
- **Authentication**: Laravel Breeze

## Sahifalar

- Asosiy sahifa - Test kategoriyalari
- Test kategoriya sahifalari
- Individual test sahifalari
- Foydalanuvchi autentifikatsiyasi

## Litsenziya

MIT License