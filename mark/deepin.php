<?php
<<<EOT
1,wifi 感叹号处理方法
    /etc/NetworkManager/NetworkManager.conf

    # [connectivity]
    # uri=http://packages.deepin.com/misc/check_network_status.txt

    #掉这两行，不会再检查互联网连接了
2，WiFi网速慢问题 -- 正在解决
    
3，linux更改源

    Linux Deepin 更新源列表配置文件为 /etc/apt/sources.list
    
    deb http://packages.linuxdeepin.com/ubuntu precise main restricted universe multiverse
    deb http://packages.linuxdeepin.com/ubuntu precise-security main restricted universe multiverse
    deb http://packages.linuxdeepin.com/ubuntu precise-updates main restricted universe multiverse
    #deb http://packages.linuxdeepin.com/ubuntu precise-proposed main restricted universe multiverse
    #deb http://packages.linuxdeepin.com/ubuntu precise-backports main restricted universe multiverse
     
    deb-src http://packages.linuxdeepin.com/ubuntu precise main restricted universe multiverse
    deb-src http://packages.linuxdeepin.com/ubuntu precise-security main restricted universe multiverse
    deb-src http://packages.linuxdeepin.com/ubuntu precise-updates main restricted universe multiverse
    #deb-src http://packages.linuxdeepin.com/ubuntu precise-proposed main restricted universe multiverse
    #deb-src http://packages.linuxdeepin.com/ubuntu precise-backports main restricted universe multiverse
     
    deb http://mirrors.cicku.me/deepin precise main non-free
    deb-src http://mirrors.cicku.me/deepin precise main non-free
     
    deb http://mirrors.cicku.me/deepin precise-updates main non-free
    deb-src http://mirrors.cicku.me/deepin precise-updates main non-free
    
    ####Linux Deepin 特有源####
    deb http://packages.linuxdeepin.com/deepin precise main non-free
    deb-src http://packages.linuxdeepin.com/deepin precise main non-free
     
    deb http://packages.linuxdeepin.com/deepin precise-updates main non-free
    deb-src http://packages.linuxdeepin.com/deepin precise-updates main non-free
    
    
    sudo apt-get update #必须对列表刷新
EOT;
