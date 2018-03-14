<?php
namespace extend;

use think\Model;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;
use PHPExcel_RichText;

class ImportService extends Model
{
    public function index($file)
    {
        $file = iconv("utf-8", "gb2312", $file);   //转码
        if(empty($file) OR !file_exists($file)) {
            die('文件不存在');
        }
        $objRead = new PHPExcel_Reader_Excel2007();   //建立reader对象
        if (!$objRead->canRead($file)) {
            $objRead = new PHPExcel_Reader_Excel5();
            if (!$objRead->canRead($file)) {
                die('创建Excel失败');
            }
        }
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
            'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
            'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ',
            'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV',
            'AW', 'AX', 'AY', 'AZ');
        $PHPExcel = $objRead->load($file);
        //读取excel文件中的第一个工作表
        $currentSheet = $PHPExcel->getSheet(0);
        //取得最大的列号
        $allColumn = $currentSheet->getHighestColumn();
        $columnCnt = array_search($allColumn, $cellName);
        //取得一共有多少行
        $allRow = $currentSheet->getHighestRow();

        $data = array();
        for($_row=2; $_row<=$allRow; $_row++){  //读取内容
            for($_column=0; $_column<=$columnCnt; $_column++){
                $cellId = $cellName[$_column].$_row;
                if($_column!=0){
                    $cellValue = $currentSheet->getCell($cellId)->getValue();
                }else{
                    continue;
                }
                if($cellValue instanceof PHPExcel_RichText){   //富文本转换字符串
                    $cellValue = $cellValue->__toString();
                }
                $data[$_row-2][$cellName[$_column]] = $cellValue;
            }
        }
        return $data;
    }
}