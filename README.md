# オンラインクイズゲーム
## What is this
日本工学院八王子専門学校 ゲーム科4年生向けのPHP実習のサンプルです。

## Quick Start
### Install
PHPが動作するWebサーバのドキュメントルート下にcloneするか、ダウンロードしたファイルを設置してください。
```
$ cd /var/wwww/html
$ git clone https://github.com/katsube/neec-quiz quiz
```

データファイルを格納するディレクトリに実行権(パーミッション)の設定を行います。
```
$ chmod 0777 /var/wwww/html/quiz/api/data
$ chmod 0777 /var/wwww/html/quiz/api/data/result
```

### How to use
通常プレイは`/quiz/index.html`へアクセスします。
```
http://example.com/quiz/
```

デバッグ用のツールも用意されています。以下のURLのへアクセスします。
```
http://example.com/quiz/gm/
```

※`example.com`は自分のドメインに置き換えてください。


## Known bugs

1. APIは予め定めらた順番通りに呼び出される必要があります。順番を守っていない場合はのエラー処理は含めていません。
1. XSSなどのセキュリティホールが存在します。
    * 後日修正予定
1. デバッグツールはURLがわかれば誰でもアクセスできてしまいます。本来はアクセス制限を設けます。
    * 後日機能追加予定


