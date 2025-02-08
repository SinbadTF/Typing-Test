<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            background-image: url('keyboard_bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            background-repeat: no-repeat;
            min-height: 100vh;
        }
        .success-card {
            max-width: 500px;
            margin: 100px auto;
            padding: 40px;
            background: rgba(45, 45, 45, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
            animation: successPop 0.5s ease-out;
        }
        @keyframes successPop {
            0% { transform: scale(0); }
            70% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px 30px;
            transition: all 0.3s ease;
            margin: 10px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }
        h2 {
            color: #fff;
            margin-bottom: 20px;
        }
        .message {
            color: #adb5bd;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-card">
            <i class="fas fa-check-circle success-icon"></i>
            <h2>Transaction Successful!</h2>
            <p class="message">Your payment has been processed successfully. Thank you for your purchase.</p>
            <div>
                <a href="index.php" class="btn btn-primary">New Transaction</a>
                <a href="transactions.php" class="btn btn-primary">View Transactions</a>
            </div>
        </div>
    </div>
</body>
</html>