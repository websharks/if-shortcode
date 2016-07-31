<?php
/**
 * WooCommerce utils.
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
 * WooCommerce utils.
 *
 * @since 160709.39379 WooCommerce utils.
 */
class WooCommerce extends SCoreClasses\SCore\Base\Core
{
    /**
     * Product ID by SKU.
     *
     * @since 160709.39379 WooCommerce utils.
     *
     * @param string $product_id_or_sku Product ID or SKU.
     *
     * @return int Product ID by SKU.
     *
     * @NOTE Product variations don't inherit an SKU from their parent.
     * Don't expect this to deal with any parent/child relationships whatsoever.
     */
    public function productIdBySku(string $product_id_or_sku): int
    {
        $product_id = $product_sku = $product_id_or_sku;

        if ((string) (int) $product_id === $product_id) {
            return (int) $product_id; // Assume ID.
        } elseif (!is_array($by_sku = $this->productIdsBySku())) {
            return (int) wc_get_product_id_by_sku($product_sku);
        }
        return $by_sku[$product_sku] ?? 0;
    }

    /**
     * Customer bought product?
     *
     * @since 160709.39379 WooCommerce utils.
     *
     * @param int|null $user_id           User ID.
     * @param string   $product_id_or_sku Product ID or SKU.
     *
     * @return bool True if customer bought product.
     *
     * @NOTE This deals with parent/child relationships automatically.
     * Checking for a product by parent SKU returns true if any variation was purchased.
     * However, if the SKU is variation-specific, this only returns true if the variation was purchased.
     *
     * The WC function `wc_customer_bought_product()` stores and searches
     * for either meta field in the DB. One of: `_product_id` or `_variation_id`.
     * The order item meta table stores the parent `_product_id` when a variation is purchased.
     * That's how this is able to work as intended; i.e., an excellent design choice on the part of WC.
     */
    public function customerBoughtProduct(int $user_id = null, string $product_id_or_sku = ''): bool
    {
        $user_id = (int) ($user_id ?? get_current_user_id());

        if (($can = &$this->cacheKey(__FUNCTION__, [$user_id, $product_id_or_sku])) !== null) {
            return $can; // Cached this already.
        }
        if ($user_id && ($product_id = $this->productIdBySku($product_id_or_sku))) {
            return $can = (bool) wc_customer_bought_product('', $user_id, $product_id);
        }
        return $can = false; // Defaults to a `false` value.
    }

    /**
     * Customer can download?
     *
     * @since 160709.39379 WooCommerce utils.
     *
     * @param int|null $user_id           User ID.
     * @param string   $product_id_or_sku Product ID or SKU.
     *
     * @return bool True if customer can download.
     *
     * @NOTE This deals with parent/child relationships automatically.
     * Checking for a product by parent SKU returns true if any variation provides download access.
     * However, if the SKU is variation-specific, this only returns true if the variation provides access.
     */
    public function customerCanDownload(int $user_id = null, string $product_id_or_sku = ''): bool
    {
        $user_id = (int) ($user_id ?? get_current_user_id());

        if (($can = &$this->cacheKey(__FUNCTION__, [$user_id, $product_id_or_sku])) !== null) {
            return $can; // Cached this already.
        }
        if ($user_id && ($product_id = $this->productIdBySku($product_id_or_sku))) {
            $WpDb              = s::wpDb(); // DB object instance.
            $local_ymd_his_now = date('Y-m-d H:i:s', s::utcToLocal(time()));
            $permissions_table = $WpDb->prefix.'woocommerce_downloadable_product_permissions';

            $sub_query = /* For parent product IDs. */ '
                SELECT `ID` FROM `'.esc_sql($WpDb->posts).'`
                    WHERE `post_parent` = %s AND `post_type` = \'product_variation\'
            ';
            $sql = /* Build SQL query. */ '
                SELECT `permissions`.`permission_id`
                    FROM `'.esc_sql($permissions_table).'` AS `permissions`

                WHERE
                    `permissions`.`user_id` = %s
                    AND (`permissions`.`product_id` = %s OR `permissions`.`product_id` IN('.$sub_query.'))
                    AND (`permissions`.`downloads_remaining` > 0 OR `permissions`.`downloads_remaining` = \'\')
                    AND (`permissions`.`access_expires` IS NULL OR `permissions`.`access_expires` = %s OR `permissions`.`access_expires` > %s)

                LIMIT 1';
            $sql = $WpDb->prepare($sql, $user_id, $product_id, $product_id, '0000-00-00 00:00:00', $local_ymd_his_now);

            return $can = (bool) $WpDb->get_var($sql);
        }
        return $can = false; // Defaults to a `false` value.
    }

    /**
     * Product IDs by SKU.
     *
     * @since 160709.39379 WooCommerce utils.
     *
     * @return array|null Product IDs by SKU, else `null` if there are too many SKUs.
     *                    If the default limit 2500 is exceeded, this utility is simply not capable of working.
     *                    Instead, use `wc_get_product_id_by_sku()`. Or, don't use SKUs, use product IDs.
     *
     * @NOTE Product variations don't inherit an SKU from their parent.
     * Don't expect this to deal with any parent/child relationships whatsoever.
     */
    public function productIdsBySku()
    {
        $transient_key = 'woocommerce_product_ids_by_sku';
        if (is_array($by_sku = s::getTransient($transient_key))) {
            return $by_sku; // Already cached this.
        }
        $WpDb   = s::wpDb(); // DB object reference.
        $by_sku = []; // Initialize array of IDs by SKU.

        $sql = /* Query 2500 product SKUs. */ '
            SELECT
                SQL_CALC_FOUND_ROWS
                `posts`.`ID`,
                `postmeta`.`meta_value` AS `sku`

                FROM `'.esc_html($WpDb->posts).'` AS `posts`,
                     `'.esc_sql($WpDb->postmeta).'` AS `postmeta`

            WHERE `posts`.`ID` = `postmeta`.`post_id`
                AND `posts`.`post_type` IN(\'product\', \'product_variation\')
                AND `postmeta`.`meta_key` = \'_sku\' AND `postmeta`.`meta_value` != \'\'

            LIMIT '.($limit = (int) s::applyFilters('woocommerce_product_ids_by_sku_limit', 2500));
        /*
         * NOTE: A warning should be given to site owners.
         * If you have more than 2500 SKUs, use IDs instead of SKUs.
         * Or, increase RAM and be aware that > 2500 product IDs will be in memory.
         *
         * The filter above allows for the upper limit to be increased to any amount you like.
         *  i.e., use filter: `if_shortcode_woocommerce_product_ids_by_sku_limit`.
         */
        if (($results = $WpDb->get_results($sql))) {
            if ((int) $WpDb->get_var('SELECT FOUND_ROWS()') > $limit) {
                return $by_sku = null; // Do not use. Do not save.
            }
            foreach ($results as $_result) {
                $by_sku[(string) $_result->sku] = (int) $_result->ID;
            } // unset($_result); // Housekeeping.
        }
        s::setTransient($transient_key, $by_sku, DAY_IN_SECONDS);

        return $by_sku; // Keyed by SKU now.
    }

    /**
     * On product save.
     *
     * @since 160709.39379 WooCommerce utils.
     */
    public function onSaveProduct()
    {
        s::deleteTransient('woocommerce_product_ids_by_sku');
    }

    /**
     * On product variation save.
     *
     * @since 160709.39379 WooCommerce utils.
     */
    public function onSaveProductVariation()
    {
        s::deleteTransient('woocommerce_product_ids_by_sku');
    }
}
