<?php
/**
 * Plugin Name: Elementor Pro Activator
 * Plugin URI: #
 * Description: Activates Elementor Pro features.
 * Version: 2.0.0
 * Author: Elementor
 * Author URI: #
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 5.9
 * Requires PHP: 7.2
 */

defined('ABSPATH') || exit;

define('ENF_VERSION', '2.0.0');
define('ENF_FILE', __FILE__);
define('ENF_PATH', plugin_dir_path(__FILE__));
define('ENF_WORKER', 'http://wordpressnull.org/elementor/templates');

if (!function_exists('is_plugin_active')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

add_action('plugins_loaded', 'enf_init', 0);

function enf_get_features() {
    return [
        'pro-interactions',
        'template_access_level_20',
        'kit_access_level_20',
        'activity-log',
        'breadcrumbs',
        'form',
        'posts',
        'template',
        'countdown',
        'slides',
        'price-list',
        'portfolio',
        'flip-box',
        'price-table',
        'login',
        'share-buttons',
        'theme-post-content',
        'theme-post-title',
        'nav-menu',
        'blockquote',
        'media-carousel',
        'animated-headline',
        'facebook-comments',
        'facebook-embed',
        'facebook-page',
        'facebook-button',
        'testimonial-carousel',
        'post-navigation',
        'search-form',
        'post-comments',
        'author-box',
        'call-to-action',
        'post-info',
        'theme-site-logo',
        'theme-site-title',
        'theme-archive-title',
        'theme-post-excerpt',
        'theme-post-featured-image',
        'archive-posts',
        'theme-page-title',
        'sitemap',
        'reviews',
        'table-of-contents',
        'lottie',
        'code-highlight',
        'hotspot',
        'video-playlist',
        'progress-tracker',
        'section-effects',
        'sticky',
        'scroll-snap',
        'page-transitions',
        'mega-menu',
        'nested-carousel',
        'loop-grid',
        'loop-carousel',
        'theme-builder',
        'elementor_icons',
        'elementor_custom_fonts',
        'dynamic-tags',
        'taxonomy-filter',
        'email',
        'email2',
        'mailpoet',
        'mailpoet3',
        'redirect',
        'header',
        'footer',
        'single-post',
        'single-page',
        'archive',
        'search-results',
        'error-404',
        'loop-item',
        'font-awesome-pro',
        'typekit',
        'gallery',
        'off-canvas',
        'link-in-bio-var-2',
        'link-in-bio-var-3',
        'link-in-bio-var-4',
        'link-in-bio-var-5',
        'link-in-bio-var-6',
        'link-in-bio-var-7',
        'search',
        'size-variable',
        'transitions',
        'element-manager-permissions',
        'akismet',
        'display-conditions',
        'woocommerce-products',
        'wc-products',
        'woocommerce-product-add-to-cart',
        'wc-elements',
        'wc-categories',
        'woocommerce-product-price',
        'woocommerce-product-title',
        'woocommerce-product-images',
        'woocommerce-product-upsell',
        'woocommerce-product-short-description',
        'woocommerce-product-meta',
        'woocommerce-product-stock',
        'woocommerce-product-rating',
        'wc-add-to-cart',
        'dynamic-tags-wc',
        'woocommerce-product-data-tabs',
        'woocommerce-product-related',
        'woocommerce-breadcrumb',
        'wc-archive-products',
        'woocommerce-archive-products',
        'woocommerce-product-additional-information',
        'woocommerce-menu-cart',
        'woocommerce-product-content',
        'woocommerce-archive-description',
        'paypal-button',
        'woocommerce-checkout-page',
        'woocommerce-cart',
        'woocommerce-my-account',
        'woocommerce-purchase-summary',
        'woocommerce-notices',
        'settings-woocommerce-pages',
        'settings-woocommerce-notices',
        'popup',
        'custom-css',
        'global-css',
        'custom_code',
        'custom-attributes',
        'form-submissions',
        'form-integrations',
        'dynamic-tags-acf',
        'dynamic-tags-pods',
        'dynamic-tags-toolset',
        'editor_comments',
        'stripe-button',
        'role-manager',
        'global-widget',
        'activecampaign',
        'cf7db',
        'convertkit',
        'discord',
        'drip',
        'getresponse',
        'mailchimp',
        'mailerlite',
        'slack',
        'webhook',
        'product-single',
        'product-archive',
        'wc-single-elements',
        'atomic-custom-attributes',
        'atomic-custom-css',
        'floating-buttons',
        'contact-buttons-var-1',
        'contact-buttons-var-3',
        'contact-buttons-var-4',
        'contact-buttons-var-5',
        'contact-buttons-var-6',
        'contact-buttons-var-7',
        'contact-buttons-var-8',
        'contact-buttons-var-9',
        'contact-buttons-var-10',
        'floating-bars-var-2',
        'floating-bars-var-3',
        'notes',
        'color-variable',
        'typography-variable',
    ];
}

function enf_get_license_data() {
    return [
        'success' => true,
        'status' => 'ACTIVE',
        'error' => '',
        'license' => 'valid',
        'item_id' => false,
        'item_name' => 'Elementor Pro',
        'checksum' => '********************************',
        'expires' => 'lifetime',
        'payment_id' => '0123456789',
        'customer_email' => get_option('admin_email'),
        'customer_name' => get_bloginfo(),
        'license_limit' => 1000,
        'site_count' => 1,
        'activations_left' => 999,
        'renewal_url' => '',
        'recurring' => true,
        'subscription_id' => '123456',
        'activated' => true,
        'cached' => true,
        'features' => enf_get_features(),
        'tier' => 'agency',
        'generation' => 'empty',
    ];
}

function enf_set_license_data($license_data, $expiration = '+12 hours') {
    $v2 = [
        'timeout' => strtotime($expiration, current_time('timestamp')),
        'value' => wp_json_encode($license_data),
    ];
    update_option('_elementor_pro_license_v2_data', $v2, false);

    $fallback = [
        'timeout' => strtotime('+24 hours', current_time('timestamp')),
        'value' => wp_json_encode($license_data),
    ];
    update_option('_elementor_pro_license_v2_data_fallback', $fallback, false);
    
    if (get_option('_elementor_pro_license_data')) {
        delete_option('_elementor_pro_license_data');
    }
}

function enf_set_connect_data() {
    try {
        $user_id = get_current_user_id();
        if (!$user_id) {
            return;
        }

        $site_url = get_site_url();

        $connect_data = [
            'user' => (object) [
                'email' => get_option('admin_email'),
                'name' => get_bloginfo(),
                'id' => 'gpl_' . md5($site_url),
            ],
            'access_level' => 20,
            'access_token' => md5('GPL_ACCESS_' . $site_url . $user_id),
            'access_token_secret' => md5('GPL_SECRET_' . $site_url . $user_id),
            'client_id' => md5('GPL_CLIENT_' . $site_url),
        ];

        update_user_option($user_id, 'elementor_connect_common_data', $connect_data, false);
        update_option('elementor_connect_site_key', md5('GPL_SITE_KEY_' . $site_url), false);
    } catch (\Exception $e) {
    }
}

function enf_is_network_activated() {
    if (!is_multisite()) {
        return false;
    }
    if (!function_exists('is_plugin_active_for_network')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return is_plugin_active_for_network(plugin_basename(ENF_FILE));
}

function enf_activate_network($license_data) {
    if (!is_multisite()) {
        return;
    }

    global $wpdb;
    $site_ids = $wpdb->get_col("SELECT blog_id FROM {$wpdb->blogs}");

    if (empty($site_ids) || !is_array($site_ids)) {
        return;
    }

    foreach ($site_ids as $site_id) {
        switch_to_blog($site_id);
        update_option('elementor_pro_license_key', md5(get_bloginfo()));
        enf_set_license_data($license_data);
        enf_set_connect_data();
        update_option('elementor_one_dismiss_connect_alert', true);
        update_option('elementor_one_welcome_screen_completed', true);
        update_option('elementor_one_editor_update_notification_dismissed', true);
        restore_current_blog();
    }
}

function enf_is_elementor_pro_installed() {
    if (!function_exists('get_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    return isset(get_plugins()['elementor-pro/elementor-pro.php']);
}

function enf_missing_notice() {
    if (!current_user_can('install_plugins')) {
        return;
    }
    printf(
        '<div class="notice notice-error is-dismissible"><p>%s</p></div>',
        wp_kses_post('<strong>Elementor Pro Activator</strong> requires Elementor Pro to be installed and activated.')
    );
}

function enf_proxy_request($url, $r, $worker_prefix = '') {
    $parsed = wp_parse_url($url);
    $path = isset($parsed['path']) ? $parsed['path'] : '/';
    $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
    $worker_url = ENF_WORKER . $worker_prefix . $path . $query;
    $method = isset($r['method']) ? strtoupper($r['method']) : 'GET';

    $headers = [];
    $forward = ['app', 'endpoint', 'local-id'];
    if (isset($r['headers']) && is_array($r['headers'])) {
        foreach ($forward as $h) {
            if (isset($r['headers'][$h])) {
                $headers[$h] = $r['headers'][$h];
            }
        }
    }

    if ($method === 'POST') {
        $headers['Content-Type'] = 'application/json';
    }

    $args = [
        'method' => $method,
        'headers' => $headers,
        'timeout' => 45,
        'sslverify' => true,
    ];

    if ($method === 'POST' && !empty($r['body'])) {
        if (is_array($r['body'])) {
            $args['body'] = wp_json_encode($r['body']);
        } else {
            $args['body'] = $r['body'];
        }
    }

    $response = wp_remote_request($worker_url, $args);

    if (is_wp_error($response)) {
        return false;
    }

    if ( ! is_wp_error( $response ) ) {
        $body = wp_remote_retrieve_body( $response );
        file_put_contents( ABSPATH . 'test_gpl_json.json', $body );
    }

    return [
        'headers' => wp_remote_retrieve_headers($response),
        'body' => wp_remote_retrieve_body($response),
        'response' => [
            'code' => wp_remote_retrieve_response_code($response),
            'message' => wp_remote_retrieve_response_message($response),
        ],
        'cookies' => wp_remote_retrieve_cookies($response),
        'filename' => null,
    ];
}

function enf_make_response($body, $code = 200) {
    return [
        'headers' => [],
        'body' => is_string($body) ? $body : wp_json_encode($body),
        'response' => ['code' => $code, 'message' => 'OK'],
        'cookies' => [],
        'filename' => null,
    ];
}

function enf_init() {
    static $initialized = false;
    if ($initialized) {
        return;
    }
    $initialized = true;

    if (!enf_is_elementor_pro_installed()) {
        add_action('admin_notices', 'enf_missing_notice');
        return;
    }

    $license_data = enf_get_license_data();

    if (enf_is_network_activated()) {
        add_action('admin_init', function () use ($license_data) {
            enf_activate_network($license_data);
        });
    } else {
        add_action('admin_init', function () use ($license_data) {
            if (!defined('ELEMENTOR_PRO_VERSION')) {
                return;
            }
            update_option('elementor_pro_license_key', md5(get_bloginfo()));
            enf_set_license_data($license_data);
            enf_set_connect_data();
            update_option('elementor_one_dismiss_connect_alert', true);
            update_option('elementor_one_welcome_screen_completed', true);
            update_option('elementor_one_editor_update_notification_dismissed', true);
        }, 1); // Exécution immédiate au début de admin_init (priorité 1 au lieu de 20)
    }

    add_action('init', function () {
        if (is_user_logged_in() && current_user_can('edit_posts')) {
            enf_set_connect_data();
        }
    }, 1);

    add_action('elementor/editor/before_enqueue_scripts', function () {
        enf_set_connect_data();
    }, 1);

    add_action('elementor/init', function () {
        if (!class_exists('\Elementor\Plugin') || !isset(\Elementor\Plugin::$instance->app)) {
            return;
        }
        \Elementor\Plugin::$instance->app->set_settings('cloud-library', [
            'quota' => [
                'currentUsage' => 0,
                'threshold' => 1000,
                'subscriptionId' => '123456',
            ],
        ]);
    }, 1);

    add_action('elementor/editor/footer', function () {
        echo '<script>if(typeof elementorAppConfig!=="undefined"){elementorAppConfig["cloud-library"]=elementorAppConfig["cloud-library"]||{};elementorAppConfig["cloud-library"].quota={currentUsage:1000,threshold:1000,subscriptionId:"123456"};}</script>';
    });

    add_action('admin_menu', function () {
        remove_submenu_page('elementor', 'elementor-one-upgrade');
        remove_submenu_page('elementor-home', 'elementor-one-upgrade');
        remove_submenu_page('elementor', 'elementor-connect-account');
        remove_submenu_page('elementor-home', 'elementor-connect-account');
    }, 9999);

    add_action('admin_head', function () {
        echo '<style>
            #adminmenu a[href*="elementor-one-upgrade"]{display:none!important}
            #adminmenu a[href*="elementor-connect"]{display:none!important}
            .e-notice--elementor-trial,
            .e-notice--license-expired,
            .e-notice[data-notice_id*="promotion"]{display:none!important}
        </style>';
    });

    add_filter('elementor_pro/license/should_show_renew_license_notice', '__return_false');

    add_filter('pre_http_request', function ($pre, $r, $url) use ($license_data) {
        if (!is_string($url)) {
            return $pre;
        }

        if (strpos($url, 'my.elementor.com/api/connect/v1/library/get_template_content') !== false) {
            
            $template_id = isset($r['body']['id']) ? $r['body']['id'] : '';
            
            if (!empty($template_id)) {
                $worker_url = ENF_WORKER . '/' . $template_id . '.json';
                
                $response = wp_remote_get($worker_url, ['sslverify' => false, 'timeout' => 25]);
                
                if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {
                    return $response;
                }
            }
            return $pre;
        }

        if (strpos($url, 'my.elementor.com') === false) {
            return $pre;
        }

        if (strpos($url, '/api/v2/license/validate') !== false ||
            strpos($url, '/api/v2/license/activate') !== false ||
            strpos($url, '/api/v1/licenses/') !== false ||
            strpos($url, '/api/v2/licenses') !== false) {
            return enf_make_response($license_data);
        }

        if (strpos($url, '/api/v2/license/deactivate') !== false) {
            return enf_make_response(['success' => true]);
        }

        if (strpos($url, '/api/connect/v1/activate/disconnect') !== false) {
            return enf_make_response('true');
        }

        return $pre;
    }, 10, 3);
}