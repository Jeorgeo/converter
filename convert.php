<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>
  <body>

    <?php

    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', true);

    require_once __DIR__.'/SimpleXLSX.php';

    $allowedExts = array("xlsx", "xls");
    $arFile = explode(".", $_FILES["file"]["name"]);
    $nameFile = $arFile[0];
    $extensionFile = $arFile[1];

    function convertTable($nameFile) {
      if ( $xlsx = SimpleXLSX::parse($_SERVER["DOCUMENT_ROOT"]."/upload/" . $_FILES["file"]["name"])) {

        // Produce array keys from the array values of 1st array element
        $header_values = $rows = [];

        foreach ( $xlsx->rows() as $k => $r ) {
          if ( $k === 0 ) {
            $header_values = $r;
            continue;
          }
          $rows[] = array_combine( $header_values, $r );
        }

        $file = $_SERVER["DOCUMENT_ROOT"].'/json/' . $nameFile . '.json';

        $result = json_encode($rows);

        if (file_put_contents($file, $result, LOCK_EX)) {
          echo 'Конвертация завершена!';
        } else {
          echo 'Что-то пошло не так!';
        }

      }
    }

if (in_array($extensionFile, $allowedExts))
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Return Code: " . $_FILES["file"]["error"] . "<br>";
    }
    else
    {
        echo "Upload: " . $_FILES["file"]["name"] . "<br>";
        echo "Type: " . $_FILES["file"]["type"] . "<br>";
        echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
        echo "Temp file: " . $_FILES["file"]["tmp_name"] . "<br>";
        echo "name file: " . $nameFile . "<br>";

        if (file_exists($_SERVER["DOCUMENT_ROOT"] . "/upload/" . $_FILES["file"]["name"]))
        {
            echo $_FILES["file"]["name"] . " already exists. ";
            convertTable($nameFile);
        }
        else
        {
            move_uploaded_file($_FILES["file"]["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/upload/" . $_FILES["file"]["name"]);
            convertTable($nameFile);
            echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
        }
    }
}
else
{
    echo "Invalid file<br />";
    print_r($_FILES);
}

// Выводим сообщение пользователю

print "<script language='Javascript'><!--
function reload() {location = \"converter.php\"}; setTimeout('reload()', 3000);
//--></script>

<p>Сообщение отправлено! Подождите, сейчас вы будете перенаправлены на главную страницу...</p>";
exit;

?>

  </body>
</html>
