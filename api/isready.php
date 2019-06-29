<?php
/**
 * 満員確認API
 * /quiz/api/isready.php
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
// 登録者数が満員か確認する
//------------------------------------------------
$count = countUser();
putResult(true, [
	  'ready' => ($count === MAX_PLAYER)
	, 'count' => $count
]);
