<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Sayfa Bulunamadı | BiBilet</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .container {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }
        
        h1 {
            font-size: 150px;
            margin: 0;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        h2 {
            font-size: 32px;
            margin: 20px 0;
        }
        
        p {
            font-size: 18px;
            margin: 20px 0;
            opacity: 0.9;
        }
        
        a {
            color: white;
            text-decoration: none;
            border: 2px solid white;
            padding: 15px 40px;
            border-radius: 50px;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        a:hover {
            background: white;
            color: #667eea;
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .icon {
            font-size: 100px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">🚌</div>
        <h1>404</h1>
        <h2>Sayfa Bulunamadı!</h2>
        <p>Aradığınız sayfa mevcut değil veya taşınmış olabilir.</p>
        <a href="<?php echo BASE_PATH; ?>/">Ana Sayfaya Dön</a>
    </div>
</body>
</html>
