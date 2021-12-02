<?php

namespace Strays\DcatAdminRedis\Actions;

use Dcat\Admin\Grid\RowAction;
use Illuminate\Http\Request;
use Redis;
use Strays\DcatAdminRedis\RedisManager;

class DeleteActions extends RowAction
{
    protected $hash, $type;

    public function __construct($title = null, $hash = null, $type = null)
    {
        parent::__construct($title);

        $this->hash = $hash;
        $this->type = $type;
    }

    public function html()
    {
        $this->defaultHtmlAttribute('href', 'javascript:void(0)');

        return <<<HTML
<a {$this->formatHtmlAttributes()}><i class="feather icon-trash grid-action-icon" title="{$this->title(
        )}"></i> &nbsp;</a>
HTML;
    }

    public function confirm()
    {
        return [
            "您确定要删除这行数据吗？",
        ];
    }

    public function handle(Request $request)
    {
        switch ($request->input('type')) {
            case Redis::REDIS_HASH:
                $key = $request->input('key');
                break;
            case Redis::REDIS_LIST:
            case Redis::REDIS_SET:
                $key = $request->input('value');
                break;
            case Redis::REDIS_ZSET:
                //$key = $request->input('member');
                return $this->response()->error('暂未开放')->refresh();
            default:
                return $this->response()->error('删除失败')->refresh();
        }

        RedisManager::instance()->getDataType($request->input('type'))->remove(
            [
                'hash' => RedisManager::instance()->getRedisKey($request->input('hash')),
                'key' => $key,
            ]
        );

        return $this->response()->success('删除成功')->refresh();
    }


    public function parameters()
    {
        return [
            'key' => $this->row->key,
            'hash' => $this->hash,
            'type' => $this->type,
            'value' => $this->row->value,
            'member' => $this->row->member,
        ];
    }
}
