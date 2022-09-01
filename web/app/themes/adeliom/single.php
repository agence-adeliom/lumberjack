<?php

/**
 * The Template for displaying all single posts
 */

declare(strict_types=1);

namespace App;

use App\Http\Controllers\Controller;
use App\Http\Lumberjack;
use Rareloop\Lumberjack\Exceptions\TwigTemplateNotFoundException;
use Rareloop\Lumberjack\Http\Responses\TimberResponse;
use Rareloop\Lumberjack\Post;
use Timber\Timber;

class SingleController extends Controller
{
    /**
     * @throws TwigTemplateNotFoundException
     */
    public function handle(): TimberResponse
    {
        $context = Timber::get_context();
        $post = new Post();

        $context['post'] = $post;
        $context['title'] = $post->title;
        $context['content'] = $post->content;

        $template = Lumberjack::passwordRender('templates/standard/standard.html.twig', (int)$post->ID);
        return new TimberResponse($template, $context);
    }
}
