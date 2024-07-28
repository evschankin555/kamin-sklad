<?

function isJson($string) {
    return ((is_string($string) &&
            (is_object(json_decode($string)) ||
            is_array(json_decode($string))))) ? true : false;
};

if ($_POST['type'] == 'registration') {
	$result = '';
	unset($_POST['type'], $_POST['step']);
	$Query = http_build_query($_POST) . "\n";
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
			'content' => $Query,
		),
	));
	$result = file_get_contents(
		$file = "http://leadback.ru/backend/bitrix_reg.php",
		$use_include_path = false,
		$context
	);
	if (isJson($result) == true) echo $result;
}
if ($_POST['type'] == 'authorization') {
	$result = '';
	unset($_POST['type'], $_POST['step']);
	$Query = http_build_query($_POST) . "\n";
	$context = stream_context_create(array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-Type: application/x-www-form-urlencoded' . PHP_EOL,
			'content' => $Query,
		),
	));
	$result = file_get_contents(
		$file = "http://leadback.ru/backend/bitrix_auth.php",
		$use_include_path = false,
		$context
	);
	if (isJson($result) == true) echo $result;
}