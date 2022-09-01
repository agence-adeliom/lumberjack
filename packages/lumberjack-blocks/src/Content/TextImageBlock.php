<?php

namespace App\Blocks;

use Adeliom\WP\Extensions\Blocks\AbstractBlock;
use App\Admin\Utils\AcfUtils;
use App\Enum\GutBlockName;
use App\Helpers\Gutenberg\GutenbergBlockHelper;
use Traversable;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\RadioButton;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;

/**
 * Class TextImageBlock
 * @see https://github.com/wordplate/extended-acf#fields
 * @package App\FlexibleLayout
 */
class TextImageBlock extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Texte + média'),
            'description' => __('Description : Bloc avec texte accolé à une image'),
            'category' => GutBlockName::CONTENT,
            'post_types' => GutenbergBlockHelper::commonsTemplate(),
            'mode' => 'edit',
            'dir' => BlocksTwigPath::CONTENT
        ]);
    }

    /**
     * @return Traversable
     */
    protected function registerFields(): \Traversable
    {
        yield Tab::make(__('Contenu'));
        yield Image::make(__("Image"), "img")
            ->library("all")
            ->previewSize("medium")
            ->returnFormat('array')
            ->required();

        yield Text::make(__("Titre"), "title");

        yield WysiwygEditor::make(__("Texte"), "text")
            ->tabs('visual')
            ->mediaUpload(false)
            ->required();

        //yield AcfUtils::button();

        yield Tab::make(__('Mise en page'));
        yield RadioButton::make(__('Position de l\'image'), "img_position")
            ->choices([
                'left' => '<span style=" border: 1px solid #ccc;padding: 10px;display: flex;align-items: center;justify-content: center;width: 70px;margin-top: 10px;margin-bottom: 20px;"><span class="dashicons dashicons-format-image"></span><span class="dashicons dashicons-text"></span></span>',
                'right' => '<span style=" border: 1px solid #ccc;padding: 10px;display: flex;align-items: center;justify-content: center;width: 70px;margin-top: 10px;margin-bottom: 20px;"><span class="dashicons dashicons-text"></span><span class="dashicons dashicons-format-image"></span></span>',
                'bottom' => '<span style=" border: 1px solid #ccc;padding: 10px;display: flex;flex-direction:column; align-items: center;justify-content: center;width: 70px;margin-top: 10px;margin-bottom: 20px;"><span class="dashicons dashicons-text"></span><span class="dashicons dashicons-format-image"></span></span>',
                'top' => '<span style=" border: 1px solid #ccc;padding: 10px;display: flex;flex-direction:column; align-items: center;justify-content: center;width: 70px;margin-top: 10px;margin-bottom: 20px;"><span class="dashicons dashicons-format-image"></span><span class="dashicons dashicons-text"></span></span>',
            ])
            ->defaultValue("left")
            ->required();

        //yield AcfUtils::withBg();
    }
}
