<?php

namespace Adeliom\Lumberjack\Pagination;

use Rareloop\Lumberjack\Post;
use Rareloop\Lumberjack\QueryBuilder;
use Tightenco\Collect\Support\Collection;
use Timber\Timber;

class PaginatedQueryBuilder extends QueryBuilder
{
    private $searchTerm;
    private $page;
    protected $postClass = Post::class;

    public function getParameters(): array
    {
        $params = parent::getParameters();

        if (isset($this->page)) {
            $params['paged'] = (int)$this->page;
        }

        return $params;
    }

    /**
     * Use this instead of get()
     */
    public function paginate($perPage = 10, $page = null): Collection
    {
        global $paged;

        if (isset($page)) {
            $paged = $page;
        }

        if (!isset($paged) || !$paged) {
            $paged = 1;
        }

        $this->limit($perPage);
        $this->page($paged);

        query_posts($this->getParameters());

        return $this->get();
    }

    public function page($page)
    {
        $this->page = $page;

        return $this;
    }

    public static function getPagination($prefs = []): array
    {
        return Timber::get_pagination($prefs);
    }
}
