<?php
class phpider extends DOMDocument {

     
    protected $html="";
    protected $tempHtml=false;
    public $dom=false;
    protected $nodeList="";
    protected $nowNode="";
    public $length=0;
    protected $index=0;
    protected $cookie="";
    function __construct($param=[]){
        libxml_use_internal_errors(true);

    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 11:16:38 
     * @Desc: 利用DOMDocument生成html对象
     * @Return:  当前对象
     */    
    function readHtml($html){
        // $this->dom = new DOMDocument();
        
        $this->loadHTML($html); 
        $this->html=$html; 
        return $this;
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:32:22 
     * @Desc:  把节点写出成html格式
     */    
    function writeHml($node=false){
        if(isset($this->documentElement)){
            if($node){
                return $this->saveHTML($node);
            }
            return $this->saveHTML($this->nodeList->item($this->index));
        }
        return '';
    }
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
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc:  选择器，支持id，class和标签
     */    
    function selector($sele,$tag="",$native=false){
        if($this->tempHtml){
            $this->loadHTML($this->html);
            $this->tempHtml=false;
        }
        
        if(isset($this->documentElement)){
            preg_match("/^([#|.]*)([\S]+)/",$sele,$match);
            if(count($match)>2){
                switch ($match[1]) {
                    case '#'://id
                        $this->nowNode= $this->getElementById($match[2]);
                        break;
                    case '.'://class
                        $allTags=$this->getElementsByTagName($tag);
                        $classHtml="";
                        for ($i=0; $i < $allTags->length ; $i++) {
                            $theNode=$allTags->item($i);
                            $classtr=$theNode->getAttribute('class');
                            if(trim($classtr)===$match[2]){
                                $temphtml=$this->saveHTML($theNode);
                                $classHtml.=$temphtml;
                            }
                        }
                        $this->loadHTML($this->utf8html($classHtml));
                        $this->nowNode=$this->getElementsByTagName($tag);
                        $this->tempHtml=true;
                        break;
                    case ''://标签
                        $this->nowNode= $this->getElementsByTagName($match[2]);
                        break;
                    default:
                        return false;
                        break;
                }
                $this->nodeList=$this->nowNode;
                if(isset($this->nodeList->length)){
                    $this->length=$this->nodeList->length;
                }
                if($native){
                    return $this->nowNode;
                }
                return $this;
            }
        }
        return false;
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc: 选择目标是标签的时候需要通过更改节点位置。
     */ 
    function item($index=0){
        if($this->nodeList!="" && isset($this->nodeList->length)){
            $this->index=$index;
            $this->nowNode=$this->nodeList->item($index);
            return $this;
        }
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-29 00:14:01 
     * @Desc: 判断是否存在某个属性 
     */    
    function hasAttribute($attr){
        if($this->nowNode!=""){
            return $this->nowNode->hasAttribute($attr);
        }
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc:  获取当前节点的属性，可以获取所有
     */ 
    function getAttribute($attr){
        if($this->nowNode!=""){
            return $this->nowNode->getAttribute($attr);
        }
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc:  获取当前节点的文本
     */ 
    function getText(){
        if($this->nowNode!=""){
            return $this->nowNode->nodeValue;
        }
    }
    /** 
    * @Author: vition 
    * @Date: 2018-03-27 19:34:12 
    * @Desc:  快捷方式，获取页面所有图片
    */   
    function getImgs($html=""){
        return $this->regular("/[\w\/\.\/http\:]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp)/",$html);
    }
    function getLink($html=""){
        if($html!=""){
            $html=$this->html;//可以自动调用readHtml加载过的html
        }
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 20:12:12 
     * @Desc:  高级模式了，正则自己匹配
     */    
    function regular($pattern,$html=""){
        if($html==""){
            $html=$this->html;//可以自动调用readHtml加载过的html
        }
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

    function utf8html($tags){
        return "<!DOCTYPE html><html><head><meta charset='utf-8'></head><body>".$tags."</body></html>";
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
        if($charset[1]){
            $body=preg_replace($pattern,preg_replace("/(GBK|GB2312|BIG5)/i","UTF-8",$charset[0]),$body);
        }
        return $body;
    }
}