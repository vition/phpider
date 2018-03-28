# phpider
phpider爬虫，php curl 获取http网页，通过 DOMDocument类分析网页从而得到所需要的数据

## 例子

```
require_once "phpider.php";
$phpider=new phpider(); //实例化phpider
```

### 通过get获取网页数据
```
$resultHtml=$phpider->request_http(["url"=>"http://www.vitionst.com/"]);//正常情况下就可以获取到html文本数据
```
