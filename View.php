<?php

class View
{	//разделил бы по файлам, если б было больше кода
	private static $pages = [
		'main' => '<form method="POST" action="cut">
						<input name="link" placeholder="Введите ссылку, которую хотите сократить">
						<input type="submit" value="Сократить">
					</form>',
		'createdLink' => "%0%<p id='path'>%1%/%2%</p><button onclick='copy()'>Скопировать</button>",
		'notFound' => "Ссылка не найдена",
	];
	/*
		Метод show берёт html из массива $pages и помещает его в template
	*/
	static function show($page, $data = []){
		$html = self::$pages[$page];

		//цикл раскидывает переменные в разметке
		for($i=0;$i<count($data);$i++){
			$html = str_replace("%$i%", $data[$i], $html);
		}
		require 'template.php';
	}
}