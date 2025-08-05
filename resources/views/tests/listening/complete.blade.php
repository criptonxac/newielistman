@extends('layouts.student')

@section('title', 'Test Yakunlandi - ' . $test->title)
@section('description', 'IELTS Listening Test yakunlandi')

@section('content')
<style>
    .completion-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 30px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
    }
    
    .completion-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .success-icon {
        font-size: 4rem;
        color: #48bb78;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateY(0);
        }
        40% {
            transform: translateY(-10px);
        }
        60% {
            transform: translateY(-5px);
        }
    }
    
    .completion-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 15px;
    }
    
    .completion-subtitle {
        font-size: 1.2rem;
        color: #718096;
        margin-bottom: 30px;
    }
    
    .test-info {
        background: #f7fafc;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        border-left: 5px solid #4299e1;
    }
    
    .test-info h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 15px;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-label {
        font-weight: 600;
        color: #4a5568;
    }
    
    .info-value {
        font-weight: 700;
        color: #2d3748;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 15px 30px;
        border: none;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4299e1, #3182ce);
        color: white;
        box-shadow: 0 10px 25px rgba(66, 153, 225, 0.3);
    }
    
    .btn-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(66, 153, 225, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #48bb78, #38a169);
        color: white;
        box-shadow: 0 10px 25px rgba(72, 187, 120, 0.3);
    }
    
    .btn-secondary:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(72, 187, 120, 0.4);
        color: white;
        text-decoration: none;
    }
    
    .processing-message {
        background: linear-gradient(135deg, #fef5e7, #fed7aa);
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 30px;
        border-left: 5px solid #f6ad55;
    }
    
    .processing-message p {
        color: #c05621;
        font-weight: 600;
        margin: 0;
    }
</style>

<div class="completion-container">
    <div class="completion-card">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="completion-title">Test Muvaffaqiyatli Yakunlandi!</h1>
        <p class="completion-subtitle">{{ $test->title }} testi yakunlandi</p>
        
        <div class="test-info">
            <h3><i class="fas fa-info-circle mr-2"></i>Test Ma'lumotlari</h3>
            
            <div class="info-item">
                <span class="info-label">Test nomi:</span>
                <span class="info-value">{{ $test->title }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Test turi:</span>
                <span class="info-value">IELTS Listening</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Yakunlangan vaqt:</span>
                <span class="info-value">{{ $attempt->completed_at ? $attempt->completed_at->format('d.m.Y H:i') : 'Hozir' }}</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="info-value">
                    <span class="badge badge-success">
                        <i class="fas fa-check mr-1"></i>Yakunlandi
                    </span>
                </span>
            </div>
        </div>
        
        <div class="processing-message">
            <p>
                <i class="fas fa-clock mr-2"></i>
                Javoblaringiz tekshirilmoqda. Natijalar tez orada tayyor bo'ladi.
            </p>
        </div>
        
        <div class="action-buttons">
            <a href="{{ route('student.tests.index') }}" class="btn btn-primary">
                <i class="fas fa-list mr-2"></i>Testlar ro'yxati
            </a>
            
            <a href="{{ route('student.results') }}" class="btn btn-secondary">
                <i class="fas fa-chart-line mr-2"></i>Natijalarni ko'rish
            </a>
        </div>
    </div>
</div>

<style>
    .badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
    }
    
    .badge-success {
        background: linear-gradient(135deg, #48bb78, #38a169);
        color: white;
    }
</style>
@endsection
