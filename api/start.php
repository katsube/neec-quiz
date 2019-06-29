<?php
/**
 * クイズ情報取得API
 * /quiz/api/start.php
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */

//------------------------------------------------
// ライブラリ
//------------------------------------------------
require_once('util.php');
global $question;			// util.php内の変数を利用する

//------------------------------------------------
// 対戦開始に必要な情報を集める
//------------------------------------------------
// 対戦IDを取得
$battle_id = getBattleID();

// 出題番号を取得
$quiz_num = getQuestion();

// 参加者の一覧を取得
$member = getUserList();

//------------------------------------------------
// 出力
//------------------------------------------------
putResult(true, [
	  'battle_id' => $battle_id
	, 'member' => $member
	, 'quiz'   => $question[$quiz_num][COL_Q]
]);