<?php

//1 1����������

define('UC_CLIENT_VERSION', '1.5.0');	//note UCenter �汾��ʶ
define('UC_CLIENT_RELEASE', '20081031');

define('API_DELETEUSER', 1);		//note �û�ɾ�� API �ӿڿ���
define('API_RENAMEUSER', 1);		//note �û����� API �ӿڿ���
define('API_GETTAG', 1);		//note ��ȡ��ǩ API �ӿڿ���
define('API_SYNLOGIN', 1);		//note ͬ����¼ API �ӿڿ���
define('API_SYNLOGOUT', 1);		//note ͬ���ǳ� API �ӿڿ���
define('API_UPDATEPW', 1);		//note �����û����� ����
define('API_UPDATEBADWORDS', 1);	//note ���¹ؼ����б� ����
define('API_UPDATEHOSTS', 1);		//note ���������������� ����
define('API_UPDATEAPPS', 1);		//note ����Ӧ���б� ����
define('API_UPDATECLIENT', 1);		//note ���¿ͻ��˻��� ����
define('API_UPDATECREDIT', 1);		//note �����û����� ����
define('API_GETCREDITSETTINGS', 1);	//note �� UCenter �ṩ�������� ����
define('API_GETCREDIT', 1);		//note ��ȡ�û���ĳ����� ����
define('API_UPDATECREDITSETTINGS', 1);	//note ����Ӧ�û������� ����

define('API_RETURN_SUCCEED', '1');
define('API_RETURN_FAILED', '-1');
define('API_RETURN_FORBIDDEN', '-2');

//�ڶ�����2��֪ͨ��ʽ�ж��Լ���ʼ���������

if(!defined('IN_UC')) {

	error_reporting(0);
	set_magic_quotes_runtime(0);

	define('DISCUZ_ROOT', substr(dirname(__FILE__), 0, -3));
	defined('MAGIC_QUOTES_GPC') || define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	require_once DISCUZ_ROOT.'./config.inc.php';

	$_DCACHE = $get = $post = array();

	$code = @$_GET['code'];
	parse_str(authcode($code, 'DECODE', UC_KEY), $get);
	if(MAGIC_QUOTES_GPC) {
		$get = _stripslashes($get);
	}

	$timestamp = time();
	if($timestamp - $get['time'] > 3600) {
		exit('Authracation has expiried');
	}
	if(empty($get)) {
		exit('Invalid Request');
	}
	$action = $get['action'];

	require_once DISCUZ_ROOT.'./uc_client/lib/xml.class.php';
	$post = xml_unserialize(file_get_contents('php://input'));

	if(in_array($get['action'], array('test', 'deleteuser', 'renameuser', 'gettag', 'synlogin', 'synlogout', 'updatepw', 'updatebadwords', 'updatehosts', 'updateapps', 'updateclient', 'updatecredit', 'getcreditsettings', 'updatecreditsettings'))) {
		
		$uc_note = new uc_note();
		exit($uc_note->$get['action']($get, $post));
	} else {
		exit(API_RETURN_FAILED);
	}

} else {

	define('DISCUZ_ROOT', $app['extra']['apppath']);
	require_once DISCUZ_ROOT.'./config.inc.php';
	require_once DISCUZ_ROOT.'./include/db_'.$database.'.class.php';
	$GLOBALS['db'] = new dbstuff;
	$GLOBALS['db']->connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect, true, $dbcharset);
	$GLOBALS['tablepre'] = $tablepre;
	unset($dbhost, $dbuser, $dbpw, $dbname, $pconnect);
}

//�������� �ӿڴ���

class uc_note {

	var $dbconfig = '';
	//var $db = '';
	var $appdir = '';

	function _serialize($arr, $htmlon = 0) {
		if(!function_exists('xml_serialize')) {
			include_once DISCUZ_ROOT.'./uc_client/lib/xml.class.php';
		}
		return xml_serialize($arr, $htmlon);
	}

	function uc_note() {
		$this->appdir = substr(dirname(__FILE__), 0, -3);
		$this->dbconfig = $this->appdir.'./config.inc.php';
	}

	function test($get, $post) {
		return API_RETURN_SUCCEED;
	}

	function deleteuser($get, $post) {
		/*����ʡ��*/
		return API_RETURN_SUCCEED;
	}
	
	/* ����ӿ���Ŀ */
        
        /* 更多接口项目 */
	function synlogout()
	{
		// 让我们这个网站也退出
		session_start();
		$_SESSION = array();
		session_destroy();
		setcookie('username', '', 1 , '/', '.28.com');
		setcookie('password', '', 1 , '/', '.28.com');
	}
        
        





//���Ĳ���4����������


function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {

	$ckey_length = 4;

	$key = md5($key ? $key : UC_KEY);
	$keya = md5(substr($key, 0, 16));
	$keyb = md5(substr($key, 16, 16));
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

	$cryptkey = $keya.md5($keya.$keyc);
	$key_length = strlen($cryptkey);

	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
	$string_length = strlen($string);

	$result = '';
	$box = range(0, 255);

	$rndkey = array();
	for($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}

	for($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}

	for($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}

	if($operation == 'DECODE') {
		if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		return $keyc.str_replace('=', '', base64_encode($result));
	}

}

function uc_serialize($arr, $htmlon = 0) {
	include_once UC_CLIENT_ROOT.'./lib/xml.class.php';
	return xml_serialize($arr, $htmlon);
}

function uc_unserialize($s) {
	include_once UC_CLIENT_ROOT.'./lib/xml.class.php';
	return xml_unserialize($s);
}
