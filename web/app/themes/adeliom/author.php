<?php

/**
 * The template for displaying Author Archive pages
 */

declare(strict_types=1);

namespace App;

use App\Http\Controllers\Controller;
use Rareloop\Lumberjack\Exceptions\TwigTemplateNotFoundException;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\Timber;
use Timber\User as TimberUser;

class AuthorController extends Controller
{
    /**
     * @throws TwigTemplateNotFoundException
     */
    public function handle(): TimberResponse
    {
        global $wp_query;

        $context = Timber::get_context();
        $author = new TimberUser($wp_query->query_vars['author']);

        $context['author'] = $author;
        $context['title'] = 'Author Archives: ' . $author->name();

        $context['posts'] = Post::query([
            'author' => $author->ID,
        ]);

        return new TimberResponse("templates/posts.html.twig", $context);
    }
}
