<?php
<<<EOT
deepin 使用命令 https://github.com/jingle0927/blog/blob/master/source/_posts/deepin-command.md
1,wifi 感叹号处理方法
    /etc/NetworkManager/NetworkManager.conf

    # [connectivity]
    # uri=http://packages.deepin.com/misc/check_network_status.txt

    #掉这两行，不会再检查互联网连接了
2，WiFi网速慢问题 -- 正在解决
    
3，linux更改源

    Linux Deepin 更新源列表配置文件为 /etc/apt/sources.list
    首先，点击任务栏上的”控制中心“
    在”控制中心“中点击”更新“按钮。
    在”更新“中点击”更新设置“
    在”更新设置“中点击”切换镜像源“
    在”切换镜像源“中可以先进行”测速“，确定下载速度最快的软件源。
    要设置，只需要选中相应的软件源即可，如下图所示，选中了”网易“的软件源
    设置完软件源后，需要更新软件列表，同时按CTRL+ALT+T键，打开终端。
    在终端中执行如下命令即可更新软件列表sudo apt-get update  #必须对列表刷新
    
EOT;
