<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Multilingual
 *
 * Keeps expiry data consistent across translated copies of a product.
 *
 * - WPML reads the bundled wpml-config.xml (plugin root) and copies the
 *   expiry meta to translations automatically; no PHP needed there.
 * - Polylang is wired up here via the pll_copy_post_metas filter.
 *
 * Purely additive: registers existing meta keys for copy/sync. It does not
 * rename, move, or delete any stored data, and is inert when neither WPML
 * nor Polylang is active.
 */
class Multilingual {

    public function __construct() {

        // Polylang: copy/sync expiry data across translations.
        add_filter( 'pll_copy_post_metas', [ $this, 'polylang_copy_metas' ], 10, 2 );
    }

    /**
     * Meta keys that hold the same expiry value in every language and should
     * be kept in sync across translations.
     *
     * Filterable so Pro (or a site owner) can extend the list without editing
     * this file. woo_expiry_time is included for when Pro is active; it is
     * harmless when the key does not exist.
     *
     * @return string[]
     */
    public static function synced_meta_keys() {
        return apply_filters( 'woope_synced_meta_keys', [
            'woo_expiry_date',
            'woo_expiry_time',
            'woo_expiry_action',
        ] );
    }

    /**
     * Add our expiry keys to Polylang's copy/sync list.
     *
     * @param array $keys Existing meta keys Polylang copies.
     * @param bool  $sync Whether this is an ongoing sync (true) or first copy.
     * @return array
     */
    public function polylang_copy_metas( $keys, $sync = false ) {
        return array_values( array_unique( array_merge( (array) $keys, self::synced_meta_keys() ) ) );
    }
}
