<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi PlayStation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e6e4ff 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(108, 92, 231, 0.15);
            width: 100%;
            max-width: 800px;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, #6c5ce7 0%, #a29bfe 100%);
        }
        
        h2 {
            color: #6c5ce7;
            font-size: 32px;
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .hero-image {
            width: 200px;
            margin-bottom: 30px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(90deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
            text-decoration: none;
            padding: 12px 32px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 16px;
            transition: all 0.3s ease;
            margin: 10px;
            box-shadow: 0 4px 15px rgba(108, 92, 231, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(108, 92, 231, 0.4);
        }
        
        .btn i {
            margin-right: 10px;
        }
        
        .features {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 40px;
        }
        
        .feature {
            flex: 1;
            min-width: 200px;
            margin: 15px;
            text-align: center;
        }
        
        .feature i {
            font-size: 40px;
            color: #6c5ce7;
            margin-bottom: 15px;
        }
        
        .feature h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        
        .feature p {
            font-size: 14px;
            color: #666;
            line-height: 1.5;
        }
        
        .footer {
            margin-top: 40px;
            font-size: 14px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            .features {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fab fa-playstation"></i> Selamat Datang di Reservasi PlayStation</h2>
        
        <img src="image/logops.png" alt="PlayStation" class="hero-image">
        
        <p>Tempat terbaik untuk memesan waktu bermain PlayStation favorit Anda!</p>
        
        <div style="margin-top: 30px;">
            <a href="auth/login.php" class="btn"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="auth/register.php" class="btn"><i class="fas fa-user-plus"></i> Daftar</a>
        </div>
        
        <div class="features">
            <div class="feature">
                <i class="fas fa-gamepad"></i>
                <h3>Pilihan Game Lengkap</h3>
                <p>Tersedia berbagai pilihan game terbaru untuk PlayStation 5 dan PlayStation 4.</p>
            </div>
            
            <div class="feature">
                <i class="far fa-clock"></i>
                <h3>Reservasi Mudah</h3>
                <p>Pesan waktu bermain dengan cepat dan mudah melalui sistem online kami.</p>
            </div>
            
            <div class="feature">
                <i class="fas fa-trophy"></i>
                <h3>Pengalaman Premium</h3>
                <p>Nikmati pengalaman bermain dengan peralatan gaming berkualitas tinggi.</p>
            </div>
        </div>
        
        <div class="footer">
            <p>© 2025 Reservasi PlayStation. Semua hak dilindungi.</p>
        </div>
    </div>
</body>
</html>