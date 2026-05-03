<?php
$name = htmlspecialchars($_GET['name'] ?? 'there');
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Message Sent Successfully! - Elias Araya</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

.success-container {
  background: white;
  border-radius: 24px;
  padding: 60px 40px;
  max-width: 600px;
  width: 100%;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.success-icon {
  width: 100px;
  height: 100px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 32px;
  animation: scaleIn 0.5s ease-out 0.2s both;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
  }
  to {
    transform: scale(1);
  }
}

.success-icon svg {
  width: 50px;
  height: 50px;
  stroke: white;
  stroke-width: 3;
  fill: none;
  stroke-linecap: round;
  stroke-linejoin: round;
  animation: checkmark 0.8s ease-out 0.4s both;
}

@keyframes checkmark {
  0% {
    stroke-dasharray: 100;
    stroke-dashoffset: 100;
  }
  100% {
    stroke-dasharray: 100;
    stroke-dashoffset: 0;
  }
}

h1 {
  font-size: 2.5rem;
  font-weight: 900;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  margin-bottom: 16px;
  letter-spacing: -0.02em;
}

.message {
  font-size: 1.15rem;
  color: #64748b;
  line-height: 1.8;
  margin-bottom: 32px;
}

.message strong {
  color: #334155;
  font-weight: 700;
}

.info-box {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
  border: 2px solid rgba(102, 126, 234, 0.2);
  border-radius: 16px;
  padding: 24px;
  margin-bottom: 32px;
  text-align: left;
}

.info-box h3 {
  font-size: 1.1rem;
  font-weight: 800;
  color: #334155;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  gap: 8px;
}

.info-box ul {
  list-style: none;
  padding: 0;
}

.info-box li {
  font-size: 0.95rem;
  color: #64748b;
  padding: 8px 0;
  padding-left: 24px;
  position: relative;
}

.info-box li::before {
  content: '✓';
  position: absolute;
  left: 0;
  color: #667eea;
  font-weight: 700;
}

.btn-home {
  display: inline-block;
  padding: 16px 40px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  text-decoration: none;
  border-radius: 12px;
  font-weight: 700;
  font-size: 1.05rem;
  transition: all 0.3s ease;
  box-shadow: 0 4px 16px rgba(102, 126, 234, 0.4);
}

.btn-home:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(102, 126, 234, 0.5);
}

@media (max-width: 640px) {
  .success-container {
    padding: 40px 24px;
  }
  
  h1 {
    font-size: 2rem;
  }
  
  .message {
    font-size: 1rem;
  }
}
</style>
</head>
<body>
  <div class="success-container">
    <div class="success-icon">
      <svg viewBox="0 0 52 52">
        <polyline points="14 27 22 35 38 19"/>
      </svg>
    </div>
    
    <h1>Message Sent Successfully!</h1>
    
    <p class="message">
      Thanks <strong><?= $name ?></strong>! Your message has been received and saved to my inbox.
    </p>
    
    <div class="info-box">
      <h3>📬 What happens next?</h3>
      <ul>
        <li>I'll review your message within 24 hours</li>
        <li>You'll receive a response via email</li>
        <li>Check your spam folder just in case</li>
      </ul>
    </div>
    
    <a href="index.php" class="btn-home">← Back to Portfolio</a>
  </div>
</body>
</html>
