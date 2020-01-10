#!/bin/bash
path="/www/wwwroot/lechuang/public_html/uploads/image/"
path2="oss://oss-huancheng/uploads/image/"
files=$(ls $path)
for filename in $files
do

 if test $[filename] -gt 20190807 
 then
  echo $filename
  gen_path=$path$filename"/"
  gen_path2=$path2$filename"/"
  ./ossutil64 cp -r -f ${gen_path} ${gen_path2}
  
 fi 

done
