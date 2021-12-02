<?php

namespace Strays\DcatAdminRedis\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Redis;
use Strays\DcatAdminRedis\RedisManager;
use Strays\DcatAdminRedis\Support\Arr;

class SetEditFrom extends Form implements LazyRenderable
{
    use LazyWidget;

    // 使用异步加载功能

    /**
     * Handle the form request.
     *
     * @param array $input
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        RedisManager::instance()->getDataType(Redis::REDIS_SET)->update(
            [
                'hash' => RedisManager::instance()->getRedisKey(Arr::get($this->payload, 'hash', null)),
                'old' => Arr::get($this->payload, 'key', null),
                'new' => Arr::get($input, 'value'),
            ]
        );

        return $this
            ->response()
            ->success('Processed successfully.')
            ->refresh();
    }

    /**
     * Build a form here.
     */
    public function form()
    {
        $this->textarea('value')->required();
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [
            'value' => Arr::get($this->payload, 'key', null),
        ];
    }
}
