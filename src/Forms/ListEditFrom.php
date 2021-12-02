<?php

namespace Strays\DcatAdminRedis\Forms;

use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;

class listEditFrom extends Form implements LazyRenderable
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
    }

    /**
     * The data of the form.
     *
     * @return array
     */
    public function default()
    {
    }
}
