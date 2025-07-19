#!/bin/bash

# IELTS Platform ni GitLab ga push qilish

echo "1. Loyihani localhost ga ko'chiring:"
echo "   - ielts-platform-complete.tar.gz faylini yuklab oling"
echo "   - tar -xzf ielts-platform-complete.tar.gz"
echo ""

echo "2. GitLab repository yarating:"
echo "   https://gitlab.com/temureshniyozov007/newielist"
echo ""

echo "3. Git operatsiyalari:"
cat << 'EOF'
cd your-project-folder

# Git initialize
git init
git add .
git commit -m "Initial IELTS Platform commit"

# GitLab remote qo'shish
git remote add origin https://gitlab.com/temureshniyozov007/newielist.git

# Push qilish
git branch -M main
git push -u origin main
EOF

echo ""
echo "4. Local o'rnatish:"
cat << 'EOF'
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan serve
EOF