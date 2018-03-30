<?php
class phpider{

    protected $htmlDom;
    protected $subDom;
    
    
    public $dom=false;
    
    
    protected $cookie="";
    function __construct($param=[]){
        require_once "dom.php";
        require_once "function.php";
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 11:16:38 
     * @Desc: 利用DOMDocument生成html Dom对象
     * @Return:  当前对象
     */    
    function createDom($html){
        return $this->htmlDom= new dom($html);
    }
    function childDom($html){
        return $this->subDom= new dom(utf8html($html));
    }
    
    
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc: 返回DOMDocument原生element。
     */ 
    function native(){
        return $this->nowNode;
    }
    
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 20:12:12 
     * @Desc:  高级模式了，正则自己匹配
     */    
    function regular($pattern,$html){
        return regular($pattern,$html);
        preg_match_all($pattern, $html, $match);
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc: 根据url下载指定文件到本地
     */ 
    function downFile($fileName,$url){
        $file=$this->get_http(["url"=>$url]);
        $filePath=explode("/",$fileName);
        $src=implode("/",array_slice($filePath,0,count($filePath)-1));
        if(!file_exists($src)){
            mkdir($src, 0755,true);
        }
        if(!empty($file["content"])){
            return file_put_contents($fileName,$file["content"]);
        }
        return false;
        
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-28 15:02:06 
     * @Desc:  获取Cookie
     */    
    function getCookie(){
        return $this->cookie;
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-26 17:15:12 
     * @Desc: POST/GET方式请求 
     */    
    function request_http($param=[]){

        $url=isset($param["url"])?$param["url"]:'';
        $type=isset($param["type"])?$param["type"]:'get';
        $data=isset($param["data"])?$param["data"]:'';
        $cookie=isset($param["cookie"])?$param["cookie"]:$this->cookie;
        $header=isset($param["header"])?$param["header"]:'';
        $timeOut=isset($param["timeOut"])?$param["timeOut"]:60;
        $verify=isset($param["verify"])?$param["verify"]:false;
        $sslCert=isset($param["sslCert"])?$param["sslCert"]:'';
        $sslKey=isset($param["sslKey"])?$param["sslKey"]:'';
        $encoding=isset($param["encoding"])?$param["encoding"]:'UTF-8';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,$verify);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.1; Trident/6.0)');
        if($verify==true){
            curl_setopt($ch, CURLOPT_SSLCERT,$verify); 
            curl_setopt($ch, CURLOPT_SSLKEY, $sslKey);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        if($type=="post"){
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        
        if(!empty($header)){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header); 
        }
        if($cookie){
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
        }
        if($data){
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($ch, CURLOPT_TIMEOUT,$timeOut);
        ob_start();
        $resultData =curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $resultData, 2);
        preg_match("/Not Found/",$header,$statu);
        if(!empty($statu)){    
            return "";
        }
        preg_match_all("/Set\-Cookie:([^;]*);/", $header, $matches);
        if(isset($matches[1][0])>0){
            $this->cookie=substr($matches[1][0], 1);
        }
        ob_end_clean();
        curl_close($ch);
        //需要对文档进行转换，DOMDocument只支持utf-8
        $body=mb_convert_encoding($body, $encoding, 'UTF-8,GBK,GB2312,BIG5');
        $pattern="/<meta[\w\ \=\'\"\/\;\-]*charset\=[\'\"]*(GBK|GB2312|BIG5)[\'\"]*/i";
        preg_match($pattern,$body,$charset);
        if(isset($charset[1])){
            $body=preg_replace($pattern,preg_replace("/(GBK|GB2312|BIG5)/i","UTF-8",$charset[0]),$body);
        }
        return $body;
    }
}