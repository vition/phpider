<?php
class dom extends DOMDocument{
    protected $html="";
    protected $nodeList="";
    protected $nowNode="";
    protected $tempHtml=false;
    public $length=0;
    protected $index=0;
    function __construct($html){
        libxml_use_internal_errors(true);
        $this->loadHTML($html);
        $this->html=$html;
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:32:22 
     * @Desc:  把节点写出成html格式
     */    
    function writeHtml($node=false){
        if(isset($this->documentElement)){
            if($node){
                $this->formatOutput=true;
                return $this->saveHTML($node);
            }
            if($this->nodeList!="" && isset($this->nodeList->length)){
                return $this->saveHTML($this->nodeList->item($this->index));
            }
            return $this->saveHTML($this->nowNode);
        }
        return '';
    }
    /** 
     * @Author: vition 
     * @Date: 2018-03-27 16:33:03 
     * @Desc:  选择器，支持id，class和标签
     */    
    function selector($sele,$tag="",$notId=true){//如果是class，默认该标签不含id
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
                        for ($i=0; $i < $allTags->length ; $i++) {//$allTags->length
                            $theNode=$allTags->item($i);
                            $classtr=$theNode->getAttribute('class');
                             if($notId){
                                if(trim($classtr)===$match[2] && !$theNode->hasAttribute('id')){
                                    $temphtml=$this->saveHTML($theNode);
                                    $classHtml.=$temphtml;
                                }
                            }else{
                                if(trim($classtr)===$match[2]){
                                    $temphtml=$this->saveHTML($theNode);
                                    $classHtml.=$temphtml;
                                }
                            }
                        }
                        $this->loadHTML(utf8Html($classHtml));
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
                return $this;
            }
        }
        return false;
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
     * @Date: 2018-03-27 20:12:12 
     * @Desc:  高级模式了，正则自己匹配
     */    
    function regular($pattern,$html=""){
        if($html==""){
            $html=$this->html;//可以自动调用readHtml加载过的html
        }
        return regular($pattern,$html);
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
     * @Desc:  获取当前节点的文本
     */ 
    function getText(){
        if($this->nowNode!=""){
            return clearing($this->nowNode->nodeValue);
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
}