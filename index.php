<?php

include 'data.php';
include 'functions.php';

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    if (!empty($fullname)) {
        if (validateFullname($fullname)) {
            $_SESSION['fullname'] = $fullname;
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } else {
            $_SESSION['error'] = 'Некорректный формат ФИО. Пожалуйста, введите корректное ФИО.';
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    } else {
        $_SESSION['error'] = 'Пожалуйста, введите ФИО.';
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

$fullname = $_SESSION['fullname'] ?? null;
$error = $_SESSION['error'] ?? null;

unset($_SESSION['fullname'], $_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/style.css">
    <title>Random Analyzer</title>
</head>
<body>
    <div class="container">
        <h1>Идеальный подбор пары и другие функции</h1>
        <form method="post">
            <label for="fullname">Введите ваше ФИО:</label>
            <div class="form">
            <input class="input" placeholder="Type your text" required="" type="text" id="fullname" name="fullname">
            <span class="input-border"></span>
            </div>
            <button type="submit"><span class="text">Отправить</span><span>Отправить</span></button>
        </form>

        <?php if ($error): ?>
            <p class="error-message"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <?php if ($fullname): ?>
            <h2>Примеры использования функций</h2>

            <div class="example">
                <h3>Функция getFullnameFromParts</h3>
                <?php
                $parts = getPartsFromFullname($fullname);
                $fullname_reconstructed = getFullnameFromParts($parts['surname'], $parts['name'], $parts['patronymic']);
                ?>
                <p>Полное имя: <?= htmlspecialchars($fullname_reconstructed) ?></p>
            </div>

            <div class="example">
                <h3>Функция getPartsFromFullname</h3>
                <pre><?php print_r($parts); ?></pre>
            </div>

            <div class="example">
                <h3>Функция getShortName</h3>
                <?php $shortname = getShortName($fullname); ?>
                <p>Краткое имя: <?= htmlspecialchars($shortname) ?></p>
            </div>

            <div class="example">
                <h3>Функция getGenderFromName</h3>
                <?php $gender = getGenderFromName($fullname); ?>
                <p>Пол: <?= htmlspecialchars($gender) ?></p>
            </div>

            <div class="example">
                <h3>Определение гендерного состава</h3>
                <?php $gender_description = getGenderDescription($persons_array); ?>
                <pre><?= htmlspecialchars($gender_description) ?></pre>
            </div>

            <div class="example">
                <h3>Идеальный подбор пары</h3>
                <?php $perfect_partner = getPerfectPartnerFromFullname($fullname, $persons_array); ?>
                <p><?= htmlspecialchars($perfect_partner) ?></p>
            </div>
        <?php endif; ?>
    </div>
    <footer>
        © 2024 Random Analyzer
    </footer>
</body>
</html>