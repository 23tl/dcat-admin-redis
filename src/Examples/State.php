<?php

namespace Strays\DcatAdminRedis\Examples;

use Dcat\Admin\Widgets\Metrics\Donut;
use Illuminate\Support\Arr;
use Strays\DcatAdminRedis\Support\Trans;

class State extends Donut
{
    /**
     * @var
     */
    protected $connection;

    /**
     * Service constructor.
     *
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

        $this->title(Trans::get('welcome.state'));
    }

    public function renderContent()
    {
        return <<<HTML
<div class="col-sm-6 justify-content-center">
    <div class="d-flex pl-1 pr-1 pt-1" style="margin-bottom: 8px">
        <div style="width: 120px">
            {$this->getTransKey('welcome.client')}
        </div>
        <div>{$this->getRedisKey('connected_clients')}</div>
    </div>
    <div class="d-flex pl-1 pr-1" style="margin-bottom: 8px">
        <div style="width: 120px">
           {$this->getTransKey('welcome.connect')}
        </div>
        <div>{$this->getRedisKey('total_connections_received')}</div>
    </div>
    <div class="d-flex pl-1 pr-1" style="margin-bottom: 8px">
        <div style="width: 120px">
            {$this->getTransKey('welcome.history')}
        </div>
        <div>{$this->getRedisKey('total_commands_processed')}</div>
    </div>
</div>
HTML;
    }

    /**
     * @return array|\ArrayAccess|mixed
     */
    protected function getRedisKey(string $key)
    {
        $info = $this->connection->getInformation();

        return Arr::get($info, $key);
    }

    /**
     * 兼容使用（在 renderContent 中无法使用 静态方法）.
     *
     * @return string
     */
    protected function getTransKey(string $key)
    {
        return Trans::get($key);
    }
}
