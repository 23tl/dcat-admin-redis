<?php

namespace Strays\DcatAdminRedis\Examples;


use Dcat\Admin\Grid;
use Dcat\Admin\Widgets\Metrics\Donut;
use Strays\DcatAdminRedis\Support\Trans;

class Info extends Donut
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

    /**
     *
     */
    protected function init()
    {
        parent::init();

        $this->title(Trans::get('welcome.info'));
    }


    public function renderContent()
    {
        return new Grid(
            null, function (Grid $grid) {
            $grid->column('name');
            $grid->column('value');

            $grid->disableActions();
            $grid->disablePagination();
            $grid->disableToolbar();
            $grid->disableRowSelector();
            $grid->model()->setData($this->generate());
        }
        );
    }

    /**
     * @return array
     */
    protected function generate()
    {
        $data = [];
        $info = $this->connection->getInformation();
        foreach ($info as $key => $value) {
            $data[] = [
                'name' => $key,
                'value' => $value,
            ];
        }
        return $data;
    }

}
