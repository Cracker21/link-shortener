<?php

class Service
{
	private $pdo;
	private $route;

	function __construct($pdo, $route){
		$this->pdo = $pdo;
		$this->route = $route;
	}
	function createShortLink(){
		//сокращенная ссылка является подстрокой(последние 14 символов) md5-хэша основной ссылки
		$msg = '';
		$link = trim($_POST['link']);
		$hash = substr(md5($link), 18);
		$hashExists = $this->pdo->query("select hash from shLinks where hash = '$hash'");
		if($hashExists->fetch()){
			$msg.= 'Ссылка уже существует:';
		}else{
			$res = $this->pdo->prepare('insert into shLinks values(?, ?)');
			$res->execute([$link, $hash]);
			$msg.= 'Создана новая ссылка:';
		}
			View::show('createdLink', [$msg, $_SERVER['SERVER_NAME'], $hash]);
	}
	function redirect(){
		$shLink = $this->pdo->prepare('select link from shLinks where hash = ?');
		$shLink->execute([$this->route[1]]);
		if($row = $shLink->fetch(PDO::FETCH_ASSOC)){
			$link = $this->addSchemeIfMissing($row['link']);
			http_response_code(301);
			header("Location: $link");
		}else{
			View::show('notFound');
		}
	}
	private function addSchemeIfMissing($link){
		if(preg_match('/^(http|ftp)s?:\/\//', $link)){
			return $link;
		}else{
			return 'http://'.$link;
		}
	}
	function createTable(){
		if($res = $this->pdo->query('create table shLinks(link text, hash varchar(14))')){
			$arr = file('conf.php');
			$arr[6] = '$table_exists = true;';
			file_put_contents('conf.php', implode('', $arr));
		}
	}
}