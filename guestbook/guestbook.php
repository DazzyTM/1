<?php
session_start();

function renderComments() {
    $fileName = 'comments.csv';

    if (file_exists($fileName)) {
        $fileStream = fopen($fileName, "r");
        $comments = [];

        while (!feof($fileStream)) {
            $jsonString = fgets($fileStream);
            $comment = json_decode($jsonString, true);
            if (!empty($comment)) {
                $comments[] = $comment;
            }
        }
        fclose($fileStream);

        $comments = array_reverse($comments);

        foreach ($comments as $comment) {
            echo '<div class="card mb-3">';
            echo '<div class="card-header">';
            echo '<strong>' . htmlspecialchars($comment['name']) . '</strong>';
            echo ' <small class="text-muted">(' . htmlspecialchars($comment['email']) . ')</small>';
            if (isset($comment['date'])) {
                echo ' <small class="text-muted float-end">' . $comment['date'] . '</small>';
            }
            echo '</div>';
            echo '<div class="card-body">';
            echo '<p>' . nl2br(htmlspecialchars($comment['text'])) . '</p>';
            echo '</div>';
            echo '</div>';
        }
    } else {
        echo '<p class="text-muted">Поки що немає коментарів. Будьте першим!</p>';
    }
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email']) && isset($_POST['name']) && isset($_POST['text'])) {
        $email = trim($_POST['email']);
        $name = trim($_POST['name']);
        $text = trim($_POST['text']);

        $errors = [];

        if (empty($email)) {
            $errors[] = 'Email обов\'язковий для заповнення';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Некоректний формат email';
        }

        if (empty($name)) {
            $errors[] = 'Ім\'я обов\'язкове для заповнення';
        } elseif (strlen($name) < 2) {
            $errors[] = 'Ім\'я має містити мінімум 2 символи';
        }

        if (empty($text)) {
            $errors[] = 'Текст коментаря обов\'язковий для заповнення';
        } elseif (strlen($text) < 10) {
            $errors[] = 'Коментар має містити мінімум 10 символів';
        }

        if (empty($errors)) {
            $commentData = [
                'email' => $email,
                'name' => $name,
                'text' => $text,
                'date' => date('Y-m-d H:i:s')
            ];

            $jsonString = json_encode($commentData, JSON_UNESCAPED_UNICODE);

            $fileName = 'comments.csv';
            $fileStream = fopen($fileName, 'a');
            fwrite($fileStream, $jsonString . "\n");
            fclose($fileStream);

            $message = 'Коментар успішно додано!';
            $messageType = 'success';

            $_POST = [];
        } else {
            $message = implode('<br>', $errors);
            $messageType = 'danger';
        }
    } else {
        $message = 'Всі поля обов\'язкові для заповнення';
        $messageType = 'danger';
    }
}

?>
<!DOCTYPE html>
<html lang="">
<?php require_once 'sectionHead.php'?>
<body>
<div class="container">
    <!-- navbar menu -->
    <?php require_once 'sectionNavbar.php'?>
    <br>

    <!-- Повідомлення -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- guestbook section -->
    <div class="card card-primary">
        <div class="card-header bg-primary text-light">
            <i class="fas fa-book"></i> Форма гостьової книги
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-8">
                    <!-- Форма гостьової книги -->
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email адреса
                            </label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                                   required
                                   placeholder="Введіть ваш email">
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Ім'я
                            </label>
                            <input type="text"
                                   class="form-control"
                                   id="name"
                                   name="name"
                                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>"
                                   required
                                   placeholder="Введіть ваше ім'я">
                        </div>

                        <div class="mb-3">
                            <label for="text" class="form-label">
                                <i class="fas fa-comment"></i> Коментар
                            </label>
                            <textarea class="form-control"
                                      id="text"
                                      name="text"
                                      rows="4"
                                      required
                                      placeholder="Залиште ваш коментар..."><?php echo isset($_POST['text']) ? htmlspecialchars($_POST['text']) : ''; ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Відправити коментар
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Секція коментарів -->
    <div class="card card-primary">
        <div class="card-header bg-body-secondary text-dark">
            <i class="fas fa-comments"></i> Коментарі
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-sm-12">
                    <!-- Відображення коментарів -->
                    <?php renderComments(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>