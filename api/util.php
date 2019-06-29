<?php
/**
 * 共通機能用ライブラリ
 *
 * @version 1.0.0
 * @author M.katsube <katsubemakito@gmail.com>
 * @copyright 2019 M.katsube
 */

//-------------------------------------------------------------------
// 定数
//-------------------------------------------------------------------
// 同時プレイ人数
define('MAX_PLAYER', 3);

// データファイル
define('DATA_ROOM',     'data/room.txt');			// 参加者の一覧
define('DATA_ID',       'data/roomid.txt');			// 対戦ID
define('DATA_QUESTION', 'data/roomquestion.txt');	// 出題
define('DATA_ANSWER',   'data/roomanswer.txt');		// プレイヤーの回答一覧

// $question配列の添字
define('COL_Q', 0);		// 問題
define('COL_A', 1);		// 回答

//-------------------------------------------------------------------
// グローバル変数
//-------------------------------------------------------------------
// クイズの問題一覧
$question = [
	  ['上は洪水、下は大火事といえば？', '風呂']
	, ['パンはパンでも堅いパンは？', 'フライパン']
	, ['島根県の県庁所在地は？「○○市」', '松江']
	, ['東京都庁があるのは？「東京都○○区」', '新宿']
	, ['イカを数える際の単位は？', '杯']
	, ['うさぎを数える際の単位は？', '羽']
	, ['お寿司屋さんで「あがり」はお茶を意味しますが、「むらさき」は？', '醤油']
	, ['日本のお金の単位が「円」に初めて変わった際の元号は？', '明治']
	, ['現在の日本で2番目に標高が高い山は？', '北岳']
	, ['「白鳥の湖」を作曲したのは？', 'チャイコフスキー']
];


//-------------------------------------------------------------------
// 共通関数
//-------------------------------------------------------------------
// - countUser()
// - getUserList()
// - existUser($uid)
// - getBattleID()
// - getQuestion()
// - getAnswer($quiz_num)
// - getAnswerUser()
// - putResult($status, $data=null)
// - getQuery($name, $empty=null)
// - saveFile($path, $value, $mode='a')
// - debugQueryPrint()
//-------------------------------------------------------------------

/**
 * 参加中のユーザー数を返却
 *
 * @return [integer]
 */
function countUser(){
	// ファイルがまだ存在しない場合は0
	if( ! is_file(DATA_ROOM) ){
		return(0);
	}

	// ファイルが存在している場合はカウント
	$fp = fopen(DATA_ROOM, 'r');
	$count = 0;
	while( fgets($fp) !== false ){
		$count++;
	}
	fclose($fp);
	return($count);
}

/**
 * 参加中のユーザー一覧を返却
 *
 * @return [object]
 */
