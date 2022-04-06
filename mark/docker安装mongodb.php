<?php
/**
 * mongodb


docker pull mongo:latest
docker images
 * 启动mongodb服务器
1)docker run -itd --name mongo -p 27017:27017 mongo --auth
 * 挂载数据目录 到本地
 * 2)docker run --name mymongo -v /mymongo/data:/data/db -p 27017:27017 -d mongo:latest
#-p 27017:27017 ：映射容器服务的 27017 端口到宿主机的 27017 端口。外部可以直接通过 宿主机 ip:27017 访问到 mongo 的服务。
#--auth：需要密码才能访问容器服务。如果加需要验证就加--auth，不需要验证，就去掉。默认mongodb是不使用用户认证
 * -d 后台运行
 * docker ps 查看容器状态
 * docker logs mymongo
docker exec -it mongo mongo admin
# 创建一个名为 admin，密码为 123456 的用户。
>  db.createUser({ user:'admin',pwd:'123456',roles:[ { role:'userAdminAnyDatabase', db: 'admin'},"readWriteAnyDatabase"]});
# 尝试使用上面创建的用户信息进行连接。
> db.auth('admin', '123456')

 *
 *
 *
 */

/**
 * mongo-express
 *
 * 下载镜像
 * docker pull mongo-express
 *
 * 运行mongo-express
 * docker run --link mymongo:mongo -p 8081:8081 mongo-express
 *
 *
 *
 */