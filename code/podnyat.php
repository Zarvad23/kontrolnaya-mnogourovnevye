<?php
// этот файл обрабатывает нажатие кнопки "поднять" у объявления
// поднимать одно и то же объявление можно не чаще раза в сутки
require "db.php";

$id_obyavleniya = (int)$_GET['id'];

// находим само объявление
$zapros = mysqli_query($soedinenie, "SELECT * FROM obyavleniya WHERE id = $id_obyavleniya");
$obyava = mysqli_fetch_assoc($zapros);

// если такого объявления нет - просто останавливаемся
if (!$obyava) {
    die("Такого объявления не существует");
}

// проверяем когда его поднимали в последний раз
$mozhno_podnyat = true;
if ($obyava['data_podnyatiya'] !== null) {
    $vremya_podnyatiya = strtotime($obyava['data_podnyatiya']);
    $seychas = time();
    $raznica_v_chasah = ($seychas - $vremya_podnyatiya) / 3600;
    if ($raznica_v_chasah < 24) {
        $mozhno_podnyat = false;
    }
}

if ($mozhno_podnyat) {
    // ставим текущее время поднятия
    mysqli_query($soedinenie, "UPDATE obyavleniya SET data_podnyatiya = NOW() WHERE id = $id_obyavleniya");
    $soobshenie = "Объявление поднято наверх списка!";
} else {
    $soobshenie = "Это объявление уже поднимали меньше суток назад, попробуйте позже";
}

// возвращаемся на главную, в ту же рубрику откуда пришли, и показываем сообщение
header("Location: index.php?rubrika=" . $obyava['id_rubriki'] . "&soobshenie=" . urlencode($soobshenie));
exit;
?>
