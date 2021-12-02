<?php

namespace Strays\DcatAdminRedis\Http\Controllers;

use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use Strays\DcatAdminRedis\Examples\DatabaseStatistics;
use Strays\DcatAdminRedis\Examples\Info;
use Strays\DcatAdminRedis\Examples\Ram;
use Strays\DcatAdminRedis\Examples\Service;
use Strays\DcatAdminRedis\Examples\State;

class WelcomeController extends DcatAdminRedisController
{
    public function index(Content $content)
    {
        $connection = $this->manager();

        return $content
            ->header('数据统计')
            ->description('')
            ->body(
                function (Row $row) use ($connection) {
                    $row->column(4, new Service($connection));
                    $row->column(4, new Ram($connection));
                    $row->column(4, new State($connection));
                }
            )
            ->body(
                function (Row $row) use ($connection) {
                    $row->column(12, new DatabaseStatistics($connection));
                }
            )
            ->body(
                function (Row $row) use ($connection) {
                    $row->column(12, new Info($connection));
                }
            );
    }
}
