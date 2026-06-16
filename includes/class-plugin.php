<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

class Plugin {

    private static $instance = null;

    public $settings;
    public $scheduler;

    public static function instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_files();
        $this->init_modules();
        $this->load_textdomain();
        do_action( 'woope_loaded' );
    }

    private function load_files() {

        require_once WOOPE_PATH . 'includes/helpers.php';
        require_once WOOPE_PATH . 'includes/class-settings.php';
        require_once WOOPE_PATH . 'includes/class-scheduler.php';
        require_once WOOPE_PATH . 'includes/class-product-meta.php';
        require_once WOOPE_PATH . 'includes/class-frontend.php';
        require_once WOOPE_PATH . 'includes/class-order.php';
        require_once WOOPE_PATH . 'includes/class-admin.php';
        require_once WOOPE_PATH . 'includes/class-columns.php';
        require_once WOOPE_PATH . 'includes/class-filter.php';
        require_once WOOPE_PATH . 'includes/class-multilingual.php';
        require_once WOOPE_PATH . 'includes/class-expired.php';
    }

    private function init_modules() {
        $this->settings   = new Settings();
        $this->scheduler  = new Scheduler();

        new Product_Meta();
        new Frontend();
        new Order();
        new Admin();
        new Columns();
        new Filter_Admin();
        new Multilingual();
        new Expired_Status();
    }

    private function load_textdomain() {
        load_plugin_textdomain(
            'product-expiry-for-woocommerce',
            false,
            dirname( plugin_basename( __FILE__ ) ) . '/../languages'
        );
    }
}