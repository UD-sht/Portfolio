<?php
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'vendor/autoload.php'; // Adjust path if needed

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    $name = filter_var($input['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
    $message = filter_var($input['message'], FILTER_SANITIZE_STRING);
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        throw new Exception('All fields are required');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'udshrestha48@gmail.com'; // Replace with your Gmail address
    $mail->Password = '2057@Uday'; // Replace with your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    
    // Recipients
    $mail->setFrom($email, $name);
    $mail->addAddress('udshrestha48@gmail.com'); // Your receiving email
    $mail->addReplyTo($email, $name);
    
    // Content
    $mail->isHTML(false);
    $mail->Subject = 'New Contact Form Message from ' . $name;
    $mail->Body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    
    // Send email
    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully']);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>