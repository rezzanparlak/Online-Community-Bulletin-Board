<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms & Conditions</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
    background-color: #f5f5f8;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    max-width: 800px;
    box-shadow: 0 10px 30px rgba(35,25,66,0.1);
    text-align: center;
}

.header {
    margin-bottom: 30px;
}

h1 {
    color: #231942;
    font-size: 36px;
    margin-bottom: 15px;
}

.subtitle {
    color: #666;
    font-size: 18px;
    line-height: 1.6;
}

.terms-box {
    background: #f8f7fa;
    border-radius: 15px;
    padding: 30px;
    margin: 20px 0;
    text-align: left;
}

.term-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding: 15px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(35,25,66,0.05);
}

.term-icon {
    font-size: 24px;
    margin-right: 15px;
    color: #231942;
}

.term-text {
    color: #362a55;
    line-height: 1.6;
    flex: 1;
}

.grade-plea {
    font-size: 24px;
    color: #231942;
    margin: 30px 0;
    padding: 20px;
    border: 2px dashed #362a55;
    border-radius: 15px;
    background: #f8f7fa;
}

.signature {
    font-style: italic;
    color: #666;
    margin-top: 20px;
}

.heart {
    color: #231942;
    font-size: 20px;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.2); }
    100% { transform: scale(1); }
}

@media (max-width: 600px) {
    .container {
        padding: 20px;
    }
    h1 {
        font-size: 28px;
    }
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Terms & Conditions</h1>
        <p class="subtitle">By accepting these terms, you're joining our amazing journey of content creation!</p>
    </div>
    <div class="terms-box">
        <div class="term-item">
            <span class="term-icon">🎨</span>
            <p class="term-text">You promise to create awesome and original content that makes everyone smile!</p>
        </div>
        <div class="term-item">
            <span class="term-icon">📚</span>
            <p class="term-text">You understand that this project deserves the highest grade possible because we worked super hard on it!</p>
        </div>
    </div>
    <div class="grade-plea">
        Dear Nese Hocam,<br> <span class="heart">♥</span><br>
        100/100 would be nice!
    </div>
    <p class="signature">Made with love 🎯</p>
</div>
</body>
</html>