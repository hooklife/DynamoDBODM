# 修改器与修改器

## 访问器
当你使用 model 获取查询结果中的数据时，访问器可以对你要获取的指定字段的值进行修改。

## 定义一个访问器

在 model 创建 ```getFirstNameField``` 方法，即可对字段 ```first_name``` 字段定义访问器,
定义的方法名采用 get{fieldName}Field 规则命名 ( ```fieldName```为需要定义访问器的字段，字段名称采用驼峰进行命名)。

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
    public function getFirstNameField($value)
    {
        return strtolower($value);
    } 
}
```

## 修改器
当你设置 model 中的某些数据时，修改可以对你要设置的指定字段的值进行修改。

## 定义一个修改器

在 model 创建 ```setFirstNameField``` 方法，即可对字段 ```first_name``` 字段定义修改器,
定义的方法名采用 set{fieldName}Field 规则命名 ( ```fieldName```为需要定义修改器的字段，字段名称采用驼峰进行命名)。


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


    public function setFirstNameField($value)
    {
        $this->first_name = strtolower($value);
    }

}
```