<?php
/** 
 * @Author: vition 
 * @Date: 2018-03-28 14:28:27 
 * @Desc: phpider 例子 
 */
require_once "phpider.php";
$phpider=new phpider(); //实例化phpider


// /*通过get获取网页数据*/
// $resultHtml=$phpider->request_http(["url"=>"http://www.vitionst.com/"]);//正常情况下就可以获取到html文本数据


// /*通过post方式登录github*/
// /*1，获取authenticity_token*/
// $gitHubHtml=$phpider->request_http(["url"=>"https://github.com/login"]);//先获取login页面
// if($gitHubHtml!=""){
//     $inputs=$phpider->readHtml($gitHubHtml)->selector("input");//分析获取页面所有input标签，authenticity_token隐藏在input中
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
//         $profile=$phpider->request_http(["url"=>"https://github.com/vition"]);
//         file_put_contents("profile.html",$profile);
//     }
// }

/*通过get方式获取51job php开发工程师数据*/
// $phpider->request_http(["url"=>"https://www.51job.com"]);//先get下首页获取cookie，不get也无所谓
// $job51Html=$phpider->request_http(["url"=>"https://search.51job.com/list/030200,000000,0000,00,9,99,".urlencode("PHP开发工程师").",2,1.html?lang=c&stype=&postchannel=0000&workyear=99&cotype=99&degreefrom=99&jobterm=99&companysize=99&providesalary=99&lonlat=0%2C0&radius=-1&ord_field=0&confirmdate=9&fromType=&dibiaoid=0&address=&line=&specialarea=00&from=&welfare="]);
// file_put_contents("job51.html",$job51Html);

$job51Html=file_get_contents("job51.html");
$elClass=$phpider->readHtml($job51Html)->selector(".el","div");
$file=fopen("job5129.html","w+");
for ($i=0; $i < $elClass->length ; $i++) { 
    // $theNode->hasAttribute('id')
    if(!$elClass->item($i)->hasAttribute('id')){
        $temp=$elClass->item($i)->writeHml();
        fwrite($file,$temp);
    }
    
    // file_put_contents("51job-{$i}.html",$temp);
    
}
// print_r($phpider->writeHml());
// file_put_contents("512.html",$elClass->writeHml());

