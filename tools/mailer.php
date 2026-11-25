<?php
require_once __DIR__ . '/env.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

function send_contact_notification(array $message): bool
{
    if (!env('MAIL_HOST') || !env('MAIL_TO')) {
        return false;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST');
        $mail->Port = (int) env('MAIL_PORT', '587');
        $mail->SMTPAuth = env('MAIL_USER') !== null && env('MAIL_USER') !== '';
        $mail->Username = env('MAIL_USER', '');
        $mail->Password = env('MAIL_PASS', '');
        $encryption = env('MAIL_ENCRYPTION', 'tls');
        if ($encryption === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        } elseif ($encryption === 'tls') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }
        $mail->CharSet = 'UTF-8';

        $mail->setFrom(env('MAIL_FROM') ?: env('MAIL_TO'), env('MAIL_FROM_NAME', 'Portfolio'));
        $mail->addAddress(env('MAIL_TO'));
        $mail->addReplyTo($message['email'], $message['firstname'] . ' ' . $message['lastname']);

        $mail->Subject = 'Nouveau message de ' . $message['firstname'] . ' ' . $message['lastname'];
        $mail->Body = implode("\n", [
            'Nom : ' . $message['lastname'],
            'Prénom : ' . $message['firstname'],
            'E-mail : ' . $message['email'],
            'Téléphone : ' . ($message['phone'] ?: '-'),
            '',
            $message['body'],
        ]);

        $mail->send();

        return true;
    } catch (Exception $e) {
        error_log('Mail error: ' . $mail->ErrorInfo);

        return false;
    }
}
