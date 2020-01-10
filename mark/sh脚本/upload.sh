ls_date=`date +%Y%m%d`
gen1='/www/wwwroot/lechuang/public_html/uploads/image/'$ls_date"/"
gen2='oss://oss-huancheng/uploads/image/'$ls_date"/"
./ossutil64 cp -r -f ${gen1} ${gen2}
rm -rf ${gen1}
