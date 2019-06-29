<?php
/**
 * [API] サーバ上のデータをリセットする
 * /quiz/gm/api/reset.php
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */

//------------------------------------------------
// ライブラリ
//------------------------------------------------
require_once('util.php');

/** 対象ファイル一覧 */
$DATA = [DATA_ROOM, DATA_ID, DATA_QUESTION, DATA_ANSWER];

//--------------------------------
// ファイルを削除する
//--------------------------------
$error = [];
foreach ($DATA as $file) {
	// ファイルを削除する
	if( is_file($file) ){
		$ret = unlink($file);

		// 失敗した場合はファイル名を配列に入れる
		if( !$ret ){
			$error = array_push($error, $file);
		}
	}
}

//--------------------------------
// 実行結果を返却
//--------------------------------
// 正常終了
if( count($error) === 0 ){
	echo json_encode(['status'=>true]);
}
// エラー時
else{
	echo json_encode(['status'=>false, "failure"=>$error]);
}
