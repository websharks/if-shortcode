<?php
/**
 * Attr utils.
 *
 * @author @jaswsinc
 * @copyright WP Sharks™
 */
declare (strict_types = 1);
namespace WebSharks\WpSharks\IfShortcode\Classes\Utils;

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
 * Attr utils.
 *
 * @since 160919.18816 Attr utils.
 */
class AttrFuncs extends SCoreClasses\SCore\Base\Core
{
    /**
     * Request var.
     *
     * @since 160919.18816 Attr utils.
     *
     * @param string $key Request key.
     *
     * @return string Request value (as a string).
     */
    public function requestVar(string $key): string
    {
        return c::unslash((string) ($_REQUEST[$key] ?? ''));
    }
}
