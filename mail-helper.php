<?php
// includes/mail-helper.php
// Professional email sending helper with SMTP support

/**
 * Send email using native PHP mail() or configure SMTP
 * @param string $to Recipient email
 * @param string $subject Email subject
 * @param string $message Email body (HTML)
 * @param string $from Sender email
 * @return bool
 */
function sendEmail($to, $subject, $message, $from = 'noreply@mandigateway.com') {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: MandiGateway <$from>\r\n";
    $headers .= "Reply-To: support@mandigateway.com\r\n";
    
    // For better deliverability, configure SMTP below
    /*
    // PHPMailer SMTP example (uncomment and configure)
    require_once 'PHPMailer/PHPMailer.php';
    require_once 'PHPMailer/SMTP.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com';
        $mail->Password   = 'your-password';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->setFrom($from, 'MandiGateway');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $message;
        return $mail->send();
    } catch (Exception $e) {
        error_log("Email failed: " . $mail->ErrorInfo);
        return false;
    }
    */
    
    return mail($to, $subject, $message, $headers);
}

function sendResetPasswordEmail($email, $reset_link) {
    $subject = "Reset Your Password - MandiGateway";
    $message = "
    <html>
    <head><title>Password Reset</title></head>
    <body>
        <h2>Hello,</h2>
        <p>You requested to reset your password. Click the link below:</p>
        <p><a href='$reset_link' style='background:#0d6efd; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;'>Reset Password</a></p>
        <p>This link expires in 1 hour.</p>
        <hr><p>MandiGateway Team</p>
    </body>
    </html>
    ";
    return sendEmail($email, $subject, $message);
}

function sendOrderConfirmationEmail($customer_email, $order_id, $total, $store_name) {
    $subject = "Order Confirmation #$order_id - $store_name";
    $message = "
    <html>
    <body>
        <h2>Thank you for your order!</h2>
        <p>Order #$order_id placed successfully.</p>
        <p>Total: Rs. $total</p>
        <p>$store_name Team</p>
    </body>
    </html>
    ";
    return sendEmail($customer_email, $subject, $message);
}
?>