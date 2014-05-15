php-actionlog
=============
このモジュールはaccessログよりフレキシブルなjsonログ出力を目的とした  
td-agent(fluentd)の使用を前提とするphpモジュールサンプルです。  

## 補足
個人的にこんな感じでやりましたというイメージを伝えるためのものであり  
サンプルのため、あまりコードのキレイさや信憑性はありません。  

READMEもささっと書いて適当なので時間があったらソースともども整理します...汗

##ログ出力イメージ

###よくあるこんな流れのログ出力を目的としています  

①webサーバのphpアプリケーションから任意の場所にjsonフォーマットでログを出力し  
③td-agentでin_tailして受信サーバにout_forward  、  
③受信サーバのtd-agentでin_forwardで受け取り、テキストやmongodbに蓄積  

このモジュールは①の際にアプリケーションから呼ばれます  
ログの制御をこんな感じで制御したいというのが主な目的です
* 1.何もしない  
* 2.テキストログのみ出力(web側に)  
* 3.fluentdでtailする(mongodbまで入れる)  

##使い方
###導入
```php
require_once("lib/ActionLog/ActionLog.php");
```

###呼び出し
```php
require_once("lib/ActionLog/Conf.php");
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

####config例
```
// Configuration
public static $CONF = array(
	'logPath' => '/home/murata/tail-td-agent/aclog',
	'logPrefix' => 'aclog',
	'setting' => array(
		1 => array( 'lv' => self::ACLOG_LEVEL_LOCAL, 'name' => 'login'),
		2 => array( 'lv' => self::ACLOG_LEVEL_FOWARD, 'name' => 'point'),
		3 => array( 'lv' => self::ACLOG_LEVEL_LOCAL, 'name' => 'payment')
	)
);
```

###結果
こんな感じのjsonを出力します
```
# ls
aclog.login.2014-05-14

# cat aclog.login.2014-05-14
{"info":{"uid":"test","func":1,"func_detail":1,"rid":"10f3d8d9ae622c1be4a56153bf6700ea","pid":28760,"hn":"is1.paq.to","ts":"2014-05-14 16:33:54"},"val":{"entry":"1999-01-01 10:00:00"}}
```

###流れ～結果について軽く説明
* putで`'func' => Conf::ACLOG_F_LOGIN,`という指定
* Conf::$Conf['setting']['1']を参照
* 値の`array( 'lv' => self::ACLOG_LEVEL_LOCAL, 'name' => 'login')`により下記のログを出力
    * name=loginより、出力するファイル名は`actionlog.login.2014-0514_forward
    * lv=ACLOG_LEVEL_LOCALより、ローカルに出力するだけ


##説明

###config  

`lib/ActionLog/Conf.php`はサンプルのconfigです。   
定義の場所や形はなんでもよいので、インスタンス生成時にarrayの形で渡します  
```php
$al = new ActionLog(Conf::$CONF);
```

###ログ出しわけ  

Conf::$Conf['setting']の形で設定した値に応じてログを出しわけします  
>putする際に第一引数で渡している'func'の値がConf::$Conf['setting']のkeyに対応するイメージ  

###ログレベル  

Conf::$Conf['setting']に指定したlvに応じて下記を出しわけます

>ACLOG_LEVEL_IGNORE：なにもしない  
>ACLOG_LEVEL_LOCAL：ローカル(webサーバ)にのみテキスト出力※1  
>ACLOG_LEVEL_FOWARD：受信側までログを転送  


###※1.について
ACLOG_LEVEL_LOCAL指定でローカル出力のみになる仕組みについて補足です

####例
* 出力されるログ  
aclog.login.2014-05-14  

* tailするログ(tailに指定しているpath)  
ln -s aclog.login.2014-05-14 aclog.login.log   
の`aclog.login.log`  

* cronで毎日0時0分にこんな感じのshをしかけている
```
var='action'
ln -nfs ${ac_log_dir}/aclog.${var}.`date "+%Y-%m-%d"` ${slink_dir}/aclog.${var}.log
```

ってことで、ACLOG_LEVEL_LOCALを指定すると  
ファイル名が`aclog.login.2014-05-14_noforward`となりtailするシンボリックリンクから外れる  
といった強引な手を使ってます






