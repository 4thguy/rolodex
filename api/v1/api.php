<?php

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
	$_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
	$requestArray = explode('/', rtrim($_REQUEST['request'], '/'));
	$objectType = strtolower(array_shift($requestArray));
	switch ($objectType) {
		case 'user': {
			include_once('class.User.php');
			$API = new User($requestArray, $_SERVER['HTTP_ORIGIN']);
			break;
		}
		default: {
			throw new Exception();
		}
	}
	echo $API->processAPI();
} catch (Exception $e) {
	echo json_encode(Array('error' => $e->getMessage()));
}

?>
