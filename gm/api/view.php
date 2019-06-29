<?php
/**
 * [API] サーバ上のデータを返却する
 * /quiz/gm/api/view.php
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */

//------------------------------------------------
// ライブラリ
//------------------------------------------------
require_once('util.php');

//------------------------------------------------
// 引数の受け取り
//------------------------------------------------
$type = isset($_GET['type'])?  $_GET['type']:null;

//------------------------------------------------
// ファイルのパスを決定
//------------------------------------------------
switch($type){
	case 'room':
		$file = DATA_ROOM;
		break;
	case 'id':
		$file = DATA_ID;
		break;
	case 'question':
		$file = DATA_QUESTION;
		break;
	case 'answer':
		$file = DATA_ANSWER;
		break;
	default:
		$file = null;
		break;
}

//------------------------------------------------
// 返却
//------------------------------------------------
// 未定義のファイル
if( $file === null ){
	header("Content-type: text/html");
	print "<h1>404 File Not Found</h1>";
}
// ファイル未作成
else if( ! is_file($file) ){
	header("Content-type: text/html");
	print "※ファイルはまだ作成されていません";
}
// ファイルの内容返却
else{
	header("Content-type: text/plain");
	echo file_get_contents($file);
}