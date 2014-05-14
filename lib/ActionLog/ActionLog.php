<?php
/**
* ActionLog Class
* @package		tweeeety
* @subpackage	Test
* @author 		tweeeety
* @since 		PHP 5.4.14
* @version 		1.0.0
*/
class ActionLog {
	private $logfp, $rid, $info;
	private $_CONF = array();
	
	// LEVEL
	private $ACLOG_LEVEL_FOWARD		= 1;
	private $ACLOG_LEVEL_LOCAL		= 2;
	private $ACLOG_LEVEL_IGNORE		= 3;

	/**
	 * constructor
	**/
	public function __construct( $conf = NULL ) 
	{
		if( is_array($conf) ) { 
			$this->setConfigs($conf);
		}
		$this->logfp = array();
		$this->info['rid'] = substr( md5( uniqid( mt_rand(), true ) ), 0, 32 );	
		$this->pid = getmypid();
		$this->info['pid'] = $this->pid;
		$this->info['hn'] = gethostname();
	}
	
	/* set configure */
	public function setConfig($key, $val)
	{
		$this->_CONF[$key] = $val;
	}
	public function setConfigs($conf=array())
	{
		foreach( $conf as $key => $val ) {
			$this->setConfig($key, $val);
		}
	}
	
	/* get configure */
	public function getConfig($key=NULL)
	{
		return is_null($key) ? $this->_CONF : ( isset($this->_CONF[$key] ) ? $this->_CONF[$key] : NULL );
	}

	/* set log level */
	public function setLevel($levelName='', $level=1)
	{
		if( property_exists('ActionLog', $levelName) === false ) return;
		$this->$levelName = $level;
	}
	
	/* get log level */
	public function getLevel($levelName='')
	{
		return property_exists('ActionLog', $levelName) ? $this->$levelName : NULL;
	}
	
	/**
	 * put log data
	 * 
	 * @param	array	$info	log information header
	 * @param	array	$val	log items
	 * @param	int		level	log storage level
	 * @return	bool
	**/
	public function put( $info = array(), $val = array(), $level = 0 )
	{
		// config
		$_CONF = $this->getConfig();
	
		// check setting 
		if ( empty($info) || !is_array($info) || !array_key_exists( $info['func'], $_CONF['setting'] ) ) {
			error_log( "[ERROR] invalid 'arguments' or 'configuration'", 4 );
			exit;
		}
		
		// check log path
		if ( !isset($_CONF['logPath']) || !preg_match('/^\/.+/', $_CONF['logPath']) ) {
			error_log( "[ERROR] invalid 'logPath'", 4 );
			exit;
		}
		$_CONF['logPath'] = rtrim($_CONF['logPath'], '/') . '/';
		
		// level
		if ( !$level ) {
			$level = $_CONF['setting'][$info['func']]['lv'];
		}
		if ( $level == $this->ACLOG_LEVEL_IGNORE ) {
			return;
		}
		
		// file name
		$fileName = $_CONF['logPrefix'] . '.' . $_CONF['setting'][ $info['func'] ]['name'] . '.' .date('Y-m-d');
		$suffix   = ( $level == $this->ACLOG_LEVEL_LOCAL ) ? '_nofoward' : '';
		$fileName = $fileName . $suffix;
		
		// file path
		$filePath = $_CONF['logPath'] . $fileName;
		
		// file pointer
		$fpkey = 'fp_' . $fileName;
		if ( !array_key_exists ( $fpkey, $this->logfp ) || !$this->logfp[$fpkey] ) {
			if (!( $this->logfp[$fpkey] = @fopen( $filePath, "a") )) {
				error_log('[ERROR]'.__METHOD__."($this->pid) : open error", 4 );
				return false;
			}
		}
		
		// make json
		$this->info['ts'] = date('Y-m-d H:i:s');
		$info = array_merge($info, $this->info);
		$json = json_encode( array( 'info' => $info, 'val' => $val ) );
		
		// write
		$retry = 0;
		/*
		while( 1 ){
			if ( fwrite( $this->logfp[$fpkey], "$json\n" ) ) {
				break;
			} 
			if ( $retry > 0 ) { 
				error_log('[ERROR]'. __METHOD__."($this->pid) : write error", 4 );
				error_log( "$json", 4 );
				break;
			} else {
				$this->logfp{$fpkey} = fopen( $full_path, "a");
				$retry++;
			}
		}
		*/
		if ( !fwrite( $this->logfp[$fpkey], "$json\n" ) ) {
			return false;
		} 
		
	}
	
	/*
	 * destructor
	 */
	function __destruct() {
		foreach( $this->logfp as $fpkey => $fp ) {
			@fclose( $fp );
		}
	}
}