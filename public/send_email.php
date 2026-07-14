<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

// Read input (JSON or form)
$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) $input = $json;
}

$to = $input['to'] ?? '';
$subject = $input['subject'] ?? 'No Subject';
$body = $input['body'] ?? '';

if (empty($to) || empty($body)) {
    http_response_code(400);
    echo json_encode(['status' => false, 'error' => 'to dan body wajib diisi']);
    exit;
}

try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'it@kobin.co.id';
    $mail->Password   = 'xhvu opsy rpgh scad';
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    // Pengirim
    $mail->setFrom('it@kobin.co.id', 'KOBIN');

    // Penerima
    $mail->addAddress($to);

    // Ubah ini dari false ke true agar HTML bisa dirender
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;
    
    // Optional: tambahkan plain text version untuk email client yang tidak support HTML
    $mail->AltBody = strip_tags($body);

    $mail->send();

    echo json_encode(['status' => true, 'message' => 'Email terkirim']);
    exit;
} catch (PHPMailerException $e) {
    http_response_code(500);
    echo json_encode(['status' => false, 'error' => 'PHPMailer error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => false, 'error' => 'Error: ' . $e->getMessage()]);
    exit;
}