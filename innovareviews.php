<?php
/*
Plugin Name: InnovaReviews
Plugin URI: https://www.innovareviews.com
Description: Add reviews and testimonials to your website with ease. After activating the plugin, visit <a href="https://www.innovareviews.com/signup" target="_blank">InnovaReviews</a> to create an account and get your client code. Then go to your WordPress InnovaReviews settings page and enter your code there.
Version: 1.0
Author: Aert van de Hulsbeek
License: GPL2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

if ( !class_exists('InnovaReviews' ) ) {
    /*
     * Wrapper class to isolate us from the global space in order
     * to prevent method collision
     */
    class InnovaReviews {
        /*
         * Set up all actions
         */
        function __construct() {
            add_action( 'admin_menu', array( $this, 'add_admin' ) );
            add_action( 'admin_init', array( $this, 'admin_init' ) );
            add_action( 'wp_head', array( $this, 'display' ) );
        }

        /*
         * Add our options to the settings menu
         */
        function add_admin() {
            add_options_page('InnovaReviews', 'InnovaReviews', 'manage_options', 'ir_plugin', array( $this, 'plugin_options_page' ) );
        }

        /*
         * Callback for options page - set up page title and instantiate field
         */
        function plugin_options_page() {
?>
        <div class="plugin-options">
            <h2><span>InnovaReviews</span></h2>
            <p>Visit <a href="https://www.innovareviews.com/signup" target="_blank">InnovaReviews</a> to create an account and get your client code. Then enter your code here.</p>
            <form action="options.php" method="post">
<?php
                settings_fields( 'ir_options' );
                do_settings_sections( 'ir_plugin' );
?>
                <input name="Submit" type="submit" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
            </form>
        </div>
<?php
        }

        /*
         * Define options section and fields
         */
        function admin_init() {
            register_setting( 'ir_options', 'ir_options', array( $this, 'options_validate' ) );
            add_settings_section( 'ir_section', '', array( $this, 'main_section' ), 'ir_plugin' );
            add_settings_field( 'ir_string', 'Client code', array( $this, 'client_code_field'), 'ir_plugin', 'ir_section');
        }

        /*
         * Static content for options section
         */
        function main_section() {
            // NA
        }

        /*
         * Code for field
         */
        function client_code_field() {
            $options = get_option( 'ir_options' );
?>
            <input type="text" id="ir_options" name="ir_options[client_code]" value="<?php _e( $options['client_code'] ); ?>">
<?php
        }

        /*
         * No validation, just remove leading and trailing space
         */
        function options_validate($input) {
            $newinput['client_code'] = trim( $input['client_code'] );
            return $newinput;
        }

        /*
         * Display the code(s) on the public page.
         * We do an extra check to ensure that the codes don't show up
         * in the admin tool.
         */
        function display() {
            if( !is_admin() ) {
                $options = get_option( 'ir_options' );
?>
                <script src="https://plugin.innovareviews.com/plugin.js" data-innova-id="<?php _e( $options['client_code'] ); ?>" async></script>
<?php
            }
        }
    }
}

/*
 * Sanity - was there a problem setting up the class? If so, bail with error
 * Otherwise, class is now defined; create a new one to get the ball rolling.
 */
if( class_exists( 'InnovaReviews' ) ) {
    new InnovaReviews();
} else {
    $message = "<h2 style='color:red'>Error in plugin</h2>
    <p>Sorry about that! Plugin <span style='color:blue;font-family:monospace'>innovareviews</span> reports that it was unable to start.</p>
    <p>Make sure you are running the latest version of the plugin; update the plugin if not.</p>
    ";
    wp_die( $message );
}
?>
