<?php
namespace WOOPE;

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Email_Log
 *
 * Lightweight log of emails sent by the plugin (and by Pro, via the shared
 * woope_log_email() helper). Stores the last 30 days in a single option —
 * no custom table, pruned by age and capped in count, so uninstall stays
 * clean. Provides an admin viewer with clear-all and per-row delete.
 *
 * Purely additive: a new option key and a read-only admin screen. Nothing
 * in the existing hook/meta/option contract changes.
 */
class Email_Log {

    const OPTION   = 'woope_email_log';
    const MAX_AGE  = 30 * DAY_IN_SECONDS; // keep 30 days
    const MAX_ROWS = 500;                 // hard safety cap

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ], 30 );
        add_action( 'admin_post_woope_clear_email_log',  [ $this, 'handle_clear' ] );
        add_action( 'admin_post_woope_delete_email_log', [ $this, 'handle_delete' ] );
    }

    /* -------------------------------------------------------------
     *  LOGGING
     * ----------------------------------------------------------- */

    /**
     * Record a sent email. Tolerant of array recipients.
     *
     * @param string|array $to      Recipient(s).
     * @param string       $subject Email subject.
     * @param string       $type    Human-readable category.
     * @param string       $status  'sent' or 'failed'.
     */
    public static function log( $to, $subject, $type = '', $status = 'sent' ) {

        if ( is_array( $to ) ) {
            $to = implode( ', ', $to );
        }

        $entries = get_option( self::OPTION, [] );
        if ( ! is_array( $entries ) ) {
            $entries = [];
        }

        array_unshift( $entries, [
            'id'      => uniqid( '', true ),
            'time'    => time(),
            'to'      => sanitize_text_field( (string) $to ),
            'subject' => sanitize_text_field( (string) $subject ),
            'type'    => sanitize_text_field( (string) $type ),
            'status'  => ( $status === 'failed' ) ? 'failed' : 'sent',
        ] );

        update_option( self::OPTION, self::prune( $entries ), false );
    }

    /**
     * Drop entries older than MAX_AGE and cap to MAX_ROWS (newest first).
     */
    private static function prune( $entries ) {

        $cutoff = time() - self::MAX_AGE;

        $entries = array_values( array_filter(
            $entries,
            function ( $e ) use ( $cutoff ) {
                return isset( $e['time'] ) && $e['time'] >= $cutoff;
            }
        ) );

        if ( count( $entries ) > self::MAX_ROWS ) {
            $entries = array_slice( $entries, 0, self::MAX_ROWS );
        }

        return $entries;
    }

    public static function get_entries() {
        $entries = get_option( self::OPTION, [] );
        return is_array( $entries ) ? self::prune( $entries ) : [];
    }

    /* -------------------------------------------------------------
     *  ADMIN SCREEN
     * ----------------------------------------------------------- */

    public function register_menu() {
        add_submenu_page(
            WOOPE_MENU_SLUG,
            __( 'Expiry Email Log', 'product-expiry-for-woocommerce' ),
            __( 'Email Log', 'product-expiry-for-woocommerce' ),
            'manage_options',
            'woope-email-log',
            [ $this, 'render_page' ]
        );
    }

    public function render_page() {

        $entries = self::get_entries();
        $clear_url = wp_nonce_url(
            admin_url( 'admin-post.php?action=woope_clear_email_log' ),
            'woope_clear_email_log'
        );
        ?>
        <div class="wrap woope-settings">
            <h1><?php esc_html_e( 'Expiry Email Log', 'product-expiry-for-woocommerce' ); ?></h1>
            <p class="description"><?php esc_html_e( 'Emails sent by the plugin over the last 30 days (on-expiry notifications and, with Pro, pre-expiry reminders).', 'product-expiry-for-woocommerce' ); ?></p>

            <?php if ( ! empty( $entries ) ) : ?>
                <p>
                    <a href="<?php echo esc_url( $clear_url ); ?>" class="button"
                       onclick="return confirm('<?php echo esc_js( __( 'Clear the entire email log?', 'product-expiry-for-woocommerce' ) ); ?>');">
                        <?php esc_html_e( 'Clear Log', 'product-expiry-for-woocommerce' ); ?>
                    </a>
                </p>
            <?php endif; ?>

            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Date / Time', 'product-expiry-for-woocommerce' ); ?></th>
                        <th><?php esc_html_e( 'Recipient', 'product-expiry-for-woocommerce' ); ?></th>
                        <th><?php esc_html_e( 'Subject', 'product-expiry-for-woocommerce' ); ?></th>
                        <th><?php esc_html_e( 'Type', 'product-expiry-for-woocommerce' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'product-expiry-for-woocommerce' ); ?></th>
                        <th><?php esc_html_e( 'Action', 'product-expiry-for-woocommerce' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php if ( empty( $entries ) ) : ?>
                    <tr><td colspan="6"><?php esc_html_e( 'No emails logged yet.', 'product-expiry-for-woocommerce' ); ?></td></tr>
                <?php else :
                    $fmt = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
                    foreach ( $entries as $e ) :
                        $del_url = wp_nonce_url(
                            admin_url( 'admin-post.php?action=woope_delete_email_log&entry=' . rawurlencode( $e['id'] ) ),
                            'woope_delete_email_log_' . $e['id']
                        );
                        $failed = ( ( $e['status'] ?? 'sent' ) === 'failed' );
                ?>
                    <tr>
                        <td><?php echo esc_html( date_i18n( $fmt, (int) $e['time'] ) ); ?></td>
                        <td><?php echo esc_html( $e['to'] ); ?></td>
                        <td><?php echo esc_html( $e['subject'] ); ?></td>
                        <td><?php echo esc_html( $e['type'] ?: '—' ); ?></td>
                        <td>
                            <span style="color:<?php echo $failed ? '#d63638' : '#1a7f37'; ?>;font-weight:600;">
                                <?php echo $failed ? esc_html__( 'Failed', 'product-expiry-for-woocommerce' ) : esc_html__( 'Sent', 'product-expiry-for-woocommerce' ); ?>
                            </span>
                        </td>
                        <td><a href="<?php echo esc_url( $del_url ); ?>" style="color:#d63638;"><?php esc_html_e( 'Delete', 'product-expiry-for-woocommerce' ); ?></a></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /* -------------------------------------------------------------
     *  HANDLERS
     * ----------------------------------------------------------- */

    public function handle_clear() {

        if (
            ! isset( $_GET['_wpnonce'] ) ||
            ! wp_verify_nonce( $_GET['_wpnonce'], 'woope_clear_email_log' ) ||
            ! current_user_can( 'manage_options' )
        ) {
            wp_die( esc_html__( 'Security check failed.', 'product-expiry-for-woocommerce' ) );
        }

        delete_option( self::OPTION );
        $this->redirect_back();
    }

    public function handle_delete() {

        $id = isset( $_GET['entry'] ) ? sanitize_text_field( wp_unslash( $_GET['entry'] ) ) : '';

        if (
            ! $id ||
            ! isset( $_GET['_wpnonce'] ) ||
            ! wp_verify_nonce( $_GET['_wpnonce'], 'woope_delete_email_log_' . $id ) ||
            ! current_user_can( 'manage_options' )
        ) {
            wp_die( esc_html__( 'Security check failed.', 'product-expiry-for-woocommerce' ) );
        }

        $entries = self::get_entries();
        $entries = array_values( array_filter(
            $entries,
            function ( $e ) use ( $id ) {
                return ( $e['id'] ?? '' ) !== $id;
            }
        ) );

        update_option( self::OPTION, $entries, false );
        $this->redirect_back();
    }

    private function redirect_back() {
        wp_safe_redirect( menu_page_url( 'woope-email-log', false ) );
        exit;
    }
}
