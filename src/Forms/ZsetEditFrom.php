<?php

namespace Strays\DcatAdminRedis\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Redis;
use Strays\DcatAdminRedis\RedisManager;
use Strays\DcatAdminRedis\Support\Arr;

class ZsetEditFrom extends Form implements LazyRenderable
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
        RedisManager::instance()->getDataType(Redis::REDIS_ZSET)->update(
            [
                'hash' => RedisManager::instance()->getRedisKey(Arr::get($this->payload, 'hash', null)),
                'oScore' => Arr::get($this->payload, 'score', 0),
                'oMember' => Arr::get($this->payload, 'member', null),
                'score' => Arr::get($input, 'score', 0),
                'member' => Arr::get($input, 'member', null),
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
        $this->textarea('member')->required();
        $this->text('score')->required();
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
        return [
            'score' => Arr::get($this->payload, 'score', null),
            'member' => Arr::get($this->payload, 'member', null),
        ];
    }
}
