<?php

namespace Strays\DcatAdminRedis\Repository;

use Dcat\Admin\Contracts\Repository;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Illuminate\Support\LazyCollection;
use Strays\DcatAdminRedis\RedisManager;
use Strays\DcatAdminRedis\Support\Paginator;

class RedisRepository implements Repository
{
    protected $connection;

    public function __construct()
    {
        $this->connection = RedisManager::instance();
    }

    public function getKeyName()
    {
        return 'key';
    }

    public function getCreatedAtColumn()
    {
        return null;
    }

    public function getUpdatedAtColumn()
    {
        return null;
    }

    public function isSoftDeletes()
    {
        return false;
    }

    public function get(Grid\Model $model)
    {
        $page = $model->filter()->input('page');
        $pattern = $model->filter()->input('_search_', '*');
        $count = $model->filter()->input('per_page', 20);
        $newPage = $model->filter()->input('new_page');
        // 不知道什么原因，第一次迭代时，0必须是 string ，后续迭代为 int
        if ($newPage) {
            $newPage = (int)$newPage;
        } else {
            $newPage = '0';
        }

        $result = $this->connection->scan($newPage, $pattern, $count);

        return new Paginator(
            $result['data'],
            $count,
            $newPage ?: $result['cursor'],
            $result['cursor'],
            $page
        );
    }

    public function edit(Form $form)
    {
        $value = $this->connection->fetch($form->getKey());
        $ttl = $this->connection->ttl($form->getKey());

        return [
            'key' => $form->getKey(),
            'ttl' => $ttl,
            'value' => $value,
        ];
    }

    public function detail(Show $show)
    {
        // TODO: Implement store() method.
    }

    public function store(Form $form)
    {
        // TODO: Implement store() method.
    }

    public function updating(Form $form)
    {
        // TODO: Implement updating() method.
    }

    public function update(Form $form)
    {
        // TODO: Implement deleting() method.
    }

    public function deleting(Form $form)
    {
        // TODO: Implement deleting() method.
    }

    /**
     * @param Form $form
     * @param array $deletingData
     * @return bool
     */
    public function delete(Form $form, array $deletingData)
    {
        // TODO: Implement deleting() method.
    }

    /**
     * @param string $key
     * @return array
     */
    public function hGet(string $key)
    {
        $data = [];
        $chunks = LazyCollection::make($this->connection->fetch($key))
            ->chunk(5)->toArray();

        foreach ($chunks as $chunk) {
            foreach ($chunk as $i => $v) {
                $data[] = [
                    'key' => $i,
                    'value' => $v,
                ];
            }
        }

        return $data;
    }

    /**
     * @param string $key
     * @return array
     */
    public function zSet(string $key)
    {
        $data = [];
        $chunks = LazyCollection::make($this->connection->fetch($key))
            ->chunk(5)->toArray();

        foreach ($chunks as $chunk) {
            foreach ($chunk as $i => $v) {
                $data[] = [
                    'score' => $v,
                    'member' => $i,
                ];
            }
        }

        return $data;
    }

}
