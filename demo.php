<?php
/** 
 * @Author: vition 
 * @Date: 2018-03-28 14:28:27 
 * @Desc: phpider 例子 
 */
// require_once "phpider/phpider.php";
// $phpider=new phpider(); //实例化phpider


// /*通过get获取网页数据*/
// $resultHtml=$phpider->request_http(["url"=>"http://www.vitionst.com/"]);//正常情况下就可以获取到html文本数据

// // $imgs=$phpider->createDom($resultHtml)->getImgs();

// /*获取所有的img*/
// $adom=$phpider->createDom($resultHtml)->selector("img");
// for ($i=0; $i < $adom->length ; $i++) { 
//     $childNode=$adom->item($i);
//     // echo $childNode->writeHtml(),"\n";//输出img标签
//     echo protocol($childNode->getAttribute("src"),"https://www.vitionst.com/"),"\n";//输出img的src 图片地址
// }
// $imgs=$phpider->createDom($resultHtml)->regular("/[\w\/\.\/http\:\\\]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp|\/132)/");
// print_r($imgs);
// $phpider->regular("/[\w\/\.\/http\:\\\]*(\.gif|\.jpeg|\.png|\.jpg|\.bmp|\/132)/",$resultHtml);
// print_r($imgs);

// print_r($dom);

// /*通过post方式登录github*/
// /*1，获取authenticity_token*/
// $gitHubHtml=$phpider->request_http(["url"=>"https://github.com/login"]);//先获取login页面
// if($gitHubHtml!=""){
//     $inputs=$phpider->createDom($gitHubHtml)->selector("input");//分析获取页面所有input标签，authenticity_token隐藏在input中
//     $token=false;
//     for ($i=0; $i < $inputs->length ; $i++) { 
//         $name=$inputs->item($i)->getAttribute("name");
//         if($name=="authenticity_token"){//判断所有input的name是否为authenticity_token
//             $token=$inputs->item($i)->getAttribute("value");//储存authenticity_token的值
//             break;
//         }
//     }
//     if($token){
//         $data=[
//             "authenticity_token"=>$token,
//             "commit"=>"Sign+in",
//             "login"=>"github账号",
//             "password"=>"github密码",
//             "utf8"=>"✓"//urldecode 之后是%E2%9C%93 request_http方法里有 http_build_query 所以就不带了。
//         ];
//         /*2，通过post方式提交登录数据，request_http中自带cookie了*/
//         $loginResult=$phpider->request_http(["url"=>"https://github.com/session","type"=>"post","data"=> $data]);//post请求
//         file_put_contents("loginResult.html",$loginResult);
//         /*3，request_http中自带cookie，所以可以通过get方式获取到对应的页面了，就像浏览器操作一样*/
//         $profile=$phpider->request_http(["url"=>"https://github.com/github账号"]);
//         file_put_contents("profile.html",$profile);
//     }
// }

/*通过get方式获取51job php开发工程师数据*/
// $phpider->request_http(["url"=>"https://www.51job.com"]);//先get下首页获取cookie，不get也无所谓
// $postName="PHP开发工程师";
// $job51Html=$phpider->request_http(["url"=>"https://search.51job.com/list/030200,000000,0000,00,9,99,".urlencode($postName).",2,1.html?lang=c&stype=&postchannel=0000&workyear=99&cotype=99&degreefrom=99&jobterm=99&companysize=99&providesalary=99&lonlat=0%2C0&radius=-1&ord_field=0&confirmdate=9&fromType=&dibiaoid=0&address=&line=&specialarea=00&from=&welfare="]);
// // file_put_contents("job51.html",$job51Html);exit;
// // $job51Html=file_get_contents("job51.html");
// $elClass=$phpider->createDom($job51Html)->selector(".el","div");
// $job51Php=[];
// for ($i=0; $i < $elClass->length ; $i++) { //
//     if(!$elClass->item($i)->hasAttribute('id')){
//         $temp=$elClass->item($i)->writeHtml();
//         $aChild=$phpider->childDom($temp)->selector("a");
//         $jobInfo["post"]=$aChild->item(0)->getText();
//         $jobInfo["postUrl"]=$aChild->item(0)->getAttribute("href");
//         $jobInfo["company"]=$aChild->item(1)->getText();
//         $jobInfo["companyUrl"]=$aChild->item(1)->getAttribute("href");

