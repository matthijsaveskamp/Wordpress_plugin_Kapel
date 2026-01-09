<?php
/**
 * Kapel Footer Gallery Uninstall
 * 
 * Verwijdert alle plugin data bij deinstallatie
 */

// Voorkom directe toegang
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Verwijder alle plugin opties
delete_option('kfg_gallery_images');
delete_option('kfg_transition_speed');
delete_option('kfg_display_duration');
delete_option('kfg_show_filename');
delete_option('kfg_random_order');
delete_option('kfg_image_captions');

// Voor multisite
if (is_multisite()) {
    $sites = get_sites(array('number' => 0));
    foreach ($sites as $site) {
        switch_to_blog($site->blog_id);
        
        delete_option('kfg_gallery_images');
        delete_option('kfg_transition_speed');
        delete_option('kfg_display_duration');
        delete_option('kfg_show_filename');
        delete_option('kfg_random_order');
        delete_option('kfg_image_captions');
        
        restore_current_blog();
    }
}
