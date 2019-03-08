<?php
/**
 * Created by PhpStorm.
 * User: mpc
 * Date: 2019/3/8
 * Time: 17:03
 */
function vote($content='')
{
    //提取图片路径的src的正则表达式
    preg_match_all("/<img(.*)src=\"([^\"]+)\"[^>]+>/isU",$content,$matches);

    $img = "";
    if(!empty($matches)) {
        //注意，上面的正则表达式说明src的值是放在数组的第三个中
        $img = $matches[2];
    }else {
        $img = "";
    }
    if (!empty($img)) {
        $img_url = WEB_URL;

        $patterns= array();
        $replacements = array();

        foreach($img as $imgItem){

            $domain = strstr($imgItem, $img_url);
            if(!$domain){
                $final_imgUrl = $img_url.$imgItem;
                $replacements[] = $final_imgUrl;

                $img_new = "/".preg_replace("/\//i","\/",$imgItem)."/";
                $patterns[] = $img_new;
            }
        }

        //让数组按照key来排序
        ksort($patterns);
        ksort($replacements);

        //替换内容
        $vote_content = preg_replace($patterns, $replacements, $content);
        return  $vote_content;
    }
}
