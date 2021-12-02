<?php

namespace Strays\DcatAdminRedis;

use Dcat\Admin\Extend\ServiceProvider;

class DcatAdminRedisServiceProvider extends ServiceProvider
{
    protected $js = [
        'js/index.js',
    ];
    protected $css = [
        'css/index.css',
    ];

    protected $menu = [
        [
            'title' => 'Dcat Redis',
            'uri' => 'dcat-admin-redis',
            'icon' => ''
        ],
        [
            'parent' => 'Dcat Redis', // 指定父级菜单
            'title' => 'statistics',
            'uri' => 'dcat-admin-redis/welcome',
        ],
        [
            'parent' => 'Dcat Redis', // 指定父级菜单
            'title' => 'database',
            'uri' => 'dcat-admin-redis',
        ]
    ];

    public function register()
    {
    }

    public function init()
    {
        parent::init();
    }
}
