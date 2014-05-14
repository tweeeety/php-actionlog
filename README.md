php-actionlog
=============

これはtd-agent(fluentd)で行動ログ的なやつを扱った際のログ出力モジュールサンプルです  
サンプルのためあまりコードのキレイさや信憑性はありません。  

READMEもささっと書いて適当なので時間があったらソースともども整理します...汗

##はじめに

本体は`lib/ActionLog/ActionLog.php`で  
actionlog_input.phpが動作確認するためのソースです

ログの制御をこんな感じで制御したいというイメージです

* ①何もしない  
* ②テキストログのみ出力(web側に)  
* ③fluentdでtailする(mongodbまで入れる)  

##導入
```php
require_once("lib/ActionLog/ActionLog.php");
```

##使い方
###呼び出し
メインはこんな感じです
```php
require_once("lib/ActionLog/ActionLog.php");
$al = new ActionLog(Conf::$CONF);
$al->put(
	array(
		'uid' => 'test',
		'func' => Conf::ACLOG_F_LOGIN,
		'func_detail' => Conf::ACLOG_FD_LOGIN
	),
	array(
		'entry' => '1999-01-01 10:00:00'
	)
);
```
###結果
こんな感じのjsonを出力します
```
{"info":{"uid":"test","func":1,"func_detail":1,"rid":"10f3d8d9ae622c1be4a56153bf6700ea","pid":28760,"hn":"is1.paq.to","ts":"2014-05-14 16:33:54"},"val":{"entry":"1999-01-01 10:00:00"}}
```

##説明

###config  

`lib/ActionLog/Conf.php`もサンプルのconfigです。  
どこにどんな形で定義してあっても良いのでこんな感じでインスタンス生成時に渡します
```php
$al = new ActionLog(Conf::$CONF);
```

###ログ出しわけ  

Conf::$Conf['setting']に設定した値に応じてログを出しわけします  
Conf::$Conf['setting']のkeyが、putする際に第一引数で渡している'func'の値に対応します  
上記の使い方例では  
`'func' => Conf::ACLOG_F_LOGIN,`を指定しているので  
Conf::$Conf['setting']['1']の`array( 'lv' => self::ACLOG_LEVEL_LOCAL, 'name' => 'login')`になります

この場合下記の情報を元にログファイルを出力します  

>ログレベル：self::ACLOG_LEVEL_FOWARD  
>ファイル名：action.login.YYYYMMDD  

###ログレベル  

fluentdのin_tailプラグインのpathに該当ログへのシンボリックリンクを指定することが前提です(後述※1)  
詳細は[こちら(7.シンボリックリンクでの…トコ)](http://tweeeety.hateblo.jp/entry/20131213/1386899221)

>ACLOG_LEVEL_FOWARD：受信側までログを転送  
>ACLOG_LEVEL_LOCAL：ローカル(webサーバ)にのみテキスト出力※1  
>ACLOG_LEVEL_IGNORE：なにもしない  

###※1.ACLOG_LEVEL_LOCAL：ローカルの指定でローカルのみのとどまる仕組み

* 出力されるログ  

aclog.login.2014-05-14  

* tailするログ(tailに指定しているpath)  

ln -s aclog.login.2014-05-14 aclog.login.log 
の`aclog.login.log`  

* cronで毎日0時0分にこんな感じのshをしかけている

var='action'
ln -nfs ${ac_log_dir}/aclog.${var}.`date "+%Y-%m-%d"` ${slink_dir}/aclog.${var}.log






