<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.jianghuasheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 江华生 <jianghuasheng333gmail.com>  2017-10-12
// +----------------------------------------------------------------------
// | Desc: 本接口为测试案例，如有未经允许调用必追究责任！
// +----------------------------------------------------------------------

/*
 * 数据库连接信息配置
*/
	//mysql连接方式
	$servername = "";
	$username = "";
	$password = "";
	$dbname = "shortUrl";
	//域名配置
	$domainName = 'http://s.jianghuasheng.cn/';
	 
	// 创建连接
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    exit("<center style='font-size:40px;font-weight: bold;color:#333;margin-top:120px;'>501:数据连接失败！请联系管理员！^^<br><a href='http://www.jianghuasheng.cn' style='font-size:20px;color:#666;text-decoration:none;margin-top:30px;display:block;'>http://www.jianghuasheng.cn 江华生个人博客</a></center>");
	} 

    
?>
