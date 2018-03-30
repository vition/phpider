# phpdier (php开发网络爬虫)

### 1，介绍
> 通过curl_setopt对网络发起get/post请求，再使用DOMDocument对html文档进行节点和属性分析，配合正则，可以达到简单的爬虫效果。

### 2，目录文件结构
> phpider目录下有三个文件。
1. dom.php DOMDocument 操作类
2. function.php 常用的自定义公共函数
3. phpider.php 主要文件，已引入上述两个文件

### 3，phpider主要方法说明

#### 1，request_http方法
> 参数是数组，参数说明如下
- url 请求的http地址
- type 请求类型，默认get，post需要自行带上
- data post时发送请求数据
- cookie post时记得带上cookie，不过该方法默认会带上cookie，除非cookie有变
- header 请求头
- timeOut 请求超时，默认60秒
- verify 是否证书认证，默认否
- sslCert 证书认证时需要带上cert证书地址
- sslKey 证书认证时需要带上key证书地址
- encoding 编码，默认是utf8
- referer 引用路径

#### 2，createDom方法
> 参数是html文本，从request_http方法中获取html就可以通过次方法生成Dom对象了，

#### 3，childDom方法
> 参数是html文本 和createDom功能一样，不同的是，片段的html标签需要带上Html等标签，同时也为了区分，所以有此方法

#### 4，downFile方法
> 下载文件到本地，参数1是文件名字，参数2是文件的网络地址(http),

### 4，dom主要方法说明

#### 1，selector选择器方法
> 对dom文档进行节点筛选了，参数说明如下：
sele：选择器，#开头表示选择id，.表示选择class，其他表示标签；
tag：当sele为class(.)的时候必须带上此参数，表示class属于哪个标签的；
notId：当sele为class(.)该参数生效，默认为true，表示查找的class标签里不包含id属性
> 选择id时返回的是单数，因为id是唯一的，其他都是多个。

#### 2，native方法
> dom类中目前可用方法比较少，如果想使用原生DOMDocument的方法，可以在selector()->native()返回原生的对象

#### 3，item方法
> 参数是一个整数，一般是选择非id的时候常用，表示选择第几个节点

#### 4，writeHtml方法
> 可以将当前的节点转换成html文本模式

#### 5，getAttribute方法
> 获取当前节点的属性，例如 id，class，src，href等。

#### 6，hasAttribute方法
> 判断当前节点是否包含某个属性，同上。

#### 7，getText方法
> 返回当前节点的文本


### 4，例子

#### 引入phpider并实例化

```
require_once "phpider.php";
$phpider=new phpider(); //实例化phpider
```
#### 通过get获取网页数据
```
$resultHtml=$phpider->request_http(["url"=>"http://www.vitionst.com/"]);//正常情况下就可以获取到html文本数据
```
#### 通过选择器获取img图片地址src
```
$adom=$phpider->createDom($resultHtml)->selector("img");
for ($i=0; $i < $adom->length ; $i++) { 
    $childNode=$adom->item($i);
    echo $childNode->writeHtml(),"\n";//输出img标签
    echo $childNode->getAttribute("src"),"\n";//输出img的src 图片地址
}
```
#### 通过正则方式获取img图片src 返回数组
```
//三种方式任选一种
$imgs=$phpider->regular("/[\w\/\.\/http\:\\\]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp|\/132)/",$resultHtml);
$imgs=$phpider->createDom($resultHtml)->regular("/[\w\/\.\/http\:\\\]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp|\/132)/");
$imgs=$phpider->createDom($resultHtml)->getImgs();//可以使用这种完全封装好的
```
#### 通过post方式登录github

```
$gitHubHtml=$phpider->request_http(["url"=>"https://github.com/login"]);//先获取login页面
if($gitHubHtml!=""){
    $inputs=$phpider->createDom($gitHubHtml)->selector("input");//分析获取页面所有input标签，authenticity_token隐藏在input中
    $token=false;
    for ($i=0; $i < $inputs->length ; $i++) { 
        $name=$inputs->item($i)->getAttribute("name");
        if($name=="authenticity_token"){//判断所有input的name是否为authenticity_token
            $token=$inputs->item($i)->getAttribute("value");//储存authenticity_token的值
            break;
        }
    }
    if($token){
        $data=[
            "authenticity_token"=>$token,
            "commit"=>"Sign+in",
            "login"=>"github账号",
            "password"=>"github密码",
            "utf8"=>"✓"//urldecode 之后是%E2%9C%93 request_http方法里有 http_build_query 所以就不带了。
        ];
        /*2，通过post方式提交登录数据，request_http中自带cookie了*/
        $loginResult=$phpider->request_http(["url"=>"https://github.com/session","type"=>"post","data"=> $data]);//post请求
        file_put_contents("loginResult.html",$loginResult);
        /*3，request_http中自带cookie，所以可以通过get方式获取到对应的页面了，就像浏览器操作一样*/
        $profile=$phpider->request_http(["url"=>"https://github.com/github账号"]);
        file_put_contents("profile.html",$profile);
    }
}
```

