<?php
require_once 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Створення змінних строкового типу
$subject = 'MY TEST EMAIL';
echo '============' . "\n";
echo $subject . "\n";
echo '============' . "\n";

$firstName = 'Artur';
$lastName = 'Meleshchenko';
$email = 'a.v.meleshchenko@student.khai.edu';
$orderNumber = '12345';
$orderAmount = '1500.00';
$currentDate = date('Y-m-d H:i:s');

// Створення текстових фрагментів
$text1 = "Ім'я: $firstName" . "\n";
$text2 = "Прізвище: $lastName" . "\n";
$text3 = "Email: $email" . "\n";
$text4 = "Номер замовлення: $orderNumber" . "\n";
$text5 = "Сума замовлення: $orderAmount грн" . "\n";
$text6 = "Дата: $currentDate" . "\n";

// З'єднання змінних
$message = $text1 . $text2 . $text3 . $text4 . $text5;
$message .= $text6;
$message .= "\n" . "Дякуємо за ваше замовлення!" . "\n";
$message .= "З повагою, команда магазину." . "\n";

echo $message;

$mail = new PHPMailer(true);

try {
    // Налаштування SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'a.v.meleshchenko@student.khai.edu';
    $mail->Password   = 'lcqhpbphuqidbdbl';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->CharSet = 'UTF-8';

    // Відправник та отримувач
    $mail->setFrom('a.v.meleshchenko@student.khai.edu', 'Test Sender');
    $mail->addAddress('a.v.meleshchenko@student.khai.edu', 'Test Recipient');

    // Зміст листа
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body    = $message;

    // Відправка
    $mail->send();
    echo "\n" . "=== РЕЗУЛЬТАТ ВІДПРАВКИ ===" . "\n";
    echo "Лист успішно надіслано!" . "\n";

} catch (Exception $e) {
    echo "\n" . "=== ПОМИЛКА ВІДПРАВКИ ===" . "\n";
    echo "Помилка: $mail->ErrorInfo" . "\n";
}
