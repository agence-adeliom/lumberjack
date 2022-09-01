<?php

namespace App\Blocks;

use Adeliom\Lumberjack\Admin\AbstractBlock;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\WysiwygEditor;

class WysiwygBlock extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Texte centré'),
            'description' => __('Bloc de texte centré'),
            'mode' => 'edit',
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
