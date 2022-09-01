<?php

/**
 * Search results page
 */

declare(strict_types=1);

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Exceptions\TwigTemplateNotFoundException;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\Timber;

class SearchController extends Controller
{
    /**
     * @throws TwigTemplateNotFoundException
     */
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();
        $searchQuery = get_search_query();

        $context['title'] = "Search results for '" . htmlspecialchars($searchQuery) . "'";
        $context['posts'] = Post::query([
            's' => $searchQuery,
        ]);

        return new TimberResponse('templates/search.html.twig', $context);
    }
}
