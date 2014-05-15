<?php

final class Conf{
	// LEVEL
	const ACLOG_LEVEL_FOWARD 	= 1;
	const ACLOG_LEVEL_LOCAL 	= 2;
	const ACLOG_LEVEL_IGNORE 	= 3;
	
	// Function
	const ACLOG_F_LOGIN		= 1;
	const ACLOG_F_POINT		= 2;
	const ACLOG_F_PAYMENT 	= 3;
	
	// Function Detail
	const ACLOG_FD_LOGIN			= 1;
	const ACLOG_FD_POINT_GET		= 1;
	const ACLOG_FD_POINT_USE		= 2;
	const ACLOG_FD_POINT_USE_FAIL	= 3;
	const ACLOG_FD_PAYMENT_OK		= 1;
	const ACLOG_FD_PAYMENT_NG		= 2;
	
	// Configuration
	public static $CONF = array(
			'logPath' => '/home/murata/tail-td-agent/aclog',
			'logPrefix' => 'aclog',
			'setting' => array(
				1 => array( 'lv' => self::ACLOG_LEVEL_FOWARD, 'name' => 'login'),
				2 => array( 'lv' => self::ACLOG_LEVEL_FOWARD, 'name' => 'point'),
				3 => array( 'lv' => self::ACLOG_LEVEL_LOCAL, 'name' => 'payment')
			)
	);
}
