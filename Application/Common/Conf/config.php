<?php
return array(  //设置在页面底部显示系统跟踪信息
    'SHOW_PAGE_TRACE'   =>true,
    
	//'配置项'=>'配置值'
    //数据库配置信息
    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'php_28', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => 'php28_', // 数据库表前缀 
    'DB_CHARSET'=> 'utf8', // 字符集

      
      
    //设置在页面底部显示系统跟踪信息
    'SHOW_PAGE_TRACE'   =>true,
    
  /************** 发邮件的配置 ***************/
	'MAIL_ADDRESS' => 'php28_28@163.com',   // 发货人的email
	'MAIL_FROM' => 'php28_28',      // 发货人姓名
	'MAIL_SMTP' => 'smtp.163.com',      // 邮件服务器的地址
	'MAIL_LOGINNAME' => 'php28_28',   
	'MAIL_PASSWORD' => 'php123123',
                        // Email验证码有效期
	'EMAIL_CODE_EXPIRE_TIME' => 3600,
    
    // 用来DES加密的密钥
	'DES_KEY' => 'fd@212_3#43',
	// 使用的加密算法是什么
	'DATA_CRYPT_TYPE' => 'Des',
    
    'HTML_CACHE_ON'     =>    FALSE, // 开启静态缓存
    'HTML_CACHE_TIME'   =>    60,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'  =>    '.html', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'  =>     array(
           'Index:index'=>array('index',3600),
            'Index:goods'=>array('Goods/{id}',6000)
    ),
 
  
);