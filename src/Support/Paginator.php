<?php

namespace Strays\DcatAdminRedis\Support;

use Illuminate\Pagination\Paginator as BasePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class Paginator extends BasePaginator
{
    public $oldPage;
    public $newPage = 0;

    public function __construct($items, $perPage, $oldPage = 0, $newPage = 0, $currentPage = null, array $options = [])
    {
        parent::__construct($items, $perPage, $currentPage, $options);

        $this->oldPage = $oldPage;
        $this->newPage = $newPage;
    }

    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }
        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        $parameters = [$this->pageName => $page, 'old_page' => $this->oldPage, 'new_page' => $this->newPage];

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return url()->current().$this->path()
            .(Str::contains($this->path(), '?') ? '&' : '?')
            .Arr::query($parameters)
            .$this->buildFragment();
    }

    public function nextPageUrl()
    {
        if ($this->hasMorePages()) {
            return $this->url($this->currentPage());
        }
    }

    public function hasMorePages()
    {
        return $this->currentPage() > 0;
    }
}
