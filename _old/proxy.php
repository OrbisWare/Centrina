<?php
function icq_uin($uin)
{
    if (! is_numeric($uin))
        return false;
    $proxy_name = 'proxy.mydomain.de';
    $proxy_port = 8080;
    $proxy_user = "";
    $proxy_pass = "";
    $proxy_cont = '';
    $request_url = "http://status.icq.com/online.gif?icq=$uin";

    $proxy_fp = fsockopen($proxy_name, $proxy_port);
    if (!$proxy_fp)
        return false;
    fputs($proxy_fp, "GET $request_url HTTP/1.0\r\nHost: $proxy_name\r\n");
    fputs($proxy_fp, "Proxy-Authorization: Basic ". base64_encode ("$proxy_user:$proxy_pass")."\r\n\r\n");
    while(!feof($proxy_fp)){
        $proxy_cont .= fread($proxy_fp,4096);
    }
    fclose($proxy_fp);
    $proxy_cont = substr($proxy_cont, strpos($proxy_cont,"\r\n\r\n")+4);
    if (strstr($proxy_cont, 'online1'))
        return 'online';
    if (strstr($proxy_cont, 'online0'))
        return 'offline';
    if (strstr($proxy_cont, 'online2'))
        return 'disabled';
} 
?>