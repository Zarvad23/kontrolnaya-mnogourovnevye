<?php
// главная страница сайта - тут выводим рубрики и объявления по ним
require "db.php";

// смотрим выбрал ли пользователь рубрику через ссылку (index.php?rubrika=2)
$rubrika_vybrana = 0;
if (isset($_GET['rubrika'])) {
    $rubrika_vybrana = (int)$_GET['rubrika'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Доска объявлений</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<h1>Доска объявлений</h1>

<p>
    <a href="dobavit.php?rubrika=<?php echo $rubrika_vybrana; ?>">+ Добавить объявление</a>
</p>

<?php
// выводим сообщение если пришли сюда после нажатия "поднять"
if (isset($_GET['soobshenie'])) {
    echo "<p class='soobshenie'>" . htmlspecialchars($_GET['soobshenie']) . "</p>";
}
?>

<h3>Рубрики:</h3>
<ul class="menu-rubrik">
    <li><a href="index.php">Все рубрики</a></li>
    <?php
    // достаем все рубрики чтобы построить меню сверху
    $zapros_rubrik_menu = mysqli_query($soedinenie, "SELECT * FROM rubriki ORDER BY nazvanie");
    while ($odna_rubrika = mysqli_fetch_assoc($zapros_rubrik_menu)) {
        echo "<li><a href='index.php?rubrika=" . $odna_rubrika['id'] . "'>" . htmlspecialchars($odna_rubrika['nazvanie']) . "</a></li>";
    }
    ?>
</ul>

<hr>

<?php
// заново запрашиваем рубрики - будем по каждой выводить блок с объявлениями
$zapros_rubrik = mysqli_query($soedinenie, "SELECT * FROM rubriki ORDER BY nazvanie");

while ($odna_rubrika = mysqli_fetch_assoc($zapros_rubrik)) {

    $id_tekushey_rubriki = $odna_rubrika['id'];

    // если пользователь выбрал конкретную рубрику - остальные пропускаем
    if ($rubrika_vybrana != 0 && $rubrika_vybrana != $id_tekushey_rubriki) {
        continue;
    }

    echo "<h2>" . htmlspecialchars($odna_rubrika['nazvanie']) . "</h2>";

    // объявления сортируем так, чтобы недавно поднятые или недавно добавленные были сверху
    $sql_obyav = "SELECT * FROM obyavleniya WHERE id_rubriki = $id_tekushey_rubriki ORDER BY COALESCE(data_podnyatiya, data_dobavlen) DESC";
    $zapros_obyav = mysqli_query($soedinenie, $sql_obyav);

    if (mysqli_num_rows($zapros_obyav) == 0) {
        echo "<p><i>В этой рубрике пока нет объявлений</i></p>";
    }

    while ($obyava = mysqli_fetch_assoc($zapros_obyav)) {

        echo "<div class='obyava'>";
        echo "<div class='obyava-zagolovok'>" . htmlspecialchars($obyava['zagolovok']) . "</div>";
        echo "<div>" . nl2br(htmlspecialchars($obyava['opisanie'])) . "</div>";
        echo "<div class='obyava-info'>Автор: " . htmlspecialchars($obyava['avtor']);
        if ($obyava['telefon'] != "") {
            echo ", телефон: " . htmlspecialchars($obyava['telefon']);
        }
        echo "</div>";
        echo "<div class='obyava-info'>Добавлено: " . $obyava['data_dobavlen'] . "</div>";

        // проверяем можно ли поднять это объявление - смотрим когда его поднимали последний раз
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
            echo "<a class='knopka-podnyat' href='podnyat.php?id=" . $obyava['id'] . "'>[ Поднять ]</a>";
        } else {
            echo "<span class='seroe'>Уже поднимали недавно, попробуйте позже</span>";
        }

        echo "</div>";
    }
}
?>

</body>
</html>
