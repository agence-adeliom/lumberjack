<?php

namespace Adeliom\Lumberjack\Admin\Fields\Medias;

use Adeliom\Lumberjack\Admin\Fields\Choices\TrueFalseField;
use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\File;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Text;

class VideoField
{
    private const VIDEO = "video";
    private const THUMBNAIL = "thumbnail";
    private const VIDEO_FILE = "file";
    private const IS_YOUTUBE = "is_youtube";
    private const ID_YOUTUBE = "id";

    /**
     * Vidéo
     */
    public static function make(): Group
    {
        return Group::make(__("Vidéo"), self::VIDEO)
            ->fields([

                ImageField::make(__("Vignette"), self::THUMBNAIL)
                    ->required(),

                TrueFalseField::make("Vidéo youtube ?", self::IS_YOUTUBE),

                Text::make('Identifiant de la vidéo', self::ID_YOUTUBE)
                    ->conditionalLogic([
                        ConditionalLogic::where(self::IS_YOUTUBE, "==", 1)
                    ]),

                File::make("Fichier", self::VIDEO_FILE)
                    ->mimeTypes(["mp4"])
                    ->returnFormat("url")
                    ->required()
                    ->conditionalLogic([
                        ConditionalLogic::where(self::IS_YOUTUBE, "!=", 1),
                    ])

            ]);
    }
}
