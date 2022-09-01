<?php

namespace App\Blocks;

use Adeliom\WP\Extensions\Blocks\AbstractBlock;
use App\Enum\GutBlockName;
use App\Helpers\Gutenberg\GutenbergBlockHelper;
use Rareloop\Lumberjack\Post;
use Traversable;
use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\Message;
use Extended\ACF\Fields\Relationship;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;

/**
 * Class QuoteBlock
 * @see https://github.com/wordplate/extended-acf#fields
 * @package App\FlexibleLayout
 */
class LastNews extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Dernières actualités'),
            'description' => __("Bloc permettant d'afficher vos dernières actualités publiées"),
            'category' => GutBlockName::LISTING,
            'post_types' => GutenbergBlockHelper::commonsTemplate(),
            'mode' => 'edit',
            'dir' => BlocksTwigPath::LISTING
        ]);
    }

    /**
     * @return Traversable
     */
    protected function registerFields(): \Traversable
    {
        yield Text::make(__("Titre"), "title");
        yield Message::make(__("Ce bloc remonte automatiquement vos dernières actualités publiées."), "msg");
        yield TrueFalse::make(__("Je souhaite sélectionner manuellement les actualités à remonter"), "is_custom_news")->defaultValue(false)->stylisedUi();
        yield Relationship::make(__('Sélectionnez les actualités à afficher'), 'custom_last_news')
            ->postTypes([Post::getPostType()])
            ->conditionalLogic(
                [ConditionalLogic::where('is_custom_news', "==", 1)]
            )
            ->min(1)
            ->required();
    }
}
