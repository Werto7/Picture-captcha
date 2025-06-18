<?php
session_start();
$correct = $_SESSION['captcha_wrong'] ?? [];
$user = array_map('intval', $_POST['selected'] ?? []);

sort($correct);
sort($user);

if ($user === $correct) {
    unset($_SESSION['captcha_wrong']);
    $_SESSION['captcha_passed'] = true;
    header('Location: index.php');
    exit;
} else {
    unset($_SESSION['captcha_wrong']);
    $_SESSION['wrong_solved'] = true;
    header('Location: captcha.php');
}
?>