<?php
declare (strict_types = 1);
namespace WebSharks\WpSharks\IfShortcode\Classes;

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
 * App class.
 *
 * @since 160707.2545 Initial release.
 */
class App extends SCoreClasses\App
{
    /**
     * Version.
     *
     * @since 160707.2545 Initial release.
     *
     * @type string Version.
     */
    const VERSION = '160707.2545'; //v//

    /**
     * Constructor.
     *
     * @since 160707.2545 Initial release.
     *
     * @param array $instance Instance args.
     */
    public function __construct(array $instance = [])
    {
        $is_multisite = is_multisite();
        $is_main_site = !$is_multisite || is_main_site();

        $instance_base = [
            '©di' => [
                '©default_rule' => [
                    'new_instances' => [
                    ],
                ],
            ],

            '©brand' => [
                '©name'    => '[if /] Shortcode',
                '©acronym' => 'IFSC',

                '©text_domain' => 'if-shortcode',

                '©slug' => 'if-shortcode',
                '©var'  => 'if_shortcode',

                '©short_slug' => 'if-sc',
                '©short_var'  => 'if_sc',
            ],

            '§pro_option_keys' => [
                'att_expr_enable',
                'att_for_blog_enable',
            ],
            '§default_options' => [
                'att_expr_enable'     => $is_multisite && !$is_main_site ? '0' : '1',
                'att_for_blog_enable' => $is_multisite && !$is_main_site ? '0' : '1',
            ],
        ];
        parent::__construct($instance_base, $instance);
    }

    /**
     * Early hook setup handler.
     *
     * @since 160707.2545 Initial release.
     */
    protected function onSetupEarlyHooks()
    {
        parent::onSetupEarlyHooks();
    }

    /**
     * Other hook setup handler.
     *
     * @since 160707.2545 Initial release.
     */
    protected function onSetupOtherHooks()
    {
        parent::onSetupOtherHooks();

        for ($_i = 0, $if_shortcode_name = $this->Utils->Shortcode->name, $if_shortcode_names = []; $_i < 5; ++$_i) {
            add_shortcode($if_shortcode_names[] = str_repeat('_', $_i).$if_shortcode_name, [$this->Utils->Shortcode, 'onShortcode']);
        } // unset($_i); // Housekeeping.

        add_filter('no_texturize_shortcodes', function (array $shortcodes) use ($if_shortcode_names) {
            return array_merge($shortcodes, $if_shortcode_names);
        }); // See: <http://jas.xyz/24AusB7> for more about this filter.

        add_filter('widget_text', 'do_shortcode'); // Enable shortcodes in widgets.
    }
}
