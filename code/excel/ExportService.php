<?php
namespace excel;

use think\Model;
use \PHPExcel_IOFactory;
use \PHPExcel;
class ExportService extends Model
{
    public function index($data,$title,$tableName,$boxTitle)
    {
        $PHPSheet=new \PHPExcel();
        $PHPSheet->createSheet();
        $objSheet = $PHPSheet->getActiveSheet();//获得当前活动Sheet
        $objSheet->setTitle($boxTitle);//当前活动窗口名
        $i=65;
        foreach($title as $k=>$v){
            $ascii=chr($i+$k);
            $objSheet->setCellValue($ascii.'1',$v);
        }
        $m=0;
        foreach($data as $k=>$val){
            $j=$k+2;
            foreach($val as $v){
                $ascii=chr($i+$m);
                $objSheet->setCellValue($ascii.$j,$v);
                $m++;
            }
            $m=0;
        }
        $objWriter = PHPExcel_IOFactory::createWriter($PHPSheet, 'Excel5');//Excel2007
        header('Content-Type: application/vnd.ms-excel');//告诉浏览器将要输出excel03文件
        header('Content-Disposition: attachment;filename="'.$tableName.'"');//告诉浏览器将输出文件的名称(文件下载)
        header('Cache-Control: max-age=0');//禁止缓存
        $objWriter->save("php://output");
    }
}