<?php 
//+++++++++++++++++++++++++主功能实现模块（获取到公司信息）+++++++++++++++++++++++++++++++
set_time_limit(0);
//目标网站地址
$arr = get_companyHttp();//调用了下面的方法
//循环公司主页地址得到对应信息
foreach ($arr as $url) {
	//获得网站内容
	$html = file_get_contents($url); 
	//正则匹配，获取目标内容
	$isMatched = preg_match( "/<div id=\"lianxi-whole\">(.+?)<div id=\"youqing-whole\">/is", $html, $matches);	
	//将所需信息以数组的形式输出
	$text[] = $matches[1];
}

//var_dump($text);



/**
*获得企业名链接（因为企业地址信息没有可控共同点，因此需要从上一级获取当页所有公司链接）
*/
function get_companyHttp(){
	for ($page=1; $page <=5 ; $page++) { //页码

		$url = "http://search.114chn.com/searchresult.aspx?type=1&areaid=41&pattern=2&page=".$page;
		$html = file_get_contents($url);
		$dom = new DOMDocument();
		@$dom->loadHTML($html);
		// grab all the on the page
		$xpath = new DOMXPath($dom);
		$hrefs = $xpath->evaluate("/html/body//a");
			for ($i = 0; $i < $hrefs->length; $i++) {
			       $href = $hrefs->item($i);
			       $url = $href->getAttribute('href');
			       $result = strpos($url,'shopid');
				       if($result!==false){
							$arrHttp[]=$url;
				       }      
			}
		}
		return $arrHttp;
	}

/*
未解决问题：
1.本电脑运行不了大量数据检索
2.数据导出还未解决
3.数据的再分类	
*/
