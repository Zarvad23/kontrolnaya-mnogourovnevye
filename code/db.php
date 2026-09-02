<?php
// этот файл просто подключается к базе данных
// его подключаем в начале других php файлов через require

$server = "localhost";
$polzovatel_bd = "doska_user";
$parol_bd = "doska_pass";
$imya_bazy = "doska_obyav";

// пытаемся соединиться с базой через mysqli
$soedinenie = mysqli_connect($server, $polzovatel_bd, $parol_bd, $imya_bazy);

// если не получилось - останавливаем скрипт и пишем ошибку
if (!$soedinenie) {
    die("Не получилось подключиться к базе данных: " . mysqli_connect_error());
}

// говорим базе что будем работать в кодировке utf8, чтобы русские буквы не превращались в кракозябры
mysqli_query($soedinenie, "SET NAMES 'utf8mb4'");
?>
