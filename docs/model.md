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

## 修改器

当用户尝试获取 ```first_name``` 字段时将调用 ```$setField``` 属性 ```$value``` 为要获取字段值， ```$this``` 为当前记录对应的模型

当用户尝试修改 ```first_name``` 字段时将调用 ```$getField``` 属性 ```$value``` 为要修改字段值， ```$this``` 为当前记录对应的模型

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
     * 取值时进行操作的字段
     *
     * @var array
     */
    public static function mutator()
    {
        return [
            'set.first_name' => 'setFirstName',
            'get.first_name'=> function ($value){
                 $this->first_name = strtolower($value);
            }
        ]
    }

    public function setFirstName($value)
    {
        $this->first_name = strtolower($value);
    }

    public function getFirstName($value)
    {
        return strtolower($value);
    }


// Model.initialize
// Model.beforeMarshal
// Model.beforeFind
// Model.beforeSave
// Model.afterSave
// Model.afterSaveCommit
// Model.beforeDelete
// Model.afterDelete
// Model.afterDeleteCommit
    
}
```