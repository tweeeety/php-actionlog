<?php 
/* require */
require_once("lib/ActionLog/Conf.php");
require_once("lib/ActionLog/ActionLog.php");
require_once("lib/ActionLog/User.php");

/* 
 * call action log only 
 */
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
$testHash = array( "hoge" => "1", "fuga" => "2");


/* 
 * call action log from User Object
 */
/* define user */
$users = array(
	'001' => array('uid' => '001', 'name' => 'hoge', 'entry' => '2014-05-11 10:00:00', 'point'=>0),
	'002' => array('uid' => '002', 'name' => 'fuga', 'entry' => '2014-05-12 10:00:00', 'point'=>0),
	'003' => array('uid' => '003', 'name' => 'piyo', 'entry' => '2014-05-13 10:00:00', 'point'=>0)
);

/* create user */
$user = array();
foreach ( $users as $id => $val )
{
	$user[$id] = new User($val);
}

/* action user */
// 001
$user['001']->login();			// 001 login
$user['001']->getPoint(10);		// 001 get point
$user['001']->getPoint(100);	// 001 get point
$user['001']->getPoint(200);	// 001 get point
$user['001']->usePoint(50);		// 001 use point

// 002
sleep(2);
$user['002']->login();			// 002 login
$user['002']->usePoint(50);		// 002 use point -> fail

// 003
sleep(1);
$user['003']->login();			// 003 login

?>