//         $spanChidld=$phpider->childDom($temp)->selector("span");
//         $jobInfo["position"]=$spanChidld->item(2)->getText();
//         $jobInfo["salary"]= $spanChidld->item(3)->getText();
//         $jobInfo["addDate"]= $spanChidld->item(4)->getText();
//         $job51Php[]=$jobInfo;
//     }
// }
// if(!empty($job51Php)){
//     $htmlTable="";
//     foreach ($job51Php as $jobInfo) {
//         $htmlTable.="<tr>";
//         foreach ($jobInfo as $subJobInfo) {
//             $htmlTable.="<td>{$subJobInfo}</td>";
//         }
//         $htmlTable.="</tr>";
//     }
//     $htmlTable="<table><tr><th>职位名称</th><th>职位链接</th><th>公司名称</th><th>公司链接</th><th>地区</th><th>薪资</th><th>发布日期</th></tr>{$htmlTable}</table>";
//     file_put_contents("job51Table.html",utf8Html($htmlTable));
// }

// //爬京东 书籍
// $phpider->request_http(["url"=>"https://www.jd.com"]);//先get下首页获取cookie，不get也无所谓
// $postName="web开发";//要爬的关键字
// $product=[];//最终存储的数组

// function getProduct($html){//定义一个函数来处理产品信息
//     global $phpider,$product;
//     $items=$phpider->createDom($html)->selector(".gl-item","li");//获取所有产品的节点
//     for ($i=0; $i < $items->length; $i++) { 
//         $subItem=$items->item($i);
//         $datasku= $subItem->getAttribute("data-sku");
//         $subDom=$phpider->childDom($subItem->writeHtml());//每个产品节点

//         $product[$datasku]["datasku"]=$datasku;
//         $aChild=$subDom->selector("a");
//         $product[$datasku]["url"]=protocol($aChild->item(0)->getAttribute("href"),"https:");//京东指定链接
//         $product[$datasku]["author"]=$aChild->item(2)->getAttribute("title");//作者信息
//         $product[$datasku]["store"]=$aChild->item(3)->getAttribute("title");//出版商

//         $emChild=$subDom->selector("em");
//         $product[$datasku]["name"]=$emChild->item(1)->getText();//书名

//         $iChild=$subDom->selector("i");
//         $product[$datasku]["price"]=$iChild->item(0)->getText();//价格

//         $imgs=$subDom->getImgs();
//         $product[$datasku]["img"]=protocol($imgs[0],"https://");//产品图片
//     }
// }
// $jdHtml=$phpider->request_http(["url"=>"https://search.jd.com/Search?keyword=".urlencode($postName)."&enc=utf-8&wq=".urlencode($postName).""]);//这里获取京东初始化查找的第一次数据，京东第一次默认获取30个产品，
// // file_put_contents("jdpage.html",$jdHtml);//可以把获取到的数据写到文件中，也可以不写。

// preg_match_all("/log_id:[\'\"]?([\d\.]+)[\'\"]?/",$jdHtml,$match);//获取一个log_id在第二次请求时需要使用，
// $log_id=isset($match[1][0])?$match[1][0]:'';

// $show_items="";
// getProduct($jdHtml);//第一次处理产品数据了
// $show_items=implode(",",array_column($product,"datasku"));//这个值也是第二次请求的时候需要的数据

// $jd2Html=$phpider->request_http(["url"=>"https://search.jd.com/s_new.php?keyword=".urlencode($postName)."&enc=utf-8&qrst=1&rt=1&stop=1&vt=2&wq=".urlencode($postName)."&page=2&s=27&scrolling=y&log_id={$log_id}&tpl=2_M&show_items={$show_items}","referer"=>"https://search.jd.com/Search?keyword=".urlencode($postName)."&enc=utf-8&wq=".urlencode($postName).""]);//这里发送第二次请求，注意，要带上referer，不然是获取不到数据的。
// // file_put_contents("jdpage2.html",$jd2Html);//同样可以把它写到文件中，也可以不写。只是作为验证数据而已。
// getProduct(utf8Html($jd2Html));//处理第二次的数据，要使用utf8Html，因为第二次请求的html是没有html、head的标签
// //下面是开始输出为表格了。
// if(!empty($product)){
//     $htmlTable="";
//     foreach ($product as $Info) {
//         $htmlTable.="<tr>";
//         $htmlTable.="<td>{$Info['datasku']}</td><td>{$Info['name']}</td><td>{$Info['author']}</td><td>{$Info['store']}</td><td>{$Info['price']}</td><td>{$Info['img']}</td><td>{$Info['url']}</td>";
//         $htmlTable.="</tr>";
//     }
//     $htmlTable="<table><tr><th>编号</th><th>书名</th><th>作者</th><th>出版社</th><th>价格</th><th>图片</th><th>京东链接</th></tr>{$htmlTable}</table>";
//     file_put_contents("jdTable.html",utf8Html($htmlTable));
// }
