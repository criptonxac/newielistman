# IELTS Platform - GitLab ga yuklash yo'riqnomasi

## GitLab Repository: https://gitlab.com/temureshniyozov007/newielist

### Qadamlar:

1. **GitLab da repository yarating:**
   - GitLab.com ga kiring
   - "New project" tugmasini bosing
   - Repository nomini kiriting: `newielist`

2. **Loyihani yuklash:**
   
   **Variant A: Git Clone + Push**
   ```bash
   # Replit Terminal da
   git clone https://gitlab.com/temureshniyozov007/newielist.git
   cd newielist
   
   # Fayllarni ko'chirish (manual)
   # Yoki quyidagi fayllarni GitLab Web IDE orqali yarating
   ```

   **Variant B: Manual file upload**
   - GitLab da "Web IDE" yoki "Upload files" dan foydalaning
   - Quyidagi fayllarni birma-bir yarating

### Asosiy fayllar ro'yxati:

**Ildiz papka:**
- composer.json
- package.json  
- .env.example
- artisan
- README.md
- tailwind.config.js
- vite.config.js
- postcss.config.js
- phpunit.xml

**app/ papkasi:**
- app/Http/Controllers/HomeController.php
- app/Http/Controllers/TestController.php
- app/Http/Controllers/TestCategoryController.php
- app/Models/Test.php
- app/Models/TestCategory.php
- app/Models/TestQuestion.php
- app/Models/UserTestAttempt.php
- app/Models/UserAnswer.php

**resources/ papkasi:**
- resources/views/layouts/main.blade.php
- resources/views/home.blade.php
- resources/views/welcome.blade.php
- resources/views/categories/index.blade.php
- resources/views/categories/show.blade.php

**database/ papkasi:**
- database/migrations/
- database/seeders/TestCategorySeeder.php
- database/seeders/TestSeeder.php
- database/seeders/DatabaseSeeder.php

**routes/ papkasi:**
- routes/web.php
- routes/auth.php

### Local da o'rnatish:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate
php artisan db:seed
php artisan serve
```

Platform http://localhost:8000 da ishlaydi.