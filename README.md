# phpider
phpider爬虫，php curl 获取http网页，通过 DOMDocument类分析网页从而得到所需要的数据

## 例子

### 引入phpider并实例化
```
require_once "phpider.php";
$phpider=new phpider(); //实例化phpider
```

### 通过get获取网页数据
```
$resultHtml=$phpider->request_http(["url"=>"http://www.vitionst.com/"]);//正常情况下就可以获取到html文本数据
```
### 通过选择器获取img图片地址src
```
$adom=$phpider->createDom($resultHtml)->selector("img");
for ($i=0; $i < $adom->length ; $i++) { 
    $childNode=$adom->item($i);
    echo $childNode->writeHtml(),"\n";//输出img标签
    echo $childNode->getAttribute("src"),"\n";//输出img的src 图片地址
}
```
### 通过正则方式获取img图片src 返回数组
```
//三种方式任选一种
$imgs=$phpider->regular("/[\w\/\.\/http\:\\\]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp|\/132)/",$resultHtml);
$imgs=$phpider->createDom($resultHtml)->regular("/[\w\/\.\/http\:\\\]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp|\/132)/");
$imgs=$phpider->createDom($resultHtml)->getImgs();//可以使用这种完全封装好的
```

### 通过post方式登录github

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

### 通过get方式搜索51job 职位相关列表
```
$phpider->request_http(["url"=>"https://www.51job.com"]);//先get下首页获取cookie，不get也无所谓
$postName="PHP开发工程师";
$job51Html=$phpider->request_http(["url"=>"https://search.51job.com/list/030200,000000,0000,00,9,99,".urlencode($postName).",2,1.html?lang=c&stype=&postchannel=0000&workyear=99&cotype=99&degreefrom=99&jobterm=99&companysize=99&providesalary=99&lonlat=0%2C0&radius=-1&ord_field=0&confirmdate=9&fromType=&dibiaoid=0&address=&line=&specialarea=00&from=&welfare="]);

$elClass=$phpider->createDom($job51Html)->selector(".el","div");
$job51Php=[];
for ($i=0; $i < $elClass->length ; $i++) { 
    if(!$elClass->item($i)->hasAttribute('id')){
        $temp=$elClass->item($i)->writeHtml();
        $aChidld=$phpider->childDom($temp)->selector("a");
        $jobInfo["post"]=$aChidld->item(0)->getText();
        $jobInfo["postUrl"]=$aChidld->item(0)->getAttribute("href");
        $jobInfo["company"]=$aChidld->item(1)->getText();
        $jobInfo["companyUrl"]=$aChidld->item(1)->getAttribute("href");

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

