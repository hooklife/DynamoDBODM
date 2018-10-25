# 查询

## 多条与单条数据查询
```php
// select * from post limit 1
$result = Post::one();
// select * from post
$result = Post::all();
// select * from post limit 10
$result = Post::limit(10)->all();
```

## 过滤
```php
// select * from post where title='test_title' and id in (1,2)
$result = Post::where(['title' => 'test_title', 'id' => [1,2]])->all();

// select * from post where title='test_title' and id > 2
$result = Post::where(['title' => 'test_title', ['id', '>', 2]])->all();

// select * from post where title like '%swoft%'
$result = Post::where('title','like','%swoft%')->all();
```


## 排序
```php
// select * from post order by create_time desc
$result = Post::orderBy(['create_time'=> 'desc'])->all();
// select * from post order by create_time desc
$result = Post::orderBy('create_time', 'desc')->all();
// select * from post order by create_time desc,update_time desc
$result = Post::orderBy(['create_time'=> 'desc','update_time'=>'desc'])->all();
```

## 运算
```php
// select count(id) from post
$result = Post::count('id');
// select sum(id) from post
$result = Post::sum('id');
// select max(id) from post
$result = Post::max('id');
// select avg(id) from post
$result = Post::avg('id');
```


## 删除数据

