# 模型定义

## 基础模型

使用模型中 ```$table``` 属性来指定模型使用的数据表
```php
use Hooklife\DynameDB\Model;

class Post extends Model
{
    /**
     * 模型使用的数据表
     *
     * @var string
     */
    public $table = 'posts';
}
```

## 数据库连接

默认情况下，模型将使用配置中的 ```default``` 为数据库连接。如果想使用其他连接，可以使用模型中的 ```$connection``` 属性实现

```php
use Hooklife\DynameDB\Model;

class Post extends Model
{
     /**
     * 模型使用的数据表
     *
     * @var string
     */
    public $table = 'posts';

    /**
     * 指定连接
     *
     * @var string
     */
    public $connection = 'other';
}
```

