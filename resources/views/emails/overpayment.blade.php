<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Overpayment Notification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Overpayment Notification</h1>
        <p>Hello,</p>
        <p>We have detected an overpayment by <strong>{{ number_format($overpaidAmount, 2) }} BGN</strong> for your bank credit with unique ID <strong>{{ $uniqueId }}</strong>.</p>
        <p>We have adjusted the payment to match the outstanding balance, and no additional funds were taken beyond the owed sum of <strong>{{ number_format($owedAmount, 2) }} BGN</strong>.</p>
        <p>Please contact us if you have any questions or concerns.</p>
        <p>Thank you for your attention to this matter.</p>
        <p>Best regards,</p>
        <p>Your favorite company!</p>
    </div>
</body>
</html>
