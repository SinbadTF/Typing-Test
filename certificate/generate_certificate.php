<?php
session_start();
require_once '../config/database.php';
require_once '../vendor/autoload.php'; // Make sure you have TCPDF installed via composer

// Check if user is logged in and has passed the exam
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

// Fetch user's exam data with explicit column selection
$stmt = $pdo->prepare("
    SELECT ce.exam_id, ce.wpm, ce.accuracy, ce.exam_date, u.username 
    FROM certificate_exams ce 
    INNER JOIN users u ON ce.user_id = u.user_id 
    WHERE ce.user_id = ? AND ce.passed = 1 
    ORDER BY ce.exam_date DESC 
    LIMIT 1
");
$stmt->execute([$_SESSION['user_id']]);
$examData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$examData) {
    header('Location: ../index.php');
    exit();
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('Typing Test');
$pdf->SetAuthor('Typing Test');
$pdf->SetTitle('Typing Certificate');

// Remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', '', 20);

// Certificate content
// Update the HTML template to use username instead of full_name
// Update the date display in the HTML template
$html = <<<EOD
<style>
    .certificate { text-align: center; color: #333; }
    .title { font-size: 24pt; color: #2c3e50; margin-bottom: 20px; }
    .subtitle { font-size: 18pt; color: #34495e; margin-bottom: 15px; }
    .name { font-size: 20pt; color: #2980b9; margin: 20px 0; }
    .details { font-size: 12pt; color: #7f8c8d; margin: 10px 0; }
    .date { font-size: 10pt; color: #95a5a6; margin-top: 30px; }
</style>
<div class="certificate">
    <h1 class="title">Certificate of Achievement</h1>
    <div class="subtitle">This is to certify that</div>
    <div class="name">{$examData['username']}</div>
    <div class="details">has successfully completed the Typing Test Certification</div>
    <div class="details">with the following results:</div>
    <div class="details">
        WPM: {$examData['wpm']}<br>
        Accuracy: {$examData['accuracy']}%
    </div>
    <div class="date">Date of Achievement: {$examData['exam_date']}</div>
</div>
EOD;

// Output the certificate
$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('typing_certificate.pdf', 'I');