<?php
/** 
 * @Author: vition 
 * @Date: 2018-03-30 14:35:53 
 * @Desc: 各类公共函数 
 */
/** 
 * @Author: vition 
 * @Date: 2018-03-27 16:32:40 
 * @Desc: 清除各种空格
 */    
function clearing($str){ 
    $str = trim($str); //清除字符串两边的空格
    $str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
    $str = preg_replace("/\r\n/","",$str); 
    $str = preg_replace("/\r/","",$str); 
    $str = preg_replace("/\n/","",$str); 
    // $str = preg_replace("/ /","",$str);
    $str = preg_replace("/  /","",$str);  //匹配html中的空格
    return trim($str); //返回字符串
/** 
 * @Author: vition 
 * @Date: 2018-03-30 14:37:18 
 * @Desc: utf编码的html
 */}
function utf8Html($tags){
    return "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>".$tags."</body></html>";
}
/** 
 * @Author: vition 
 * @Date: 2018-03-27 20:12:12 
 * @Desc:  高级模式了，正则自己匹配
 */    
function regular($pattern,$html){
    preg_match_all($pattern, $html, $match);
    $items=[];
    if(count($match[0])>0){
        $items = array_unique($match[0]);
        foreach ($items as $key => $item) {
            $items[$key]=ltrim($item,"/");
        }
        return $items;
    }
    return [];
}
function json_encode_cn($array){
    return json_encode($array,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
}
function protocol($url,$protocol){
    preg_match("/^http/",$url,$match);
    if(isset($match[0])){
        return $url;
    }
    return $protocol.$url;
}