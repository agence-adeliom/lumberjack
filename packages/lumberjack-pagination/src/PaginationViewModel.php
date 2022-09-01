<?php

namespace Adeliom\Lumberjack\Pagination;

use Rareloop\Lumberjack\ViewModel;

class PaginationViewModel extends ViewModel
{
    /**
     * @var array<string, mixed>|mixed
     */
    public $pagination;

    public static function fromQueryBuilder(int $resultsPerPage = 10, int $forPage = 1): self
    {
        $pagination = PaginatedQueryBuilder::getPagination();

        return new self(
            $resultsPerPage,
            $forPage,
            $pagination['current'],
            $pagination['total'],
            $pagination['pages'],
            $pagination['prev'],
            $pagination['next']
        );
    }

    public function __construct(int $resultsPerPage, int $forPage, int $current, int $total, $pages = [], $prev = [], $next = [])
    {
        $this->pagination = [
            'current' => $current,
            'total' => $total,
            'pages' => $pages,
            'next' => $next,
            'prev' => $prev,
            'per_page' => $resultsPerPage,
            'for_page' => $forPage,
        ];
    }

    /**
     * @return mixed[]
     */
    public function toArray(): array
    {
        return $this->pagination;
    }
}
