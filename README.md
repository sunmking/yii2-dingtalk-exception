# yii2-dingtalk-exception

## 说明
> yii2 日志自动发送到钉钉 实时监控服务是否运行正常


## 安装

```bash
composer require saviorlv/yii2-dingtalk-exception
```
## 配置

在使用本扩展之前，你需要去 [群机器人](https://open-doc.dingtalk.com/microapp/serverapi2/nr29om) 获取相关信息。


## 使用

> 在config/main.php配置文件中定义component配置信息

```bash
'components' => [
    ......
    'log' => [
                'traceLevel' => YII_DEBUG ? 3 : 0,
                'targets' => [
                    [
                        'class' => 'yii\log\FileTarget',
                        'levels' => ['error', 'warning'],
                    ],
                    [
                        'class' => 'Saviorlv\Log\ExceptionTarget',
                        'levels' => ['error', 'warning'],
                        'options' => [
                            'accessToken' => 'xxxxxxxx',
                            'isAtAll' => false,
                            'atMobiles' => ['136xxxx5134']
                        ],
                    ],
                ],
            ],
    ......
]
```
## 参数

`'isAtAll' => true`  @所有人 <br>
`'atMobiles' => ['136xxxx5134'，'136xxxx5133']` @部分人员
