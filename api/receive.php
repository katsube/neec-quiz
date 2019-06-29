<?php
/**
 * クイズ回答API
 * /quiz/api/receive.php
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
// 引数を取得
//------------------------------------------------
$answer = getQuery('answer');
$uid    = getQuery('uid');

//---------------------
// Validation
//---------------------
// 回答が未入力
if( $answer === null ){
	putResult(false, 'answer is required'); return(false);
}
// ユーザーIDが未入力
if( $uid === null ){
	putResult(false, 'uid is required'); return(false);
}
// ユーザーIDが存在しない
if( ! existUser($uid) ){
	putResult(false, 'uid is undefine id'); return(false);
}

//------------------------------------------------
// 回答を記録
//------------------------------------------------
$ret = setAnswer($uid, $answer);

//------------------------------------------------
// 結果出力
//------------------------------------------------
putResult([
  'status' => $ret
]);


/**
 * 回答を記録する
 *
 * @param [string]   $uid    ユーザーID
 * @param [string]   $answer 回答
 * @return [boolean]
 */
function setAnswer( $uid, $answer ){
	// 正解を取得
	$answer_correct = getAnswer();

	// 保存する
	$fp = fopen(DATA_ANSWER, 'a');
	if( ! $fp  ){
		return(false);
	}
	flock($fp, LOCK_EX);
	fwrite($fp, implode("\t", [
					  $uid
					 , $answer
					, (($answer===$answer_correct)?  1:0)
	]));
	fwrite($fp,"\n");
	flock($fp, LOCK_UN);
	fclose($fp);

	return(true);
}