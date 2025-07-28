<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }} - Test Natijalari Hisoboti</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 28px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
            font-size: 16px;
        }
        .user-info {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .user-info h2 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            opacity: 0.9;
        }
        .stat-card .score {
            font-size: 32px;
            font-weight: bold;
            margin: 0;
        }
        .skill-section {
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
        }
        .skill-header {
            background: #3b82f6;
            color: white;
            padding: 15px 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .skill-content {
            padding: 20px;
        }
        .test-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .test-item:last-child {
            border-bottom: none;
        }
        .test-name {
            font-weight: 500;
            color: #374151;
        }
        .test-score {
            font-weight: bold;
            color: #059669;
        }
        .test-date {
            font-size: 14px;
            color: #6b7280;
        }
        .no-tests {
            text-align: center;
            color: #9ca3af;
            font-style: italic;
            padding: 20px;
        }
        .chart-placeholder {
            background: #f9fafb;
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            color: #6b7280;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>IELTS Test Natijalari Hisoboti</h1>
            <p>{{ date('d.m.Y H:i') }} da yaratilgan</p>
        </div>

        <!-- User Info -->
        <div class="user-info">
            <h2>{{ $user->name }}</h2>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Ro'yxatdan o'tgan:</strong> {{ $user->created_at->format('d.m.Y') }}</p>
            <p><strong>Jami test urinishlari:</strong> {{ $totalAttempts }}</p>
        </div>

        <!-- Overall Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Umumiy O'rtacha</h3>
                <p class="score">{{ number_format($overallAverage, 1) }}%</p>
            </div>
            <div class="stat-card">
                <h3>Listening</h3>
                <p class="score">{{ number_format($averages['listening'], 1) }}%</p>
            </div>
            <div class="stat-card">
                <h3>Reading</h3>
                <p class="score">{{ number_format($averages['reading'], 1) }}%</p>
            </div>
            <div class="stat-card">
                <h3>Writing</h3>
                <p class="score">{{ number_format($averages['writing'], 1) }}%</p>
            </div>
        </div>

        <!-- Chart Placeholder -->
        <div class="chart-placeholder">
            <h3>Natijalar Diagrammasi</h3>
            <p>Bu yerda Listening, Reading, Writing bo'yicha natijalar chart ko'rinishida bo'ladi</p>
            <p>Chart.js yoki boshqa kutubxona bilan amalga oshiriladi</p>
        </div>

        <!-- Listening Results -->
        <div class="skill-section">
            <div class="skill-header">
                🎧 Listening ({{ count($skillResults['listening']) }} ta test)
            </div>
            <div class="skill-content">
                @if(count($skillResults['listening']) > 0)
                    <ul class="test-list">
                        @foreach($skillResults['listening'] as $result)
                        <li class="test-item">
                            <div>
                                <div class="test-name">{{ $result['test_name'] }}</div>
                                <div class="test-date">{{ $result['date'] }}</div>
                            </div>
                            <div class="test-score">{{ $result['score'] }}%</div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="no-tests">Listening testlari topshirilmagan</div>
                @endif
            </div>
        </div>

        <!-- Reading Results -->
        <div class="skill-section">
            <div class="skill-header">
                📖 Reading ({{ count($skillResults['reading']) }} ta test)
            </div>
            <div class="skill-content">
                @if(count($skillResults['reading']) > 0)
                    <ul class="test-list">
                        @foreach($skillResults['reading'] as $result)
                        <li class="test-item">
                            <div>
                                <div class="test-name">{{ $result['test_name'] }}</div>
                                <div class="test-date">{{ $result['date'] }}</div>
                            </div>
                            <div class="test-score">{{ $result['score'] }}%</div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="no-tests">Reading testlari topshirilmagan</div>
                @endif
            </div>
        </div>

        <!-- Writing Results -->
        <div class="skill-section">
            <div class="skill-header">
                ✍️ Writing ({{ count($skillResults['writing']) }} ta test)
            </div>
            <div class="skill-content">
                @if(count($skillResults['writing']) > 0)
                    <ul class="test-list">
                        @foreach($skillResults['writing'] as $result)
                        <li class="test-item">
                            <div>
                                <div class="test-name">{{ $result['test_name'] }}</div>
                                <div class="test-date">{{ $result['date'] }}</div>
                            </div>
                            <div class="test-score">{{ $result['score'] }}%</div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="no-tests">Writing testlari topshirilmagan</div>
                @endif
            </div>
        </div>

        <!-- Speaking Results -->
        <div class="skill-section">
            <div class="skill-header">
                🗣️ Speaking ({{ count($skillResults['speaking']) }} ta test)
            </div>
            <div class="skill-content">
                @if(count($skillResults['speaking']) > 0)
                    <ul class="test-list">
                        @foreach($skillResults['speaking'] as $result)
                        <li class="test-item">
                            <div>
                                <div class="test-name">{{ $result['test_name'] }}</div>
                                <div class="test-date">{{ $result['date'] }}</div>
                            </div>
                            <div class="test-score">{{ $result['score'] }}%</div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="no-tests">Speaking testlari topshirilmagan</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Bu hisobot IELTS Platform tomonidan avtomatik yaratilgan</p>
            <p>© {{ date('Y') }} IELTS Platform. Barcha huquqlar himoyalangan.</p>
        </div>
    </div>
</body>
</html>
