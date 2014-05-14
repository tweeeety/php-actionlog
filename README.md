php-actionlog
=============

これはtd-agent(fluentd)で行動ログ的なやつを扱った際のログ出力モジュールサンプルです  
サンプルのため、あまりコードの信憑性はありません。  

##はじめに

本体は`lib/ActionLog/ActionLog.php`で  
actionlog_input.phpが動作確認するためのソースです

ログの制御をこんな感じで制御したいというイメージです

* ①何もしない  
* ②テキストログのみ出力(web側に)  
* ③fluentdでtailする(mongodbまで入れる)  

##導入