function getUserList(){
	$member = [];		// 配列として初期化

	// ファイルから取得
	$fp = fopen(DATA_ROOM, 'r');
	flock($fp, LOCK_SH);
	while( ($buff = fgets($fp)) !== false ){
		list($uid, $uname) = explode("\t", $buff);
		$member[$uid] = trim($uname);		// trime()で改行コードを削除
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	return($member);
}

/**
 * 指定ユーザーIDが参加中か調べる
 *
 * @param [string] $uid ユーザーID
 * @return [boolean]
 */
function existUser($uid){
	$member = getUserList();
	return( array_key_exists($uid, $member) );
}

/**
 * 対戦IDを取得する
 *
 * @return [string]
 */
function getBattleID(){
	$battle_id = null;

	//-------------------------------------
	// データファイルが存在していれば
	//-------------------------------------
	if( is_file(DATA_ID) ){
		// データファイルから内容を取得
		$fp = fopen(DATA_ID, 'r');	// 読み取りモード
		flock($fp, LOCK_SH);				// ファイルロック
		$battle_id = fgets($fp);
		flock($fp, LOCK_UN);				// ロック解除
		fclose($fp);
	}
	//-------------------------------------
	// データファイルがまだ無ければ
	//-------------------------------------
	else{
		$battle_id = uniqid("BID");		// IDを作成

		// データファイルを作成し記録
		$fp = fopen(DATA_ID, 'w');		// 書き込みモード
		flock($fp, LOCK_EX);			// ファイルロック
		fwrite($fp, $battle_id);
		flock($fp, LOCK_UN);			// ロック解除
		fclose($fp);
	}

	return($battle_id);
}


/**
 * 問題を取得する
 *
 * @return [integer] クイズの出題番号
 */
function getQuestion(){
	$quiz_num = null;

	//-------------------------------------
	// データファイルが存在していれば
	//-------------------------------------
	if( is_file(DATA_QUESTION) ){
		// データファイルから内容を取得
		$fp = fopen(DATA_QUESTION, 'r');	// 読み取りモード
		flock($fp, LOCK_SH);				// ファイルロック
		$quiz_num = fgets($fp);
		flock($fp, LOCK_UN);				// ロック解除
		fclose($fp);
	}
	//-------------------------------------
	// データファイルがまだ無ければ
	//-------------------------------------
	else{
		global $question;
		$quiz_num = rand(0, count($question)-1);	// 問題を決定

		// データファイルを作成し記録
		$fp = fopen(DATA_QUESTION, 'w');	// 書き込みモード
		flock($fp, LOCK_EX);				// ファイルロック
		fwrite($fp, $quiz_num);
		flock($fp, LOCK_UN);				// ロック解除
		fclose($fp);
	}

	return( (integer) $quiz_num );
}

/**
 * クイズの回答を返却
 *
 * @param [integer] $quiz_num
 * @return [string]
 */
function getAnswer($quiz_num=null){
	global $question;
	if($quiz_num === null){
		$quiz_num = getQuestion();
	}

	return(
		$question[$quiz_num][COL_A]
	);
}

/**
 * クイズの回答一覧を返却
 *
 * @return void
 */
function getAnswerUser(){
	$member = getUserList();
	$list = [];

	$fp = fopen(DATA_ANSWER, 'r');
	flock($fp, LOCK_SH);
	while( ($buff = fgets($fp)) !== false ){
		list($uid, $answer, $correct) = explode("\t", trim($buff));
		$list[$uid] = [
			  'answer'  => $answer
			, 'uname'   => $member[$uid]
			, 'correct' => ($correct === '1')?  true:false
		];
	}
	flock($fp, LOCK_UN);
	fclose($fp);

	return($list);
}


/**
 * 最終的な結果をJSON出力
 *
 * @param [boolean] $status 成功ならfrue, エラーならfalse
 * @param [mixed]   $value  クライアントへ返却したい値
 * @return void
 */
function putResult($status, $data=null){
	$value = [
		'status' => $status,
		'value'  => $data
	];

	// デバッグ用出力
	if( isset($_GET['_debug']) && $_GET['_debug'] === "on" ){
		header('Content-type: text/plain');
		echo json_encode($value, JSON_PRETTY_PRINT);	// 人間が見やすい形で出力
		debugQueryPrint();								// クエリーを表示
	}
	// 本番用出力
	else{
		header('Content-type: application/json');
		echo json_encode($value);
	}
}

/**
 * クエリーを取得する
 *
 * @param [string] $name  クエリー名
 * @param [mixed]  $empty クエリーが渡されなかった場合の値
 * @return mixed
 */
function getQuery($name, $empty=null){
	// クエリーが渡された場合
	if( isset($_GET[$name]) && !empty($_GET[$name]) ){
		return($_GET[$name]);
	}
	// クエリーが渡されなかった場合
	else{
		return($empty);
	}
}


/**
 * ファイルに書き込む(flock)
 *
 * @param [string] $path  ファイルのパス
 * @param [mixed]  $value ファイルへ記録したい文字列
 * @param [string] $mode  ファイルを開くモード(未指定時は'a')
 * @return void
 */
function saveFile($path, $value, $mode='a'){
	$fp = fopen($path, $mode);	// ファイルを開く
	flock($fp, LOCK_EX);		// ロック開始
	fwrite($fp, $value);		// 書き込み
	flock($fp, LOCK_UN);		// ロック解除
	fclose($fp);				// ファイルを閉じる
}

/**
 * 全クエリー表示（デバッグ用）
 *
 * @return void
 */
function debugQueryPrint(){
	printf("\n\n");
	printf("----------------------\n");
	printf(' $_GET'."\n");
	printf("----------------------\n");
	print_r($_GET);

	printf("\n\n");
	printf("----------------------\n");
	printf(' $_COOKIE'."\n");
	printf("----------------------\n");
	print_r($_COOKIE);
}