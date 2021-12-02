<?php


namespace Strays\DcatAdminRedis\Http\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Layout\Content;
use Redis;
use Strays\DcatAdminRedis\Actions\DeleteActions;
use Strays\DcatAdminRedis\Actions\EditActions;
use Strays\DcatAdminRedis\RedisManager;
use Strays\DcatAdminRedis\Repository\RedisRepository;

class RedisController extends DcatAdminRedisController
{
    /**
     * 列表
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content->title('数据管理')
            ->description()
            ->body($this->grid());
    }

    /**
     * 编辑
     * @param $key
     * @param Content $content
     * @return Content
     */
    public function edit($key, Content $content)
    {
        return $content->header('edit')
            ->description()
            ->body(
                Form::make(
                    new RedisRepository(),
                    function (Form $form) use ($key) {
                        $form->edit($key);
                        $form->text('key');
                        $form->number('ttl');
                        $form->textarea('value');

                        $form->footer(
                            function ($footer) {
                                $footer->disableReset();
                                $footer->disableViewCheck();
                                $footer->disableEditingCheck();
                                $footer->disableCreatingCheck();
                                $footer->defaultViewChecked();
                                $footer->defaultEditingChecked();
                                $footer->defaultCreatingChecked();
                            }
                        );
                    }
                )
            );
    }

    public function update($key)
    {
        $this->manager()->update(
            array_merge(
                request()->all(),
                [
                    'origin' => $key,
                ]
            )
        );
        return (new Form())->response()->success('修改成功')->redirect('dcat-admin-redis');
    }

    public function show($key, Content $content)
    {
        return $content->header($key)
            ->description('详情')
            ->body($this->getRedisValueShowView($key));
    }


    public function destroy($key)
    {
        $this->manager()->delKeys(explode(',', $key));

        return (new Form())->response()->success('删除成功')->refresh();
    }

    /**
     * 设置数据源
     * @return Grid
     */
    protected function grid()
    {
        return new Grid(
            new RedisRepository(), function (Grid $grid) {
            $grid->column('key');
            $grid->column('type')->badge(RedisManager::$typeColor);
            $grid->column('ttl');
            $grid->disableCreateButton();
            $grid->actions(
                function (Grid\Displayers\Actions $actions) {
                    $rowArray = $actions->row->toArray();
                    if ($rowArray['type'] === 'string') {
                        $actions->disableView();
                    } else {
                        $actions->disableEdit();
                    }
                }
            );

            $grid->quickSearch(
                function ($model, $query) {
                    $model->where('key', $query);
                }
            )->placeholder('搜索...');
        }
        );
    }

    /**
     * 列表模板
     * @param string $key
     * @return Grid
     */
    protected function getRedisValueShowView(string $key)
    {
        $type = $this->manager()->getConnection()->type($this->manager()->getRedisKey($key));
        switch ($type) {
            case Redis::REDIS_HASH:
                return $this->hashListView($key);
            case Redis::REDIS_SET:
                return $this->setListView($key);
            case Redis::REDIS_ZSET:
                return $this->zSetListView($key);
            case Redis::REDIS_LIST:
                return $this->listView($key);
            default:
        }
    }

    /**
     * Hash 列表模板
     * @param string $key
     * @return Grid
     */
    private function hashListView(string $key)
    {
        return new Grid(
            new RedisRepository(), function (Grid $grid) use ($key) {
            $grid->number('ID');
            $grid->column('key');
            $grid->column('value');
            $grid->disablePagination();
            $grid->disableBatchDelete();
            $grid->model()->setData(
                $grid->model()->repository()->hGet($key)
            );

            $grid->disableCreateButton();
            $grid->actions(
                function (Grid\Displayers\Actions $actions) use ($key) {
                    $actions->disableView();
                    $actions->disableEdit();
                    $actions->disableDelete();
                    $actions->append(new EditActions('Edit', $key, Redis::REDIS_HASH));
                    $actions->append(new DeleteActions('Delete', $key, Redis::REDIS_HASH));
                }
            );
        }
        );
    }

    /**
     * Set 列表模板
     * @param string $key
     * @return Grid
     */
    private function setListView(string $key)
    {
        return new Grid(
            new RedisRepository(), function (Grid $grid) use ($key) {
            $grid->number('ID');
            $grid->column('value');
            $grid->disablePagination();
            $grid->disableBatchDelete();
            $grid->model()->setData(
                $grid->model()->repository()->hGet($key)
            );
            $grid->disableCreateButton();
            $grid->actions(
                function (Grid\Displayers\Actions $actions) use ($key) {
                    $actions->disableView();
                    $actions->disableEdit();
                    $actions->disableDelete();
                    $actions->append(new EditActions('Edit', $key, Redis::REDIS_SET));
                    $actions->append(new DeleteActions('Delete', $key, Redis::REDIS_SET));
                }
            );
        }
        );
    }

    /**
     * @param string $key
     * @return Grid
     */
    private function zSetListView(string $key)
    {
        return new Grid(
            new RedisRepository(), function (Grid $grid) use ($key) {
            $grid->number('ID');
            $grid->column('score');
            $grid->column('member');
            $grid->disablePagination();
            $grid->disableBatchDelete();
            $grid->model()->setData(
                $grid->model()->repository()->zSet($key)
            );
            $grid->disableCreateButton();
            $grid->actions(
                function (Grid\Displayers\Actions $actions) use ($key) {
                    $actions->disableView();
                    $actions->disableEdit();
                    $actions->disableDelete();
                    $actions->append(new EditActions('Edit', $key, Redis::REDIS_ZSET));
                    $actions->append(new DeleteActions('Delete', $key, Redis::REDIS_ZSET));
                }
            );
        }
        );
    }

    /**
     * @param string $key
     * @return Grid
     */
    private function listView(string $key)
    {
        return new Grid(
            new RedisRepository(), function (Grid $grid) use ($key) {
            $grid->number('ID');
            $grid->column('value');
            $grid->disablePagination();
            $grid->disableBatchDelete();
            $grid->model()->setData(
                $grid->model()->repository()->hGet($key)
            );
            $grid->disableCreateButton();
            $grid->actions(
                function (Grid\Displayers\Actions $actions) use ($key) {
                    $actions->disableView();
                    $actions->disableEdit();
                    $actions->disableDelete();
                    // $actions->append(new EditActions('Edit', $key, Redis::REDIS_LIST));
                    // $actions->append(new DeleteActions('Delete', $key, Redis::REDIS_LIST));
                }
            );
        }
        );
    }
}
