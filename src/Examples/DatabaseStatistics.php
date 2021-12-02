<?php

namespace Strays\DcatAdminRedis\Examples;

use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Metrics\Donut;
use Illuminate\Support\Str;
use Strays\DcatAdminRedis\Support\Trans;

class DatabaseStatistics extends Donut
{
    /**
     * @var
     */
    protected $connection;

    /**
     * Service constructor.
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;

        parent::__construct();
    }


    protected function init()
    {
        parent::init();

        $this->title(Trans::get('welcome.statistics'));
    }


    /**
     * 格式化内容
     * @return Grid
     */
    public function renderContent()
    {
        return new Grid(
            null, function (Grid $grid) {
            $grid->column('name')->sortable();
            $grid->column('keys')->sortable();
            $grid->column('expires')->sortable();
            $grid->column('avg')->sortable();

            $grid->disableActions();
            $grid->disablePagination();
            $grid->disableToolbar();
            $grid->disableRowSelector();
            $grid->model()->setData($this->generate());
        }
        );
    }

    /**
     * 设置 信息 源
     * @return array
     */
    protected function generate()
    {
        $data = [];
        $info = $this->connection->getInformation();
        foreach ($info as $key => $value) {
            if (Str::startsWith($key, 'db')) {
                $database = explode(',', $value);
                foreach ($database as $datum) {
                    $statistics = explode('=', $datum);
                    $data[] = [
                        'name' => $key,
                        'keys' => $statistics[1],
                        'expires' => $statistics[1],
                        'avg' => $statistics[1],
                    ];
                    break;
                }
            }
        }
        return $data;
    }
}
