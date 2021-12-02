<?php

namespace Strays\DcatAdminRedis\Actions;

use Dcat\Admin\Grid\RowAction;
use Dcat\Admin\Widgets\Modal;
use Redis;
use Strays\DcatAdminRedis\Forms\HashEditFrom;
use Strays\DcatAdminRedis\Forms\SetEditFrom;
use Strays\DcatAdminRedis\Forms\ZsetEditFrom;

class EditActions extends RowAction
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
<a {$this->formatHtmlAttributes()}><i title="{$this->title(
        )}" class="feather icon-edit-1 grid-action-icon"></i> &nbsp;</a>
HTML;
    }

    public function render()
    {
        return Modal::make()
            ->lg()
            ->title($this->title())
            ->body($this->setFormView())
            ->button($this->html());
    }

    public function parameters()
    {
        return [
            'hash' => $this->row->hash,
            'key' => $this->row->key,
            'value' => $this->row->value,
            'type' => $this->row->type,
            'score' => $this->row->score,
            'member' => $this->row->member,
        ];
    }

    private function setFormView()
    {
        switch ($this->type) {
            case Redis::REDIS_HASH:
                return HashEditFrom::make()->payload(
                    [
                        'hash' => $this->hash,
                        'key' => $this->row->key,
                        'value' => $this->row->value,
                    ]
                );

            case Redis::REDIS_SET:
                return SetEditFrom::make()->payload(
                    [
                        'hash' => $this->hash,
                        'key' => $this->row->value,
                    ]
                );
            case Redis::REDIS_ZSET:
                return ZsetEditFrom::make()->payload(
                    [
                        'hash' => $this->hash,
                        'score' => $this->row->score,
                        'member' => $this->row->member,
                    ]
                );
            case Redis::REDIS_LIST:
            default:
        }
    }
}
