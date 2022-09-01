<?php

namespace App\Blocks;

use Adeliom\Lumberjack\Admin\AbstractBlock;
use Extended\ACF\Fields\Image;

class ImageFullBlock extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Image pleine largeur'),
            'description' => __("Bloc contenant une image de la largeur de l'écran"),
            'post_types' => ["page", "post"],
            'mode' => 'edit'
        ]);
    }

    protected function registerFields(): \Traversable
    {
        yield Image::make(__("Image"), "img")
            ->library("all")
            ->previewSize("medium")
            ->returnFormat('array')
            ->required();
    }
}
