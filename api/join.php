<?php
/**
 * ユーザー登録API
 * /quiz/api/join.php
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
// クエリーの受け取り
//------------------------------------------------
// ユーザー名
$uname = getQuery('uname');
if( $uname === null ){
	putResult(false, 'uname is required');	// 名前未入力エラー
	exit(0);
}

//------------------------------------------------
// プレイ状況のチェック
//------------------------------------------------
// 入室者数のチェック
$count = countUser();
if( $count >= MAX_PLAYER ){
	putResult(false, 'This room is full');  // 満員エラー
	exit(0);
}

//------------------------------------------------
// 参加処理
//------------------------------------------------
// ユーザーIDの作成
$uid = uniqid();

// ファイルへ書き込む
$str = implode("\t", [$uid, $uname]) ."\n";  // $uid<タブ>$uname<改行>
saveFile(DATA_ROOM, $str);

// レスポンス
putResult(true, ['uid'=> $uid]);
