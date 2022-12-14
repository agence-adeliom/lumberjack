<?php

namespace Adeliom\Lumberjack\Taxonomy;

use Adeliom\Lumberjack\Taxonomy\Exceptions\TaxonomyRegistrationException;
use Spatie\Macroable\Macroable;
use Tightenco\Collect\Support\Collection;
use Timber\Term as TimberTerm;
use Timber\Timber;

class Term extends TimberTerm
{
    use Macroable {
        Macroable::__call as __macroableCall;
        Macroable::__callStatic as __macroableCallStatic;
    }

    public function __construct($tid = null, $tax = '', $preventTimberInit = false)
    {
        /**
         * There are occasions where we do not want the bootstrap the data. At the moment this is
         * designed to make Query Scopes possible
         */
        if (!$preventTimberInit) {
            parent::__construct($tid, $tax);
        }
    }

    public function __call($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return $this->__macroableCall($name, $arguments);
        }

        return parent::__call($name, $arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        if (static::hasMacro($name)) {
            return static::__macroableCallStatic($name, $arguments);
        }

        trigger_error('Call to undefined method ' . __CLASS__ . '::' . $name . '()', E_USER_ERROR);
    }

    /**
     * Return the key used to register the taxonomy with WordPress
     * First parameter of the `register_taxonomy` function:
     * https://developer.wordpress.org/reference/functions/register_taxonomy/
     *
     * @return string|null
     */
    public static function getTaxonomyType(): ?string
    {
        return null;
    }

    /**
     * Return the object type which use this taxonomy.
     * Second parameter of the `register_taxonomy` function:
     * https://developer.wordpress.org/reference/functions/register_taxonomy/
     *
     * @return array|null
     */
    public static function getTaxonomyObjectTypes(): ?array
    {
        return ['post'];
    }

    /**
     * Return the config to use to register the taxonomy with WordPress
     * Third parameter of the `register_taxonomy` function:
     * https://developer.wordpress.org/reference/functions/register_taxonomy/
     *
     * @return array|null
     */
    protected static function getTaxonomyConfig(): ?array
    {
        return null;
    }

    /**
     * Register this PostType with WordPress
     *
     * @return void
     * @throws TaxonomyRegistrationException
     */
    public static function register(): void
    {
        $taxonomyType = static::getTaxonomyType();
        $taxonomyObjectTypes = static::getTaxonomyObjectTypes();
        $config = static::getTaxonomyConfig();

        if (empty($taxonomyType)) {
            throw new TaxonomyRegistrationException('Taxonomy type not set');
        }

        if (empty($taxonomyObjectTypes)) {
            throw new TaxonomyRegistrationException('Taxonomy object type not set');
        }

        if (empty($config)) {
            throw new TaxonomyRegistrationException('Config not set');
        }

        register_taxonomy($taxonomyType, $taxonomyObjectTypes, $config);
    }

    /**
     * Get all terms of this taxonomy
     *
     * @param string $orderby Field(s) to order terms by (defaults to term_order)
     * @param string $order Whether to order terms in ascending or descending order (defaults to ASC)
     * @return Collection
     */
    public static function all(string $orderby = 'term_order', string $order = 'ASC'): Collection
    {
        $order = strtoupper($order);

        $args = [
            'orderby'       => $orderby,
            'order'         => $order,
        ];

        return static::query($args);
    }


    /**
     * Convenience function that takes a standard set of WP_Term_Query arguments but mixes it with
     * arguments that mean we're selecting the right taxonomy type
     *
     * @param array|null $args standard WP_Term_Query array
     * @return Collection
     */
    public static function query(?array $args = null): Collection
    {
        $args = is_array($args) ? $args : [];

        // Set the correct post type
        $args = array_merge($args, ['taxonomy' => static::getTaxonomyType()]);

        return self::terms($args);
    }

    /**
     * Raw query function that uses the arguments provided to make a call to Timber::get_terms
     * and casts the returning data in instances of ourself.
     *
     * @param array|null $args standard WP_Query array
     * @return Collection
     */
    private static function terms(?array $args = null): Collection
    {
        return collect(Timber::get_terms($args, [], static::class));
    }
}
