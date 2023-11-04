<?php
/*
Plugin Name: 1337 CDN Plugin
Description: Allows users to add 10 CSS and JS CDNs to the head section of every page
Version: 1337.0
Author: Randal Burger Jr
Author URI: https://codelyfe.github.io/
License: GNU General Public License v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
// I Provide Free Tech Support: https://support-desk.bss.design/index.html

// Add a new settings page to the WordPress admin menu
function cdn_add_menu_item() {
    add_menu_page(
        '1337 CDN Settings',
        '1337 CDN',
        'manage_options',
        'cdn-settings',
        'cdn_settings_page'
    );
}
add_action( 'admin_menu', 'cdn_add_menu_item' );

// Create the settings page
function cdn_settings_page() {

    // Check that the user is authorized to access the settings page
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Save the CDN URLs when the form is submitted
    if ( isset( $_POST['cdn_urls'] ) ) {
        update_option( 'cdn_urls', $_POST['cdn_urls'] );
        echo '<div class="notice notice-success"><p>CDN URLs updated successfully.</p></div>';
    }

    // Display the settings page
    $cdn_urls = get_option( 'cdn_urls', array() );
    // Make sure that there are 10 arrays in the $cdn_urls array
    for ( $i = count( $cdn_urls ); $i < 10; $i++ ) {
        $cdn_urls[] = array(
            'url' => '',
            'type' => 'css',
        );
    }
    ?>
    <div class="wrap">
        <h1>CDN Settings</h1>
        <form method="post">
            <?php for ( $i = 0; $i < 10; $i++ ) : ?>
                <label for="cdn_url_<?php echo $i; ?>">Input your CDN URLs <?php echo $i + 1; ?>:</label>
                <input type="text" name="cdn_urls[<?php echo $i; ?>][url]" id="cdn_url_<?php echo $i; ?>" value="<?php echo esc_url( $cdn_urls[ $i ]['url'] ); ?>" /><br />
                <input type="radio" name="cdn_urls[<?php echo $i; ?>][type]" value="css" <?php checked( $cdn_urls[ $i ]['type'], 'css' ); ?>> CSS
                <input type="radio" name="cdn_urls[<?php echo $i; ?>][type]" value="js" <?php checked( $cdn_urls[ $i ]['type'], 'js' ); ?>> JS
                <br />
            <?php endfor; ?>
            <input type="submit" value="Save" class="button button-primary" />
        </form>
    </div>
    <?php
}

// Add the CDNs to the head section of every page
function cdn_add_to_head() {
    $cdn_urls = get_option( 'cdn_urls', array() );
    foreach ( $cdn_urls as $cdn_url ) {
        if ( ! empty( $cdn_url['url'] ) ) {
            if ( $cdn_url['type'] === 'css' ) {
                echo '<link rel="stylesheet" href="' . esc_url( $cdn_url['url'] ) . '" />';
            } elseif ( $cdn_url['type'] === 'js' ) {
                echo '<script src="' . esc_url( $cdn_url['url'] ) . '"></script>';
            }
        }
    }
}
add_action( 'wp_head', 'cdn_add_to_head' );
