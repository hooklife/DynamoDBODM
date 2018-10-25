# 查询构造器
可以不创建 model 直接对数据库进行操作

##  DB 与 Model 的区别

DB 类使用基本与 Model 相同，Model 是用 DB 类实现的， 但 Model 的修改器，事件等功能 DB 类无法使用


## Get Started

```php
DB::table('post')->where(['title' => 'test_title', 'id' => [1,2]])->all();
```


## 使用原生语句查询 (不同数据库查询实现有区别，务必谨慎使用)

```php
DB::query([
    'TableName' => 'post',
    'KeyConditionExpression' => '#yr = :yyyy',
    'ExpressionAttributeNames'=> [ '#yr' => 'year' ],
    'ExpressionAttributeValues'=> {
        ":yyyy": 1985 
    }
]);
```
