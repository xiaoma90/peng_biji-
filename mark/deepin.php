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
 
4 安装deb包
    Ubuntu16.04安装deb包
    在Ubuntu下安装deb包需要使用dpkg命令.
    Dpkg 的普通用法：
    1、sudo dpkg -i <package.deb>
    安装一个 Debian 软件包，如你手动下载的文件。
    2、sudo dpkg -c <package.deb>
    列出 <package.deb> 的内容。
    3、sudo dpkg -I <package.deb>
    从 <package.deb> 中提取包裹信息。
    4、sudo dpkg -r <package>
    移除一个已安装的包裹。
    5、sudo dpkg -P <package>
    完全清除一个已安装的包裹。和 remove 不同的是，remove 只是删掉数据和可执行文件，purge 另外还删除所有的配制文件。
    6、sudo dpkg -L <package>
    列出 <package> 安装的所有文件清单。同时请看 dpkg -c 来检查一个 .deb 文件的内容。
    7、sudo dpkg -s <package>
    显示已安装包裹的信息。同时请看 apt-cache 显示 Debian 存档中的包裹信息，以及 dpkg -I 来显示从一个 .deb 文件中提取的包裹信息。
    8、sudo dpkg-reconfigure <package>
    重新配制一个已经安装的包裹，如果它使用的是 debconf (debconf 为包裹安装提供了一个统一的配制界面)。
    
    如果安装过程中出现问题,可以先使用命令:
    sudo apt-get update
    更新后再执行上面的命令
EOT;
