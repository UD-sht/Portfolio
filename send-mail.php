<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Adjust path if needed

header('Content-Type: application/json');
// Enable CORS (restrict to your domain in production)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Sanitize and validate inputs
    $name = filter_var($input['name'] ?? '', FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $message = filter_var($input['message'] ?? '', FILTER_SANITIZE_STRING);
    
    if (empty($name) || empty($email) || empty($message)) {
        throw new Exception('All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    // Enable debugging (set to 0 in production)
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        file_put_contents('debug.log', date('Y-m-d H:i:s') . " [$level] $str\n", FILE_APPEND);
    };
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'udshrestha48@gmail.com';
    $mail->Password = 'mxll njzq qhvw eyfm'; // Replace with your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Recipients
    $mail->setFrom($email, $name);
    $mail->addAddress('udshrestha48@gmail.com');
    $mail->addReplyTo($email, $name);
    
    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Contact Form Message from ' . $name;
    $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    
    // Send email
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    // Log error to file
    file_put_contents('error.log', date('Y-m-d H:i:s') . ' - ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>