<?php
/**
 * クイズ結果取得API
 * /quiz/api/result.php
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
// 引数を受け取る
//------------------------------------------------
$battle_id = getQuery('battle_id');
if( $battle_id === null ){
	putResult(false, 'battle_id is required');
	exit(0);
}

//------------------------------------------------
// 次の対戦のための処理
//------------------------------------------------
// 対戦結果データの保存先
$file = sprintf('data/result/%s', $battle_id);

// すでに対戦結果データがある場合
if( is_file($file) ){
	$data = file_get_contents($file);
	$result = json_decode($data);

}
// 対戦結果データがない、現在の対戦IDと合致
else if( $battle_id === getBattleID() ){
	// 別の場所に対戦結果を保存し直す
	$result = saveResult($file);

	// データを削除
	resetDataFile();
}
// エラー
else{
	putResult(false, 'Unknown battle_id');
	exit(0);
}

//------------------------------------------------
// 結果出力
//------------------------------------------------
putResult(true, $result);


/**
 * 対戦結果データを新規に保存
 *
 * @param [string] $file
 * @return [object]
 */
function saveResult($file){
	// 回答一覧の取得
	$list   = getAnswerUser();
	$result = json_encode($list);		//JSON形式に変換

	// 結果ファイルに記録
	$fp = fopen($file, 'w');
	flock($fp, LOCK_EX);
	fwrite($fp, json_encode($list));
	flock($fp, LOCK_UN);
	fclose($fp);

	return($list);
}

/**
 * 対戦用データファイルをすべて削除
 *
 * @return void
 */
function resetDataFile(){
	$files = [
		  DATA_ROOM
		, DATA_ID
		, DATA_QUESTION
		, DATA_ANSWER
	];

	for( $i=0; $i<count($files); $i++ ){
		unlink($files[$i]);
	}
}