<?php
// страница добавления нового объявления
require "db.php";

$oshibka = "";

// рубрика, которую могли передать из ссылки (чтобы сразу ее выбрать в списке)
$rubrika_po_umolchaniyu = 0;
if (isset($_GET['rubrika'])) {
    $rubrika_po_umolchaniyu = (int)$_GET['rubrika'];
}

// если форма была отправлена методом POST - пробуем сохранить объявление
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $zagolovok = trim($_POST['zagolovok']);
    $opisanie = trim($_POST['opisanie']);
    $avtor = trim($_POST['avtor']);
    $telefon = trim($_POST['telefon']);
    $id_rubriki = (int)$_POST['id_rubriki'];

    // проверка что обязательные поля не пустые
    if ($zagolovok == "" || $opisanie == "" || $avtor == "") {
        $oshibka = "Заполните, пожалуйста, заголовок, текст объявления и имя автора";
    } else {

        // экранируем строки перед вставкой в базу, чтобы не было sql-инъекции
        $zagolovok_bezop = mysqli_real_escape_string($soedinenie, $zagolovok);
        $opisanie_bezop = mysqli_real_escape_string($soedinenie, $opisanie);
        $avtor_bezop = mysqli_real_escape_string($soedinenie, $avtor);
        $telefon_bezop = mysqli_real_escape_string($soedinenie, $telefon);

        $sql_dobavit = "INSERT INTO obyavleniya (id_rubriki, zagolovok, opisanie, avtor, telefon, data_dobavlen)
                         VALUES ($id_rubriki, '$zagolovok_bezop', '$opisanie_bezop', '$avtor_bezop', '$telefon_bezop', NOW())";

        mysqli_query($soedinenie, $sql_dobavit);

        // после успешного добавления перекидываем обратно на главную, сразу в нужную рубрику
        header("Location: index.php?rubrika=" . $id_rubriki);
        exit;
    }
}

// достаем список рубрик для выпадающего списка в форме
$zapros_rubrik = mysqli_query($soedinenie, "SELECT * FROM rubriki ORDER BY nazvanie");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Добавить объявление</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

<div class="shapka">
    <h1>Добавить объявление</h1>
</div>

<div class="stranica">

<p><a href="index.php">&laquo; Назад на главную</a></p>

<?php if ($oshibka != "") { ?>
    <p class="oshibka"><?php echo htmlspecialchars($oshibka); ?></p>
<?php } ?>

<form class="forma-dobavit" method="POST" action="dobavit.php">

    <table>
        <tr>
            <td>Рубрика:</td>
            <td>
                <select name="id_rubriki">
                    <?php
                    // выводим все рубрики, если она совпадает с переданной - делаем выбранной
                    mysqli_data_seek($zapros_rubrik, 0);
                    while ($odna_rubrika = mysqli_fetch_assoc($zapros_rubrik)) {
                        $vybran = "";
                        if ($odna_rubrika['id'] == $rubrika_po_umolchaniyu) {
                            $vybran = "selected";
                        }
                        echo "<option value='" . $odna_rubrika['id'] . "' " . $vybran . ">" . htmlspecialchars($odna_rubrika['nazvanie']) . "</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>Заголовок:</td>
            <td><input type="text" name="zagolovok" size="40" value="<?php echo isset($_POST['zagolovok']) ? htmlspecialchars($_POST['zagolovok']) : ''; ?>"></td>
        </tr>
        <tr>
            <td valign="top">Текст объявления:</td>
            <td><textarea name="opisanie" rows="5" cols="40"><?php echo isset($_POST['opisanie']) ? htmlspecialchars($_POST['opisanie']) : ''; ?></textarea></td>
        </tr>
        <tr>
            <td>Ваше имя:</td>
            <td><input type="text" name="avtor" size="40" value="<?php echo isset($_POST['avtor']) ? htmlspecialchars($_POST['avtor']) : ''; ?>"></td>
        </tr>
        <tr>
            <td>Телефон:</td>
            <td><input type="text" name="telefon" size="40" value="<?php echo isset($_POST['telefon']) ? htmlspecialchars($_POST['telefon']) : ''; ?>"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Разместить объявление"></td>
        </tr>
    </table>

</form>

</div>

</body>
</html>
