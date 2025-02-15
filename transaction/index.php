<?php
session_start();
require_once '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KBZ Pay Transaction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: #ffffff;
        min-height: 100vh;
        padding: 40px 0;
    }
    .transaction-form {
        padding: 40px;
        background: rgba(45, 45, 45, 0.98);
        border-radius: 25px;
        margin: 40px auto;
        max-width: 600px;
        box-shadow: 0 0 40px rgba(0,123,255,0.1);
        border: 1px solid rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
    }
    .form-control {
        padding: 10px 15px;
        font-size: 1rem;
    }
    .payment-info {
        padding: 20px;
        margin-bottom: 20px;
    }
    .kbz-logo {
        width: 150px;
        margin: 0 auto 20px;
    }
    .btn-primary {
        padding: 12px 30px;
        font-size: 1.1rem;
    }
    .transaction-form::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #007bff, #00ff88);
    }
    .transaction-title {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 2rem;
        background: linear-gradient(45deg, #007bff, #00ff88);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        text-align: center;
    }
    .form-control {
        background: rgba(54, 54, 54, 0.8);
        border: 2px solid rgba(64, 64, 64, 0.5);
        color: #fff;
        padding: 15px;
        border-radius: 12px;
    }
    .form-control:focus {
        background: rgba(64, 64, 64, 0.9);
        border-color: #007bff;
        color: #fff;
        box-shadow: 0 0 15px rgba(0,123,255,0.3);
    }
    .form-label {
        background: linear-gradient(45deg, #007bff, #00ff88);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .payment-info h5 {
        background: linear-gradient(45deg, #007bff, #00ff88);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 600;
        margin-bottom: 10px;
    }
    .payment-info p {
        color: #ffffff;
        font-size: 1.1rem;
        margin-bottom: 0;
    }
    .payment-info span {
        background: linear-gradient(45deg, #007bff, #00ff88);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: bold;
    }
    #phoneError {
        color: #ff4d4d;
    }
    .btn-primary {
        background: linear-gradient(45deg, #007bff, #00ff88);
        border: none;
        padding: 15px 40px;
        font-size: 1.2rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.4s ease;
    }
    .btn-primary:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,123,255,0.4);
    }
    .payment-info {
        background: rgba(35, 35, 35, 0.7);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .kbz-logo {
        width: 200px;
        height: auto;
        margin: 0 auto 30px;
        display: block;
        filter: brightness(1.2);
        transition: all 0.4s ease;
        animation: logoFloat 3s ease-in-out infinite;
    }

    @keyframes logoFloat {
        0%, 100% {
            transform: translateY(0) scale(1);
            filter: brightness(1.2) drop-shadow(0 5px 15px rgba(0,123,255,0.2));
        }
        50% {
            transform: translateY(-10px) scale(1.05);
            filter: brightness(1.3) drop-shadow(0 15px 25px rgba(0,123,255,0.4));
        }
    }

    .kbz-logo:hover {
        animation-play-state: paused;
        transform: scale(1.1) rotate(2deg);
        filter: brightness(1.4) drop-shadow(0 20px 30px rgba(0,123,255,0.5));
    }
        
    .preview-image {
        border-radius: 15px;
        background: rgba(35, 35, 35, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
        transition: all 0.3s ease;
    }
    .btn-outline-primary {
        color: #ffffff;
        border: 2px solid;
        border-image: linear-gradient(45deg, #007bff, #00ff88) 1;
        background: transparent;
        transition: all 0.3s ease;
    }
    .btn-outline-primary:hover {
        background: linear-gradient(45deg, #007bff, #00ff88);
        border-color: transparent;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0,123,255,0.3);
    }
</style>


</head>
<body>
    <div class="container">
        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'success'): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    Transaction submitted successfully! We will process your payment shortly.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] === 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <?php 
                        $error_message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Error submitting transaction. Please try again.';
                        echo $error_message;
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="transaction-form">
            <img src="kbzpay_logo.png" alt="KBZ Pay Logo" class="kbz-logo">
            
            <h2 class="text-center mb-4"> Premium Purchase 
                <i class="fas fa-crown" style="color: gold; margin-left: 8px; animation: shimmer 2s infinite;"></i>
            </h2>

            <div class="payment-info mb-4 p-3" style="background: rgba(30, 30, 30, 0.7); border-radius: 12px; text-align: center; position: relative;">
                <img src="qr_code.png" alt="KBZ Pay QR Code" style="width: 100px; height: 100px; margin-bottom: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); 
                    transition: all 0.3s ease;
                    cursor: pointer;"
                    onmouseover="this.style.transform='scale(1.8)'; this.style.zIndex='1000'; this.style.boxShadow='0 15px 30px rgba(0,0,0,0.4)';"
                    onmouseout="this.style.transform='scale(1)'; this.style.zIndex='1'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.3)';">
                <h5 style="color: #007bff; margin-bottom: 10px;">Scan Me</h5>
                <p style="color: #fff; font-size: 1.1rem; margin-bottom: 0;">
                    Transfer to: <span style="color: #007bff; font-weight: bold;">09880177283</span>
                </p>
            </div>
            
            <form action="process.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           pattern="^(\+959|09)\d{9,11}$" 
                           title="Phone number must start with +959 or 09" required>
                    <div id="phoneError" class="text-danger mt-1" style="font-size: 0.875rem;"></div>
                </div>

                <div class="mb-3">
                    <label for="amount" class="form-label">Amount (MMK)</label>
                    <input type="number" class="form-control" id="amount" name="amount" required>
                </div>

                <div class="mb-3">
                    <label for="screenshot" class="form-label">Transaction Screenshot</label>
                    <input type="file" class="form-control" id="screenshot" name="screenshot" accept="image/*" required>
                    <img id="preview" class="preview-image">
                </div>

                <button type="submit" class="btn btn-primary w-100">Submit Transaction</button>
            </form>
        </div>
    </div>

    <script>
        // Preview image before upload
        document.getElementById('screenshot').onchange = function(evt) {
            const preview = document.getElementById('preview');
            preview.style.display = 'block';
            const [file] = this.files;
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        };
    </script>

</body> 
    <script>
        // Phone number validation
        document.getElementById('phone').addEventListener('input', function(e) {
            const phone = e.target.value;
            const phoneError = document.getElementById('phoneError');
            const phonePattern = /^(\+959|09)\d{9,11}$/;
            
            if (!phonePattern.test(phone)) {
                if (phone.startsWith('+959')) {
                    phoneError.textContent = 'Phone number must be 10-12 digits after +959';
                } else if (phone.startsWith('09')) {
                    phoneError.textContent = 'Phone number must be 9-11 digits after 09';
                } else {
                    phoneError.textContent = 'Phone number must start with +959 or 09';
                }
                e.target.setCustomValidity('Invalid phone number format');
            } else {
                phoneError.textContent = '';
                e.target.setCustomValidity('');
            }
        });
    </script>
</body>
</html>