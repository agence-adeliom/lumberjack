<?php

namespace Adeliom\Lumberjack\Assets\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class ImageTwigExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('image', [$this, 'image']),
            new TwigFunction('icon', [$this, 'icon'])
        ];
    }

    private static function getTransient(string $name, string $value, int $expiration = 3600)
    {
        if (false === ( $transient = get_transient($name) )) {
            set_transient($name, $value, $expiration);
            $transient = get_transient($name);
        }
        return $transient;
    }

    public static function icon($image)
    {
        if (!empty($image) && $image['mime_type'] === "image/svg+xml") {
            $name = sprintf("t_icon_%s", $image['id']);
            return self::getTransient($name, file_get_contents($image['url']));
        }
        return null;
    }

    public static function image($image, $size, $customClass = '', $customAlt = true)
    {

        $attr = array(
            'class' => $customClass,
        );

        if ($customAlt) {
            $nameBeautified = ucwords(str_replace('-', ' ', $image['name']));

            $attr['alt'] = $image['alt'] != "" ? $image['alt'] : $nameBeautified;
        }

        return wp_get_attachment_image($image['ID'], $size, false, $attr);
    }

}