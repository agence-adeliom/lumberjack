<?php

namespace Adeliom\Lumberjack\Pagination;

use Rareloop\Lumberjack\Post;
use Rareloop\Lumberjack\QueryBuilder;
use Tightenco\Collect\Support\Collection;
use Timber\Timber;

class PaginatedQueryBuilder extends QueryBuilder
{
    private ?int $page;

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
    public function paginate(int $perPage = 10, ?int $page = null): Collection
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

    public function page(int $page)
    {
        $this->page = $page;

        return $this;
    }

    public static function getPagination(array $prefs = []): array
    {
        return Timber::get_pagination($prefs);
    }
}
