<?php
/**
 * Plugin Name: Kapel Footer Gallery 2
 * Plugin URI: https://svdianadenekamp.nl
 * Description: Toon roterende foto's uit je mediabibliotheek in de footer
 * Version: 2.0.0
 * Author: (c) Matthijs Aveskamp 2025
 * Author URI: https://svdianadenekamp.nl
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kapel-footer-gallery
 * Requires at least: 5.0
 * Requires PHP: 5.6
 */

// Voorkom directe toegang
if (!defined('ABSPATH')) {
    exit;
}

// Check PHP versie
if (version_compare(PHP_VERSION, '5.6', '<')) {
    add_action('admin_notices', 'kfg_php_version_notice');
    function kfg_php_version_notice() {
        echo '<div class="error"><p><strong>Kapel Footer Gallery</strong> vereist minimaal PHP 5.6. Je gebruikt momenteel PHP ' . PHP_VERSION . '. Gelieve PHP bij te werken.</p></div>';
    }
    return;
}

// Check WordPress versie
global $wp_version;
if (version_compare($wp_version, '5.0', '<')) {
    add_action('admin_notices', 'kfg_wp_version_notice');
    function kfg_wp_version_notice() {
        echo '<div class="error"><p><strong>Kapel Footer Gallery</strong> vereist minimaal WordPress 5.0. Je gebruikt momenteel WordPress ' . get_bloginfo('version') . '. Gelieve WordPress bij te werken.</p></div>';
    }
    return;
}

