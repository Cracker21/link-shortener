<?php
error_reporting(E_ALL);

try{
	require 'View.php';	
	require 'Service.php';
	require 'conf.php';

	$route = explode('/',$_SERVER['REQUEST_URI']);

	if($_SERVER['REQUEST_URI'] == '/'){
		View::show('main');
	}else if($route[1] == 'scr'){
		require 'script.js';
	}else{
		//пути, которые требуют бд
		$pdo = new \PDO('pgsql:host=localhost;dbname='.$DB_NAME, $DB_USER, $DB_PASS,[PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

		$service = new Service($pdo, $route);
		//создает таблицу
		if(!$table_exists){
			$service->createTable();
		}
		if($route[1]=='cut'&&isset($_POST['link'])){
			$service->createShortLink($DOMAIN);
		}else{
			$service->redirect();
		}
	}
//логирование
}catch(Throwable $e){
		$msg = sprintf("---%s--- Error(%d): %s in %s at %d\n",strftime('%c'), $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
		file_put_contents($_SERVER['DOCUMENT_ROOT'].'/log', $msg, FILE_APPEND);
		echo 'Произошла ошибка. Детали в логе.';
}
