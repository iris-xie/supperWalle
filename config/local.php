<?php
// Uncomment to enable debug mode. Recommended for development.
defined('YII_DEBUG') or define('YII_DEBUG', false);

// Uncomment to enable dev environment. Recommended for development
defined('YII_ENV') or define('YII_ENV', 'prod');

return [
    'components' => [
        'db' => [
            'dsn'       => isset($_ENV['WALLE_DB_DSN'])  ? $_ENV['WALLE_DB_DSN']  : 'mysql:host=127.0.0.1;dbname=walle',
            'username'  => isset($_ENV['WALLE_DB_USER']) ? $_ENV['WALLE_DB_USER'] : 'xielei',
            'password'  => isset($_ENV['WALLE_DB_PASS']) ? $_ENV['WALLE_DB_PASS'] : 'a918405',
        ],
        'mail' => [
            'transport' => [
                'host'       => isset($_ENV['WALLE_MAIL_HOST']) ? $_ENV['WALLE_MAIL_HOST'] : 'smtp.qq.com',     # smtp 发件地址
                'username'   => isset($_ENV['WALLE_MAIL_USER']) ? $_ENV['WALLE_MAIL_USER'] : '443833189@qq.com',  # smtp 发件用户名
                'password'   => isset($_ENV['WALLE_MAIL_PASS']) ? $_ENV['WALLE_MAIL_PASS'] : 'a918405',       # smtp 发件人的密码
                'port'       => isset($_ENV['WALLE_MAIL_PORT']) ? $_ENV['WALLE_MAIL_PORT'] : 465,                       # smtp 端口
                'encryption' => isset($_ENV['WALLE_MAIL_ENCRYPTION']) ? $_ENV['WALLE_MAIL_ENCRYPTION'] : 'tls',                    # smtp 协议
            ],
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from'    => [
                  (isset($_ENV['WALLE_MAIL_EMAIL']) ? $_ENV['WALLE_MAIL_EMAIL'] : '443833189@qq.com') => (isset($_ENV['WALLE_MAIL_NAME']) ? $_ENV['WALLE_MAIL_NAME'] : '向同志们问好'),
                ],  # smtp 发件用户名(须与mail.transport.username一致)
            ],
        ],
        'request' => [
            'cookieValidationKey' => 'PdXWDAfV5-gPJJWRar5sEN71DN0JcDRV',
        ],
    ],
    'language'   => isset($_ENV['WALLE_LANGUAGE']) ? $_ENV['WALLE_LANGUAGE'] : 'zh-CN', // zh-CN => 中文,  en => English
];