// Plugin constanten
define('KFG_VERSION', '1.0.0');
define('KFG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('KFG_PLUGIN_URL', plugin_dir_url(__FILE__));

// Hoofdklasse
class Kapel_Footer_Gallery {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Textdomain voor vertalingen
        add_action('init', array($this, 'load_textdomain'));
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_action_links'));
        
        // Frontend hooks
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_scripts'));
        add_shortcode('kapel_footer_gallery', array($this, 'gallery_shortcode'));
        
        // Widget
        add_action('widgets_init', array($this, 'register_widget'));
    }
    
    // Laad textdomain voor vertalingen
    public function load_textdomain() {
        load_plugin_textdomain('kapel-footer-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    // Admin menu toevoegen
    public function add_admin_menu() {
        add_options_page(
            'Kapel Footer Gallery',
            'Footer Gallery',
            'manage_options',
            'kapel-footer-gallery',
            array($this, 'settings_page')
        );
    }
    
    // Voeg actie links toe op plugin pagina
    public function add_action_links($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=kapel-footer-gallery') . '">Instellingen</a>';
        $info_link = '<a href="#" id="kfg-info-link" style="color: #2271b1; font-weight: 600;">ℹ️ Info</a>';
        array_unshift($links, $settings_link, $info_link);
        
        // Voeg inline script toe voor info popup
        add_action('admin_footer', array($this, 'add_info_popup_script'));
        
        return $links;
    }
    
    // Info popup script
    public function add_info_popup_script() {
        if (get_current_screen()->id !== 'plugins') {
            return;
        }
        ?>
        <style>
        #kfg-info-modal {
            display: none;
            position: fixed;
            z-index: 999999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        #kfg-info-modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-height: 85vh;
            overflow-y: auto;
        }
        #kfg-info-modal h2 {
            margin-top: 0;
            color: #23282d;
        }
        #kfg-info-modal-close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            line-height: 20px;
            cursor: pointer;
        }
        #kfg-info-modal-close:hover,
        #kfg-info-modal-close:focus {
            color: #000;
        }
        #kfg-info-modal ol {
            padding-left: 20px;
            line-height: 1.8;
        }
        #kfg-info-modal p {
            line-height: 1.6;
            margin: 15px 0;
        }
        #kfg-info-modal code {
            background: #f0f0f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        #kfg-info-modal .kfg-copyright {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
        </style>
        <div id="kfg-info-modal">
            <div id="kfg-info-modal-content">
                <span id="kfg-info-modal-close">&times;</span>
                <h2>Kapel Footer Gallery</h2>
                <p><strong>Om foto's toe te voegen aan de footer gallery:</strong></p>
                <ol>
                    <li>Ga naar Instellingen → Footer Gallery</li>
                    <li>Klik op "Foto's Toevoegen"</li>
                    <li>Selecteer foto's uit je mediabibliotheek</li>
                    <li>Pas de instellingen aan naar wens</li>
                    <li>Sla de wijzigingen op</li>
                </ol>
                <p><strong>Gebruik:</strong></p>
                <p>Gebruik de shortcode <code>[kapel_footer_gallery]</code> om de gallery te tonen.</p>
                <div class="kfg-copyright">
                    &copy; Matthijs Aveskamp 2025<br>
                    <a href="https://svdianadenekamp.nl" target="_blank">https://svdianadenekamp.nl</a>
                </div>
            </div>
        </div>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var modal = $('#kfg-info-modal');
            
            $(document).on('click', '#kfg-info-link', function(e) {
                e.preventDefault();
                modal.fadeIn(200);
            });
            
            $('#kfg-info-modal-close').on('click', function() {
                modal.fadeOut(200);
            });
            
            $(window).on('click', function(e) {
                if ($(e.target).is('#kfg-info-modal')) {
                    modal.fadeOut(200);
                }
            });
        });
        </script>
        <?php
    }
    
    // Settings registreren
    public function register_settings() {
        register_setting('kapel_footer_gallery_settings', 'kfg_gallery_images', array(
            'sanitize_callback' => array($this, 'sanitize_gallery_images')
        ));
        register_setting('kapel_footer_gallery_settings', 'kfg_transition_speed', array(
            'sanitize_callback' => array($this, 'sanitize_transition_speed')
        ));
        register_setting('kapel_footer_gallery_settings', 'kfg_display_duration', array(
            'sanitize_callback' => array($this, 'sanitize_display_duration')
        ));
        register_setting('kapel_footer_gallery_settings', 'kfg_show_filename', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox')
        ));
        register_setting('kapel_footer_gallery_settings', 'kfg_random_order', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox')
        ));
        register_setting('kapel_footer_gallery_settings', 'kfg_image_captions', array(
            'sanitize_callback' => array($this, 'sanitize_captions')
        ));
    }
    
    // Sanitize gallery images
    public function sanitize_gallery_images($input) {
        if (!is_array($input)) {
            return array();
        }
        return array_map('absint', $input);
    }
    
    // Sanitize transition speed
    public function sanitize_transition_speed($input) {
        $value = absint($input);
        return ($value >= 100 && $value <= 5000) ? $value : 1000;
    }
    
    // Sanitize display duration
    public function sanitize_display_duration($input) {
        $value = absint($input);
        return ($value >= 1000 && $value <= 30000) ? $value : 5000;
    }
    
    // Sanitize checkbox
    public function sanitize_checkbox($input) {
        return ($input == 1) ? 1 : 0;
    }
    
    // Sanitize captions
    public function sanitize_captions($input) {
        if (!is_array($input)) {
            return array();
        }
        $sanitized = array();
        foreach ($input as $key => $value) {
            $key = absint($key);
            if ($key > 0) {
                $sanitized[$key] = sanitize_text_field($value);
            }
        }
        return $sanitized;
    }
    
    // Admin scripts en styles
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_kapel-footer-gallery' !== $hook) {
            return;
        }
        
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script(
            'kfg-admin',
            KFG_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable'),
            KFG_VERSION,
            true
        );
        wp_enqueue_style(
            'kfg-admin',
            KFG_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            KFG_VERSION
        );
    }
    
    // Frontend scripts en styles
    public function enqueue_frontend_scripts() {
        wp_enqueue_style(
            'kfg-frontend',
            KFG_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            KFG_VERSION
        );
        wp_enqueue_script(
            'kfg-frontend',
            KFG_PLUGIN_URL . 'assets/js/frontend.js',
            array('jquery'),
            KFG_VERSION,
            true
        );
        
        // Instellingen doorgeven aan JavaScript
        $settings = array(
            'transitionSpeed' => get_option('kfg_transition_speed', 1000),
            'displayDuration' => get_option('kfg_display_duration', 5000),
            'showFilename' => get_option('kfg_show_filename', 0)
        );
        wp_localize_script('kfg-frontend', 'kfgSettings', $settings);
    }
    
    // Settings pagina
    public function settings_page() {
        $gallery_images = get_option('kfg_gallery_images', array());
        $transition_speed = get_option('kfg_transition_speed', 1000);
        $display_duration = get_option('kfg_display_duration', 5000);
        $show_filename = get_option('kfg_show_filename', 0);
        $random_order = get_option('kfg_random_order', 0);
        $image_captions = get_option('kfg_image_captions', array());
        ?>
        <div class="wrap">
            <h1>Kapel Footer Gallery Instellingen</h1>
            <form method="post" action="options.php">
                <?php settings_fields('kapel_footer_gallery_settings'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label>Galerij Foto's</label>
                        </th>
                        <td>
                            <div id="kfg-gallery-container">
                                <div id="kfg-selected-images">
                                    <?php
                                    if (!empty($gallery_images)) {
                                        foreach ($gallery_images as $image_id) {
                                            $image_url = wp_get_attachment_image_url($image_id, 'thumbnail');
                                            if ($image_url) {
                                                $caption = isset($image_captions[$image_id]) ? $image_captions[$image_id] : '';
                                                echo '<div class="kfg-image-preview" data-id="' . esc_attr($image_id) . '">';
                                                echo '<img src="' . esc_url($image_url) . '" />';
                                                echo '<span class="kfg-remove-image">&times;</span>';
                                                echo '<input type="hidden" name="kfg_gallery_images[]" value="' . esc_attr($image_id) . '" />';
                                                echo '<input type="text" class="kfg-caption-input" name="kfg_image_captions[' . esc_attr($image_id) . ']" value="' . esc_attr($caption) . '" placeholder="Optionele tekst..." />';
                                                echo '</div>';
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <button type="button" class="button" id="kfg-add-images">Foto's Toevoegen</button>
                            </div>
                            <p class="description">Selecteer foto's uit je mediabibliotheek die je wilt tonen in de footer.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="kfg_transition_speed">Overgangssnelheid (ms)</label>
                        </th>
                        <td>
                            <input type="number" id="kfg_transition_speed" name="kfg_transition_speed" 
                                   value="<?php echo esc_attr($transition_speed); ?>" min="100" max="5000" step="100" />
                            <p class="description">Duur van de overgang tussen foto's in milliseconden (standaard: 1000).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="kfg_display_duration">Weergaveduur (ms)</label>
                        </th>
                        <td>
                            <input type="number" id="kfg_display_duration" name="kfg_display_duration" 
                                   value="<?php echo esc_attr($display_duration); ?>" min="1000" max="30000" step="1000" />
                            <p class="description">Hoe lang elke foto wordt getoond in milliseconden (standaard: 5000).</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="kfg_show_filename">Toon bestandsnaam</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="kfg_show_filename" name="kfg_show_filename" value="1" 
                                       <?php checked($show_filename, 1); ?> />
                                Toon de bestandsnaam (zonder extensie) onder elke foto
                            </label>
                            <p class="description">Als je een aangepaste tekst per foto hebt ingevuld, wordt die getoond in plaats van de bestandsnaam.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="kfg_random_order">Willekeurige volgorde</label>
                        </th>
                        <td>
                            <label>
                                <input type="checkbox" id="kfg_random_order" name="kfg_random_order" value="1" 
                                       <?php checked($random_order, 1); ?> />
                                Toon foto's in willekeurige volgorde (bij elke paginalading anders)
                            </label>
                            <p class="description">Wanneer uitgeschakeld worden foto's getoond in de volgorde waarin je ze hebt toegevoegd.</p>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="kfg-usage-info">
                <h2>Gebruik</h2>
                <p><strong>Shortcode:</strong> <code>[kapel_footer_gallery]</code></p>
                <p><strong>PHP code:</strong> <code>&lt;?php echo do_shortcode('[kapel_footer_gallery]'); ?&gt;</code></p>
                <p>Je kunt ook de widget gebruiken om de galerij toe te voegen aan je footer widget area.</p>
                <hr style="margin: 20px 0; border: none; border-top: 1px solid #ddd;">
                <p style="text-align: center; color: #666; font-size: 13px;">&copy; Matthijs Aveskamp 2025</p>
            </div>
        </div>
        <?php
    }
    
    // Shortcode functie
    public function gallery_shortcode($atts) {
        $atts = shortcode_atts(array(
            'height' => '200px',
            'transition' => 'fade'
        ), $atts);
        
        $gallery_images = get_option('kfg_gallery_images', array());
        
        if (empty($gallery_images)) {
            return '';
        }
        
        // Shuffle de afbeeldingen als random order is ingeschakeld
        $random_order = get_option('kfg_random_order', 0);
        if ($random_order) {
            shuffle($gallery_images);
        }
        
        $show_filename = get_option('kfg_show_filename', 0);
        $image_captions = get_option('kfg_image_captions', array());
        
        $output = '<div class="kapel-footer-gallery" style="height: ' . esc_attr($atts['height']) . ';" data-transition="' . esc_attr($atts['transition']) . '" data-show-filename="' . esc_attr($show_filename) . '">';
        
        foreach ($gallery_images as $index => $image_id) {
            $image_url = wp_get_attachment_image_url($image_id, 'full');
            $image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
            $attachment = get_post($image_id);
            $filename = $attachment ? get_the_title($image_id) : '';
            if (empty($filename)) {
                $filename = basename(get_attached_file($image_id), '.' . pathinfo(get_attached_file($image_id), PATHINFO_EXTENSION));
                $filename = str_replace('-', ' ', $filename); // Vervang streepjes door spaties
            }
            
            // Gebruik aangepaste caption indien aanwezig, anders bestandsnaam
            $display_text = '';
            if (isset($image_captions[$image_id]) && !empty($image_captions[$image_id])) {
                $display_text = $image_captions[$image_id];
            } else {
                $display_text = $filename;
            }
            
            if ($image_url) {
                $active_class = ($index === 0) ? ' active' : '';
                $output .= '<div class="kfg-slide' . $active_class . '" data-filename="' . esc_attr($display_text) . '">';
                $output .= '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" />';
                if ($show_filename && $display_text) {
                    $output .= '<div class="kfg-filename">' . esc_html($display_text) . '</div>';
                }
                $output .= '</div>';
            }
        }
        
        $output .= '</div>';
        
        return $output;
    }
    
    // Widget registreren
    public function register_widget() {
        register_widget('Kapel_Footer_Gallery_Widget');
    }
}

// Widget klasse
class Kapel_Footer_Gallery_Widget extends WP_Widget {
    
    public function __construct() {
        parent::__construct(
            'kapel_footer_gallery_widget',
            'Kapel Footer Gallery',
            array('description' => 'Toon roterende foto\'s in de footer')
        );
    }
    
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }
        
        $height = !empty($instance['height']) ? $instance['height'] : '200px';
        $transition = !empty($instance['transition']) ? $instance['transition'] : 'fade';
        
        echo do_shortcode('[kapel_footer_gallery height="' . esc_attr($height) . '" transition="' . esc_attr($transition) . '"]');
        
        echo $args['after_widget'];
    }
    
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $height = !empty($instance['height']) ? $instance['height'] : '200px';
        $transition = !empty($instance['transition']) ? $instance['transition'] : 'fade';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">Titel:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('height')); ?>">Hoogte:</label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('height')); ?>" 
                   name="<?php echo esc_attr($this->get_field_name('height')); ?>" type="text" 
                   value="<?php echo esc_attr($height); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('transition')); ?>">Overgang:</label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('transition')); ?>" 
                    name="<?php echo esc_attr($this->get_field_name('transition')); ?>">
                <option value="fade" <?php selected($transition, 'fade'); ?>>Fade</option>
                <option value="slide" <?php selected($transition, 'slide'); ?>>Slide</option>
            </select>
        </p>
        <?php
    }
    
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['height'] = (!empty($new_instance['height'])) ? strip_tags($new_instance['height']) : '200px';
        $instance['transition'] = (!empty($new_instance['transition'])) ? strip_tags($new_instance['transition']) : 'fade';
        return $instance;
    }
}

// Initialiseer de plugin
function kapel_footer_gallery_init() {
    Kapel_Footer_Gallery::get_instance();
}
add_action('plugins_loaded', 'kapel_footer_gallery_init');
