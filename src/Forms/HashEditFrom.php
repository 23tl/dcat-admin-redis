<?php

namespace Strays\DcatAdminRedis\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Redis;
use Strays\DcatAdminRedis\RedisManager;
use Strays\DcatAdminRedis\Support\Arr;

class HashEditFrom extends Form implements LazyRenderable
{
    use LazyWidget;

    // 使用异步加载功能

    /**
     * Handle the form request.
     *
     * @return mixed
     */
    public function handle(array $input)
    {
        RedisManager::instance()->getDataType(Redis::REDIS_HASH)->update(
            [
                'hash' => RedisManager::instance()->getRedisKey(Arr::get($this->payload, 'hash', null)),
                'key' => Arr::get($this->payload, 'key', null),
                'value' => Arr::get($input, 'value', null),
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
        $this->text('key')->disable();
        $this->text('value')->required();
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [
            'key' => Arr::get($this->payload, 'key', null),
            'value' => Arr::get($this->payload, 'value', null),
        ];
    }
}
