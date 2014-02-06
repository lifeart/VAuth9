<html>

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<title>Установка VAuth 9 DLE</title>

</head>


<body>

<style>
body {background-color: #333;}
h1 {
margin-left: auto;
color: #fff;
display: block;
}
h2 {
color: rgb(67, 212, 231);
margin-left: 30px;
display: block;
}

h2 a {
color: rgb(223, 73, 73);
}
</style>

<?PHP

include_once('engine/api/api.class.php');

if (!$_GET['do']) {

echo '

	<h1>Вас приветствует мастер установки модуля VAuth</h1>
	<h2>Для установки модуля нажмите <a href="?do=install">сюда</a></h2>


';

} elseif ($_GET['do'] == 'install') {

echo '<h1>Вас приветствует мастер установки модуля VAuth</h1>';
echo '<h2>Добавляю дополнительные поля..</h2>';

$db->query("INSERT INTO " . USERPREFIX . "_admin_sections (name, title, descr, icon, allow_groups) VALUES ('vauth', 'VAuth DLE', 'Модуль авторизации и регистрации пользователей через социальные сети', 'vauth.png', '1') ");

$db->query("ALTER TABLE " . USERPREFIX . "_users ADD	vk_user_id			VARCHAR(30)	NOT NULL");

echo '<h2>Если не вылезло ошибок базы данных, то поля успешно добавлены! Вы можете закрыть эту страницу и удалить её с сайта.</h2>';

header('Location: ?do=finish');

} else {

echo '<h1>Вас приветствует мастер установки модуля VAuth</h1>';
echo '<h2>Поля успешно добавлены в базу, можете удалить этот файл с сайта</h2>';

}
?>


</body>

</html>