#### 通过get方式搜索51job 职位相关列表
```
$phpider->request_http(["url"=>"https://www.51job.com"]);//先get下首页获取cookie，不get也无所谓
$postName="PHP开发工程师";
$job51Html=$phpider->request_http(["url"=>"https://search.51job.com/list/030200,000000,0000,00,9,99,".urlencode($postName).",2,1.html?lang=c&stype=&postchannel=0000&workyear=99&cotype=99&degreefrom=99&jobterm=99&companysize=99&providesalary=99&lonlat=0%2C0&radius=-1&ord_field=0&confirmdate=9&fromType=&dibiaoid=0&address=&line=&specialarea=00&from=&welfare="]);

$elClass=$phpider->createDom($job51Html)->selector(".el","div");
$job51Php=[];
for ($i=0; $i < $elClass->length ; $i++) { 
    if(!$elClass->item($i)->hasAttribute('id')){
        $temp=$elClass->item($i)->writeHtml();
        $aChild=$phpider->childDom($temp)->selector("a");
        $jobInfo["post"]=$aChild->item(0)->getText();
        $jobInfo["postUrl"]=$aChild->item(0)->getAttribute("href");
        $jobInfo["company"]=$aChild->item(1)->getText();
        $jobInfo["companyUrl"]=$aChild->item(1)->getAttribute("href");

        $spanChidld=$phpider->childDom($temp)->selector("span");
        $jobInfo["position"]=$spanChidld->item(2)->getText();
        $jobInfo["salary"]= $spanChidld->item(3)->getText();
        $jobInfo["addDate"]= $spanChidld->item(4)->getText();
        $job51Php[]=$jobInfo;
    }
}
if(!empty($job51Php)){
    $htmlTable="";
    foreach ($job51Php as $jobInfo) {
        $htmlTable.="<tr>";
        foreach ($jobInfo as $subJobInfo) {
            $htmlTable.="<td>{$subJobInfo}</td>";
        }
        $htmlTable.="</tr>";
    }
    $htmlTable="<table><tr><th>职位名称</th><th>职位链接</th><th>公司名称</th><th>公司链接</th><th>地区</th><th>薪资</th><th>发布日期</th></tr>{$htmlTable}</table>";
    file_put_contents("job51Table.html",utf8html($htmlTable));
}
``` 
> 效果图，单纯无修饰的table
![image](https://raw.githubusercontent.com/vition/phpider/master/imgs/phpjob.png)

#### 爬京东数据，根据关键字搜索书籍
```
$phpider->request_http(["url"=>"https://www.jd.com"]);//先get下首页获取cookie，不get也无所谓
$postName="web开发";//要爬的关键字
$product=[];//最终存储的数组

function getProduct($html){//定义一个函数来处理产品信息
    global $phpider,$product;
    $items=$phpider->createDom($html)->selector(".gl-item","li");//获取所有产品的节点
    for ($i=0; $i < $items->length; $i++) { 
        $subItem=$items->item($i);
        $datasku= $subItem->getAttribute("data-sku");
        $subDom=$phpider->childDom($subItem->writeHtml());//每个产品节点

        $product[$datasku]["datasku"]=$datasku;
        $aChild=$subDom->selector("a");
        $product[$datasku]["url"]=protocol($aChild->item(0)->getAttribute("href"),"https:");//京东指定链接
        $product[$datasku]["author"]=$aChild->item(2)->getAttribute("title");//作者信息
        $product[$datasku]["store"]=$aChild->item(3)->getAttribute("title");//出版商

        $emChild=$subDom->selector("em");
        $product[$datasku]["name"]=$emChild->item(1)->getText();//书名

        $iChild=$subDom->selector("i");
        $product[$datasku]["price"]=$iChild->item(0)->getText();//价格

        $imgs=$subDom->getImgs();
        $product[$datasku]["img"]=protocol($imgs[0],"https://");//产品图片
    }
}
$jdHtml=$phpider->request_http(["url"=>"https://search.jd.com/Search?keyword=".urlencode($postName)."&enc=utf-8&wq=".urlencode($postName).""]);//这里获取京东初始化查找的第一次数据，京东第一次默认获取30个产品，
// file_put_contents("jdpage.html",$jdHtml);//可以把获取到的数据写到文件中，也可以不写。

preg_match_all("/log_id:[\'\"]?([\d\.]+)[\'\"]?/",$jdHtml,$match);//获取一个log_id在第二次请求时需要使用，
$log_id=isset($match[1][0])?$match[1][0]:'';

$show_items="";
getProduct($jdHtml);//第一次处理产品数据了
$show_items=implode(",",array_column($product,"datasku"));//这个值也是第二次请求的时候需要的数据

$jd2Html=$phpider->request_http(["url"=>"https://search.jd.com/s_new.php?keyword=".urlencode($postName)."&enc=utf-8&qrst=1&rt=1&stop=1&vt=2&wq=".urlencode($postName)."&page=2&s=27&scrolling=y&log_id={$log_id}&tpl=2_M&show_items={$show_items}","referer"=>"https://search.jd.com/Search?keyword=".urlencode($postName)."&enc=utf-8&wq=".urlencode($postName).""]);//这里发送第二次请求，注意，要带上referer，不然是获取不到数据的。
// file_put_contents("jdpage2.html",$jd2Html);//同样可以把它写到文件中，也可以不写。只是作为验证数据而已。
getProduct(utf8Html($jd2Html));//处理第二次的数据，要使用utf8Html，因为第二次请求的html是没有html、head的标签
//下面是开始输出为表格了。
if(!empty($product)){
    $htmlTable="";
    foreach ($product as $Info) {
        $htmlTable.="<tr>";
        $htmlTable.="<td>{$Info['datasku']}</td><td>{$Info['name']}</td><td>{$Info['author']}</td><td>{$Info['store']}</td><td>{$Info['price']}</td><td>{$Info['img']}</td><td>{$Info['url']}</td>";
        $htmlTable.="</tr>";
    }
    $htmlTable="<table><tr><th>编号</th><th>书名</th><th>作者</th><th>出版社</th><th>价格</th><th>图片</th><th>京东链接</th></tr>{$htmlTable}</table>";
    file_put_contents("jdTable.html",utf8Html($htmlTable));
}
```
> 效果图，单纯无修饰的table
![image](https://raw.githubusercontent.com/vition/phpider/master/imgs/keyHTML5.png)
![image](https://raw.githubusercontent.com/vition/phpider/master/imgs/keyWeb.png)