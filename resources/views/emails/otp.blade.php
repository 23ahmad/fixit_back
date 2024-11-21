<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            text-align: center;
            padding: 20px;
        }
        .otp-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-block;
        }
        .otp-code {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
            margin: 20px 0;
        }
        .message {
            font-size: 16px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <div class="otp-container">
        <h2>OTP Code</h2>
        <p class="message">Use the following OTP to complete your login:</p>
        <div class="otp-code">{{ $otp }}</div>
        <p>This code will expire in 10 minutes.</p>
    </div>

</body>
</html>
