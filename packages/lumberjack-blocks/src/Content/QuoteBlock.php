<?php

namespace App\Blocks;

use Adeliom\Lumberjack\Admin\AbstractBlock;
use Traversable;
use Extended\ACF\Fields\Textarea;

class QuoteBlock extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Citation'),
            'description' => __('Bloc permettant la mise en avant d’un témoignage ou d’une citation'),
            'mode' => 'edit'
        ]);
    }

    /**
     * @return Traversable
     */
    protected function registerFields(): \Traversable
    {
        yield Textarea::make(__("Citation"), "text")->rows(2)->required();
    }
}
