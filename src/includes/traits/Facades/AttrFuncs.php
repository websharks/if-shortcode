<?php
/**
 * Attribute handlers.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\IfShortcode\Traits\Facades;

use WebSharks\WpSharks\IfShortcode\Classes;
use WebSharks\WpSharks\IfShortcode\Interfaces;
use WebSharks\WpSharks\IfShortcode\Traits;
#
use WebSharks\WpSharks\IfShortcode\Classes\AppFacades as a;
use WebSharks\WpSharks\IfShortcode\Classes\SCoreFacades as s;
use WebSharks\WpSharks\IfShortcode\Classes\CoreFacades as c;
#
use WebSharks\WpSharks\Core\Classes as SCoreClasses;
use WebSharks\WpSharks\Core\Interfaces as SCoreInterfaces;
use WebSharks\WpSharks\Core\Traits as SCoreTraits;
#
use WebSharks\Core\WpSharksCore\Classes as CoreClasses;
use WebSharks\Core\WpSharksCore\Classes\Core\Base\Exception;
use WebSharks\Core\WpSharksCore\Interfaces as CoreInterfaces;
use WebSharks\Core\WpSharksCore\Traits as CoreTraits;
#
use function assert as debug;
use function get_defined_vars as vars;

/**
 * Attribute handlers.
 *
 * @since 160919.18816 Initial release.
 */
trait AttrFuncs
{
    /**
     * @since 160919.18816 Initial release.
     *
     * @param mixed ...$args Variadic args to underlying utility.
     *
     * @see Classes\Utils\AttrFuncs::requestVar()
     */
    public static function requestVar(...$args)
    {
        return $GLOBALS[static::class]->Utils->AttrFuncs->requestVar(...$args);
    }
}
