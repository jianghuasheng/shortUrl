<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2017 http://www.jianghuasheng.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 江华生 <jianghuasheng333gmail.com>  2017-10-12
// +----------------------------------------------------------------------
// | Desc: 本接口为测试案例，如有未经允许调用必追究责任！
// +----------------------------------------------------------------------

/*
 * 把短链接还原成长链接的功能模块
*/

//开放访问
header("Access-Control-Allow-Origin: *");
//关闭错误提醒
error_reporting(E_ALL^E_NOTICE^E_WARNING);
//判断是否有传参
if($_GET){
    if(isset($_GET['url']) && !empty($_GET['url'])){
        //接受参数
        $url = $_GET['url'];
        if(empty($url)){
            $ret = array('msg' => "非法参数!",'status' => '0');
            exit(json_encode($ret,JSON_UNESCAPED_UNICODE));
        }
        //去掉第一个‘/’
        $url = substr($url, 1);
        //数据库配置
        require 'db.php';
        //查询数据库是否存在该记录
        $sql1 = "SELECT id,url,view FROM url WHERE s_url='".$url."'";
        $has = mysqli_query($conn, $sql1);
        if(mysqli_num_rows($has) > 0){
            $data = mysqli_fetch_assoc($has);
            //关闭连接
            if(!empty($data)){
                //更新访问次数和记录
                mysqli_query($conn, "INSERT INTO log(id,u_id,ip,time,s_info) VALUES ('','" . $data['id'] . "','".$_SERVER['REMOTE_ADDR']."','".time()."','".json_encode($_SERVER)."')");
                mysqli_query($conn, "UPDATE url SET view=view+1 WHERE id='".$data['id']."'");
                //关闭连接
                mysqli_close($conn);
                //跳转到目的地址
                Header("Location: ".$data['url']);
            }else{
                //关闭连接
                mysqli_close($conn);
                exit("<center style='font-size:40px;font-weight: bold;color:#333;margin-top:120px;'>404:不存在该记录，请确认该地址是否正确！^^<br><a href='http://www.jianghuasheng.cn' style='font-size:20px;color:#666;text-decoration:none;margin-top:30px;display:block;'>http://www.jianghuasheng.cn 江华生个人博客</a></center>");
            }
        }else{
            //关闭连接
            mysqli_close($conn);
            exit("<center style='font-size:40px;font-weight: bold;color:#333;margin-top:120px;'>404:不存在该记录，请确认该地址是否正确！^^<br><a href='http://www.jianghuasheng.cn' style='font-size:20px;color:#666;text-decoration:none;margin-top:30px;display:block;'>http://www.jianghuasheng.cn 江华生个人博客</a></center>");
        }
    }else{
        exit(json_encode(array('msg' => "401:非法访问!",'status' => '0'),JSON_UNESCAPED_UNICODE));
    }
}else{
    exit(json_encode(array('msg' => "401:非法访问!",'status' => '0'),JSON_UNESCAPED_UNICODE));
}

?>
