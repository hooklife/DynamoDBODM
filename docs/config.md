# 数据库配置

```php
return [
    'default' =>[
        'driver'  => 'dynamedb',
        'debug'   => false,

        // S3 SDK 参数
        // https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html
        'S3Config' => [
            'key' => '###',
            'secret' => '###',
            'region' => "ap-southeast-1"
            
            // 'endpoint' => 'http://localhost:8000',
            // 'timeout' => 5,
            // 'retries' => 3,
        ]
    ]
]
```
> default 为 默认连接实例

## 增加实例
在配置中增加新增实例other

```php 
return [
    'other' =>[
        'driver'  => 'dynamedb',
        'debug'   => false,

        // S3 SDK 参数
        // https://docs.aws.amazon.com/sdk-for-php/v3/developer-guide/guide_configuration.html
        'S3Config' => [
            'key' => '###',
            'secret' => '###',
            'region' => "ap-southeast-1"
            
            // 'endpoint' => 'http://localhost:8000',
            // 'timeout' => 5,
            // 'retries' => 3,
        ]
    ]
]
```
> 具体实例使用方法请参阅 [...]