# 插入数据

## 方式一
```php
$post = new Post;
$post->title = "标题";
$post->save();
```

## 方式二
```php
$post = Post::insert([
    'title'=> "标题"
]);
```