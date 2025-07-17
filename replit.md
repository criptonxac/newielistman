# IELTS Platform - Laravel

## Project Overview
IELTS.com.au saytiga o'xshash, lekin o'zgartirilgan dizaynli IELTS tayyorgarlik platformasi. Bu platforma IELTS imtihoniga tayyorlanayotgan talabalar uchun turli xil resurslar, test namunalari va tayyorgarlik materiallarini taqdim etadi.

## User Preferences
- User prefers communication in Uzbek language when possible
- Wants a platform similar to IELTS.com.au but with modified design
- Focus on Laravel framework for backend development

## Project Architecture
- **Framework**: Laravel 12.x
- **Database**: SQLite (development)
- **Frontend**: Blade templates with modern CSS/JS
- **CSS Framework**: TailwindCSS
- **Authentication**: Laravel Breeze/built-in auth

## Core Features (Based on IELTS.com.au)
1. **Familiarisation Tests**
   - Listening test simulator
   - Academic Reading test simulator  
   - Academic Writing test simulator
   - General Training Reading tests

2. **Test Resources**
   - Answer sheets download
   - Sample tests with different difficulty levels
   - Video guides and tutorials

3. **Preparation Materials**
   - Study guides and tips
   - IELTS preparation articles
   - Mobile app promotion

4. **User Features** 
   - Test booking system
   - Progress tracking
   - Results checking
   - Event registration

## Planned Pages Structure
- **Home Page**: Main landing with test links and stats
- **Familiarisation Tests**: Individual test pages (Listening, Reading, Writing)
- **Sample Tests**: Additional practice materials  
- **Preparation**: Study guides and tips
- **Resources**: Downloads and answer sheets
- **About**: Platform information
- **Contact**: Support and contact forms

## Recent Changes
- Initial Laravel project setup (July 17, 2025)
- Complete database schema created with migrations for test categories, tests, questions, user attempts, and answers
- Eloquent models built with proper relationships for the IELTS test system
- Controllers created (HomeController, TestController, TestCategoryController) with proper route definitions
- Modern UI/UX created with TailwindCSS and professional design
- Database seeded with authentic IELTS test data (Listening, Academic Reading, Academic Writing, General Training Reading)
- Sample tests and questions added with realistic content
- Laravel server successfully deployed and running on port 8000

## Database Structure
- **test_categories**: Listening, Academic Reading, Academic Writing, General Training Reading
- **tests**: 6 sample tests including familiarisation and sample tests
- **test_questions**: Multiple question types (multiple choice, fill blank, true/false, essay)
- **user_test_attempts**: Track user progress and attempts
- **user_answers**: Store user responses with scoring capability

## Current Status
✅ Platform fully functional with working Laravel server
✅ Database populated with realistic IELTS test content
✅ Modern responsive design with navigation and footer
✅ Test categories and individual test pages created
✅ User authentication system integrated (Laravel Breeze)

## Next Steps
- Add test taking functionality with timer and progress tracking
- Implement answer submission and scoring system
- Add audio/video components for Listening tests
- Create user dashboard with progress analytics
- Add PDF download functionality for answer sheets