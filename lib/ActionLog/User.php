<?php

class User {
	private $uid, $name, $point;
	public function __construct($userInfo)
	{
		foreach( $userInfo as $key => $val )
		{
			$this->$key = $val;
		}
		$this->al = new ActionLog;
		
		var_dump("construct");
		
		var_dump(get_class_vars("Conf"));
		
		
	}
	
	/* login */
	public function login()
	{
		$this->al->put(
			array(
				'uid' => $this->uid,
				'func' => Conf::ACLOG_F_LOGIN,
				'func_detail' => Conf::ACLOG_FD_LOGIN
			),
			array(
				'entry' => $this->entry
			)
		);
	}

	/* get point */
	public function getPoint($point=0)
	{
		$bonus = floor($point * 0.05);
		$this->point += $point + $bonus;
		$this->al->put(
			array(
				'uid' => $this->uid,
				'func' => Conf::ACLOG_F_POINT,
				'func_detail' => Conf::ACLOG_FD_POINT_GET
			),
			array(
				'total' => $this->point,
				'add' => [$point, $bonus]
			)
		);
	}
	
	/* use point */
	public function usePoint($point=0)
	{
		// success
		if( $this->point < $point ) 
		{
			$this->al->put(
				array(
					'uid' => $this->uid,
					'func' => Conf::ACLOG_F_POINT,
					'func_detail' => Conf::ACLOG_FD_POINT_USE_FAIL
				),
				array(
					'total' => $this->point,
					'use' => $point
				)
			);
		}
		// fail
		else
		{
			$this->point -= $point;
			$this->al->put(
				array(
					'uid' => $this->uid,
					'func' => Conf::ACLOG_F_POINT,
					'func_detail' => Conf::ACLOG_FD_POINT_USE
				),
				array(
					'total' => $this->point,
					'use' => $point
				)
			);
		}
	}
}
