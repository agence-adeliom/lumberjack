<?php

namespace App\Blocks;

use Adeliom\WP\Extensions\Blocks\AbstractBlock;
use App\Admin\Utils\AcfUtils;
use App\Enum\GutBlockName;
use App\Helpers\Gutenberg\GutenbergBlockHelper;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\WysiwygEditor;

/**
 * Class WysiwygBlock
 * @see https://github.com/wordplate/extended-acf#fields
 * @package App\Admin
 */
class WysiwygBlock extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Texte centré'),
            'description' => __('Bloc de texte centré'),
            'category' => GutBlockName::CONTENT,
            'post_types' => GutenbergBlockHelper::commonsTemplate(),
            'mode' => 'edit',
            'dir' => BlocksTwigPath::CONTENT
        ]);
    }

    protected function registerFields(): \Traversable
    {
        yield Tab::make(__('Contenu'));
        yield WysiwygEditor::make(__("Description"), "text")
            ->tabs('visual')
            ->mediaUpload(false)
            ->required();

        //yield AcfUtils::button();

        yield Tab::make(__('Mise en page'));
        //yield AcfUtils::withBg();
    }
}
