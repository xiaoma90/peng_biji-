SELECT *, ACOS(
            COS(RADIANS(31.202635)) *
            COS(RADIANS(latitude)) *
            COS(RADIANS(longitude) - RADIANS(121.654555)) +
            SIN(RADIANS(31.202635)) *
            SIN(RADIANS(latitude))
          ) * 6378 AS distance
FROM merchant
ORDER BY distance
LIMIT 0, 20;

#套用 Haversine公式 直接用 MySQL 的函数去计算距离再排序。例如：假设我当前位置是 [31.202635, 121.654555] 查询周边的商家，并且按距离排序。
#其中 6378 是地球赤道的半径，如果想调整精度，比如精确到米，可以设置为 6378000，