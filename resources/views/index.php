<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Check a check</title>
</head>
<body>

<h1>Брифинг</h1>

<p>Чтобы получить json, необходимо отправить файл post-запросом по ссылке
    <br>
    http://check-parser.test/check/store
    <br>
    содержащий в себе поле check с форматом файла <b>.har</b>.
</p>
<form method="post" action="/check/store" enctype="multipart/form-data">
    <input type="file" name="check">
    <button type="submit">Отправить</button>
</form>

</body>
</html>
