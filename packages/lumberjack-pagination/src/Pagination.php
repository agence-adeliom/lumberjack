<?php

namespace Adeliom\Lumberjack\Pagination;

trait Pagination
{
    abstract public static function query($args = null);

    public static function paginate($perPage = 10, $args = [])
    {
        global $paged;
        if (!isset($paged) || !$paged) {
            $paged = 1;
        }

        $args = array_merge($args, [
            'posts_per_page' => $perPage,
            'paged' => $paged
        ]);

        // Pagination requires wordpress's query_posts method instead of Timber's.
        query_posts($args);

        return static::query($args);
    }
}
