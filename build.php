<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.jianghuasheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 江华生 <jianghuasheng333gmail.com>  2017-10-12
// +----------------------------------------------------------------------
// | Desc: 本接口为测试专用，如有未经允许调用必追究责任！
// +----------------------------------------------------------------------

/*
 * 把长链接转换成短链接的功能模块
*/

//开放访问
header("Access-Control-Allow-Origin: *");
//关闭错误提醒
// error_reporting(E_ALL^E_NOTICE^E_WARNING);
//短网址生成算法
class ShortUrl {
    //字符表
    public static $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

    //短网址生成
    public static function encode($url)
    {
        $key = 'iAmJiangHuaSheng'; //加盐
        $urlhash = md5($key . $url);
        $len = strlen($urlhash);
 
        //将加密后的串分成4段，每段4字节，对每段进行计算，一共可以生成四组短连接
        for ($i = 0; $i < 4; $i++) {
            $urlhash_piece = substr($urlhash, $i * $len / 4, $len / 4);
            
            //将分段的位与0x3fffffff做位与，0x3fffffff表示二进制数的30个1，即30位以后的加密串都归零
            //此处需要用到hexdec()将16进制字符串转为10进制数值型，否则运算会不正常
            $hex = hexdec($urlhash_piece) & 0x3fffffff;
 
            //域名根据需求填写
            $short_url = '';
            
            //生成6位短网址
            for ($j = 0; $j < 6; $j++) {
                //将得到的值与0x0000003d,3d为61，即charset的坐标最大值
                $short_url .= self::$charset[$hex & 0x0000003d];
                //循环完以后将hex右移5位
                $hex = $hex >> 5;
            }
 
            $short_url_list[] = $short_url;
        }
        return $short_url_list;
    }
}
//判断是否有传参
if($_GET){
    if(isset($_GET['url']) && !empty($_GET['url'])){
    	//接受参数
        $url = $_GET['url'];
        if(empty($url)){
            $ret = array('msg' => "非法参数!",'status' => '0');
            exit(json_encode($ret,JSON_UNESCAPED_UNICODE));
        }
        //数据库配置
        require 'db.php';
        //查询数据库是否存在该记录
        $has = mysqli_query($conn, "SELECT * FROM url WHERE url='".$url."'");
        
        if(mysqli_num_rows($has) > 0){
            $data = mysqli_fetch_assoc($has);
            //关闭连接
            mysqli_close($conn);
            $ret = array('msg' => "原存在!",'status' => '1','url'=>$data['url'],'shortUrl'=>$domainName.$data['s_url']);
            exit(json_encode($ret,JSON_UNESCAPED_UNICODE));
        }else{
            //生成短网址
            $short = ShortUrl::encode($url)[0];
            //时间戳
            $time = time();
            //存入数据库
            $sql2 = "INSERT INTO url(id,s_url,url,view,time) VALUES ('','" . $short . "','" . $url . "','0','".$time."')";
            if ($conn->query($sql2) === TRUE) {
                //关闭连接
                mysqli_close($conn);
                $ret = array('msg' => "转化成功!",'status' => '1','url'=>$url,'shortUrl'=>$domainName.$short);
                exit(json_encode($ret,JSON_UNESCAPED_UNICODE));
            } else {
                //关闭连接
                mysqli_close($conn);
                $ret = array('msg' => "502：转发失败!",'status' => '0');
                exit(json_encode($ret,JSON_UNESCAPED_UNICODE));
            }
            
        }
        
    }else{
        exit(json_encode(array('msg' => "401:非法访问!",'status' => '0'),JSON_UNESCAPED_UNICODE));
    }
}else{
    exit(json_encode(array('msg' => "401:非法访问!",'status' => '0'),JSON_UNESCAPED_UNICODE));
}

?>
