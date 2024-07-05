<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class File_Upload_Handler {

    private static $instance = null;

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'wp_ajax_nopriv_handle_file_upload', array( $this, 'handle_file_upload' ) );
        add_action( 'wp_ajax_handle_file_upload', array( $this, 'handle_file_upload' ) );
    }

    public function handle_file_upload() {
        if ( ! function_exists( 'wp_handle_upload' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }

        $file = $_FILES['file'];
        $upload_overrides = array( 'test_form' => false );
        $movefile = wp_handle_upload( $file, $upload_overrides );

        if ( $movefile && ! isset( $movefile['error'] ) ) {
            $current_user = wp_get_current_user();
            update_user_meta( $current_user->ID, 'uploaded_files', $movefile['url'] );
            echo json_encode( array( 'url' => $movefile['url'] ) );
        } else {
            echo json_encode( array( 'error' => $movefile['error'] ) );
        }

        wp_die();
    }
}
