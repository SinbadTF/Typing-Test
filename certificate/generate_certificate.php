<?php
require('config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('fpdf/fpdf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $proof_id = 'TT' . date('Ymd') . rand(1000, 9999);

        // Store in database
        $stmt = $pdo->prepare("INSERT INTO certificates (name, phone, address, proof_id) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $phone, $address, $proof_id]);

        // Create PDF
        $pdf = new FPDF('L', 'mm', 'A4');
        $pdf->AddPage();
        
        // Set background color
        $pdf->SetFillColor(45, 45, 45);
        $pdf->Rect(0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight(), 'F');
        
        // Add certificate content
        $pdf->SetFont('Arial', 'B', 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 30, 'Advanced Typing Test Certificate', 0, 1, 'C');
        
        // Add icon
        if (file_exists('typing-icon.png')) {
            $pdf->Image('typing-icon.png', ($pdf->GetPageWidth() - 30) / 2, 50, 30);
            $pdf->Ln(35); // Add space after icon
        }
        
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, 10, 'This is to certify that', 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'B', 24);
        $pdf->SetTextColor(0, 123, 255);
        $pdf->Cell(0, 20, $name, 0, 1, 'C');
        
        $pdf->SetFont('Arial', '', 14);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 10, 'has successfully completed the Advanced Typing Test', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(200, 200, 200);
        $pdf->Cell(0, 10, 'Contact: ' . $phone, 0, 1, 'C');
        $pdf->Cell(0, 10, 'Address: ' . $address, 0, 1, 'C');
        
        // Add proof ID with QR code
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(0, 20, 'Proof ID: ' . $proof_id, 0, 1, 'C');
        
        // Change output method to inline display for testing
        $pdf->Output('I', 'typing_certificate.pdf');
        exit();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Typing Certificate</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1a1a1a;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .certificate-form {
            max-width: 500px;
            margin: 50px auto;
            padding: 30px;
            background: rgba(45, 45, 45, 0.95);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.5);
            backdrop-filter: blur(10px);
        }
        .form-control {
            background-color: #363636;
            border: 1px solid #404040;
            color: #fff;
            padding: 12px;
        }
        .form-control:focus {
            background-color: #404040;
            border-color: #007bff;
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .form-label {
            color: #007bff;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 12px;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="certificate-form">
            <h2 class="text-center mb-4">Advanced Typing Certificate</h2>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Generate Certificate</button>
            </form>
        </div>
    </div>
</body>
</html>