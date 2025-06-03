<?php
/*
Plugin Name: Label 20 CTA Buttons
Plugin URI: https://github.com/arnemulder/label20-cta-buttons
Description: Toon een zwevende CTA-knop met uitbreidbare contactopties.
Version: 1.0.0
Author: Arne Mulder
Author URI: https://label20.nl
License: GPLv2 or later
Update URI: https://github.com/arnemulder/label20-cta-buttons
*/

if (!defined('ABSPATH')) exit;

// Scripts en styles
function l20cta_enqueue_scripts() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css');
    wp_enqueue_style('l20cta-style', plugin_dir_url(__FILE__) . 'style.css');
    wp_enqueue_script('l20cta-script', plugin_dir_url(__FILE__) . 'script.js', [], false, true);

    // Opties ophalen
    $phone_number = get_option('l20cta_phone_number');
    $email_address = get_option('l20cta_email_address');
    $whatsapp_number = get_option('l20cta_whatsapp_number');
    $whatsapp_text = get_option('l20cta_whatsapp_text');
    $form_url = get_option('l20cta_form_url');

    // Links genereren
    $phone_url = ($phone_number) ? 'tel:' . preg_replace('/\D+/', '', $phone_number) : '';
    $email_url = ($email_address) ? 'mailto:' . sanitize_email($email_address) : '';
    $whatsapp_url = '';
    if ($whatsapp_number) {
        $clean_number = preg_replace('/\D+/', '', $whatsapp_number);
        $text = rawurlencode($whatsapp_text ?: '');
        $whatsapp_url = "https://api.whatsapp.com/send/?phone={$clean_number}&text={$text}";
    }

    wp_localize_script('l20cta-script', 'l20ctaData', [
        'phone_url' => $phone_url,
        'email_url' => $email_url,
        'whatsapp_url' => $whatsapp_url,
        'form_url' => esc_url($form_url),
        'phone_icon_color' => get_option('l20cta_phone_icon_color'),
        'email_icon_color' => get_option('l20cta_email_icon_color'),
        'whatsapp_icon_color' => get_option('l20cta_whatsapp_icon_color'),
        'form_icon_color' => get_option('l20cta_form_icon_color'),
        'phone_bg_color' => get_option('l20cta_phone_bg_color'),
        'email_bg_color' => get_option('l20cta_email_bg_color'),
        'whatsapp_bg_color' => get_option('l20cta_whatsapp_bg_color'),
        'form_bg_color' => get_option('l20cta_form_bg_color'),
        'phone_order' => get_option('l20cta_phone_order'),
        'email_order' => get_option('l20cta_email_order'),
        'whatsapp_order' => get_option('l20cta_whatsapp_order'),
        'form_order' => get_option('l20cta_form_order'),
        'main_bg_color' => get_option('l20cta_main_bg_color'),
        'main_icon_color' => get_option('l20cta_main_icon_color'),
		'form_target_blank' => get_option('l20cta_form_target_blank', false),
        'openings' => [
            'maandag' => get_option('l20cta_opening_maandag'),
            'dinsdag' => get_option('l20cta_opening_dinsdag'),
            'woensdag' => get_option('l20cta_opening_woensdag'),
            'donderdag' => get_option('l20cta_opening_donderdag'),
            'vrijdag' => get_option('l20cta_opening_vrijdag'),
            'zaterdag' => get_option('l20cta_opening_zaterdag'),
            'zondag' => get_option('l20cta_opening_zondag'),
        ]
    ]);
}
add_action('wp_enqueue_scripts', 'l20cta_enqueue_scripts');

// HTML output
function l20cta_display_buttons() {
    echo '<div class="l20cta-wrapper">
    <div class="l20cta-buttons"></div>
    <button id="l20cta-main-btn"><i class="fa fa-plus"></i></button>
</div>';
}
add_action('wp_footer', 'l20cta_display_buttons');

// Admin menu
function l20cta_register_settings_page() {
    add_options_page('Label 20 CTA Buttons', 'Label 20 CTA Buttons', 'manage_options', 'label-20-cta-buttons', 'l20cta_settings_page');
}
add_action('admin_menu', 'l20cta_register_settings_page');

// Admin pagina
function l20cta_settings_page() {
    ?>
    <div class="wrap">
        <h1>Label 20 CTA Buttons Instellingen</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('l20cta_settings_group');
            do_settings_sections('l20cta_settings_group');
            ?>

            <h2>Algemeen</h2>
            <table class="form-table">
                <tr>
                    <th>Achtergrondkleur hoofdknop</th>
                    <td><input type="color" name="l20cta_main_bg_color" value="<?php echo esc_attr(get_option('l20cta_main_bg_color')); ?>"></td>
                </tr>
                <tr>
                    <th>Kleur plus-icoon</th>
                    <td><input type="color" name="l20cta_main_icon_color" value="<?php echo esc_attr(get_option('l20cta_main_icon_color')); ?>"></td>
                </tr>
            </table>

            <h2>Buttons</h2>

            <h3>Telefoon</h3>
            <table class="form-table">
                <tr>
                    <th>Telefoonnummer (bijv. 0031612345678)</th>
                    <td><input type="text" name="l20cta_phone_number" value="<?php echo esc_attr(get_option('l20cta_phone_number')); ?>"></td>
                </tr>
                <tr>
                    <th>Volgorde</th>
                    <td><input type="number" name="l20cta_phone_order" value="<?php echo esc_attr(get_option('l20cta_phone_order')); ?>"></td>
                </tr>
                <tr>
                    <th>Achtergrondkleur</th>
                    <td><input type="color" name="l20cta_phone_bg_color" value="<?php echo esc_attr(get_option('l20cta_phone_bg_color')); ?>"></td>
                </tr>
                <tr>
                    <th>Kleur icoon</th>
                    <td><input type="color" name="l20cta_phone_icon_color" value="<?php echo esc_attr(get_option('l20cta_phone_icon_color')); ?>"></td>
                </tr>
            </table>

            <h3>E-mail</h3>
            <table class="form-table">
                <tr>
                    <th>E-mailadres</th>
                    <td><input type="email" name="l20cta_email_address" value="<?php echo esc_attr(get_option('l20cta_email_address')); ?>"></td>
                </tr>
                <tr>
                    <th>Volgorde</th>
                    <td><input type="number" name="l20cta_email_order" value="<?php echo esc_attr(get_option('l20cta_email_order')); ?>"></td>
                </tr>
                <tr>
                    <th>Achtergrondkleur</th>
                    <td><input type="color" name="l20cta_email_bg_color" value="<?php echo esc_attr(get_option('l20cta_email_bg_color')); ?>"></td>
                </tr>
                <tr>
                    <th>Kleur icoon</th>
                    <td><input type="color" name="l20cta_email_icon_color" value="<?php echo esc_attr(get_option('l20cta_email_icon_color')); ?>"></td>
                </tr>
            </table>

            <h3>WhatsApp</h3>
            <table class="form-table">
                <tr>
                    <th>WhatsApp telefoonnummer (bijv. 0031612345678)</th>
                    <td><input type="text" name="l20cta_whatsapp_number" value="<?php echo esc_attr(get_option('l20cta_whatsapp_number')); ?>"></td>
                </tr>
                <tr>
                    <th>WhatsApp standaardtekst</th>
                    <td><input type="text" name="l20cta_whatsapp_text" value="<?php echo esc_attr(get_option('l20cta_whatsapp_text')); ?>"></td>
                </tr>
                <tr>
                    <th>Volgorde</th>
                    <td><input type="number" name="l20cta_whatsapp_order" value="<?php echo esc_attr(get_option('l20cta_whatsapp_order')); ?>"></td>
                </tr>
                <tr>
                    <th>Achtergrondkleur</th>
                    <td><input type="color" name="l20cta_whatsapp_bg_color" value="<?php echo esc_attr(get_option('l20cta_whatsapp_bg_color')); ?>"></td>
                </tr>
                <tr>
                    <th>Kleur icoon</th>
                    <td><input type="color" name="l20cta_whatsapp_icon_color" value="<?php echo esc_attr(get_option('l20cta_whatsapp_icon_color')); ?>"></td>
                </tr>
            </table>

            <h3>Formulier</h3>
            <table class="form-table">
                <tr>
                    <th>Formulier URL</th>
                    <td><input type="url" name="l20cta_form_url" value="<?php echo esc_attr(get_option('l20cta_form_url')); ?>"></td>
                </tr>
                <tr>
                    <th>Volgorde</th>
                    <td><input type="number" name="l20cta_form_order" value="<?php echo esc_attr(get_option('l20cta_form_order')); ?>"></td>
                </tr>
                <tr>
                    <th>Achtergrondkleur</th>
                    <td><input type="color" name="l20cta_form_bg_color" value="<?php echo esc_attr(get_option('l20cta_form_bg_color')); ?>"></td>
                </tr>
                <tr>
                    <th>Kleur icoon</th>
                    <td><input type="color" name="l20cta_form_icon_color" value="<?php echo esc_attr(get_option('l20cta_form_icon_color')); ?>"></td>
                </tr>
				<tr>
    <th>Open formulier in nieuw venster?</th>
    <td>
        <input type="checkbox" name="l20cta_form_target_blank" value="1" <?php checked(1, get_option('l20cta_form_target_blank'), true); ?>>
        Ja
    </td>
</tr>
			</table>
            <h2>Openingstijden telefoon</h2>
<p>Gebruik formaat <code>09:00-17:00</code>. Laat leeg als je gesloten bent.</p>
<table class="form-table">
<?php
$days = ['maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag'];
foreach ($days as $day) {
    $key = strtolower($day);
    ?>
    <tr>
        <th><?php echo ucfirst($day); ?></th>
        <td>
            <input type="text" name="l20cta_opening_<?php echo $key; ?>" value="<?php echo esc_attr(get_option("l20cta_opening_$key")); ?>" placeholder=" ">
        </td>
    </tr>
<?php } ?>
</table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings
function l20cta_register_settings() {
    register_setting('l20cta_settings_group', 'l20cta_phone_number');
    register_setting('l20cta_settings_group', 'l20cta_email_address');
    register_setting('l20cta_settings_group', 'l20cta_whatsapp_number');
    register_setting('l20cta_settings_group', 'l20cta_whatsapp_text');
    register_setting('l20cta_settings_group', 'l20cta_form_url');
	register_setting('l20cta_settings_group', 'l20cta_form_target_blank');


    register_setting('l20cta_settings_group', 'l20cta_phone_icon_color');
    register_setting('l20cta_settings_group', 'l20cta_email_icon_color');
    register_setting('l20cta_settings_group', 'l20cta_whatsapp_icon_color');
    register_setting('l20cta_settings_group', 'l20cta_form_icon_color');

    register_setting('l20cta_settings_group', 'l20cta_phone_bg_color');
    register_setting('l20cta_settings_group', 'l20cta_email_bg_color');
    register_setting('l20cta_settings_group', 'l20cta_whatsapp_bg_color');
    register_setting('l20cta_settings_group', 'l20cta_form_bg_color');

    register_setting('l20cta_settings_group', 'l20cta_phone_order');
    register_setting('l20cta_settings_group', 'l20cta_email_order');
    register_setting('l20cta_settings_group', 'l20cta_whatsapp_order');
    register_setting('l20cta_settings_group', 'l20cta_form_order');
	$dagen = ['maandag','dinsdag','woensdag','donderdag','vrijdag','zaterdag','zondag'];
foreach ($dagen as $dag) {
    register_setting('l20cta_settings_group', "l20cta_opening_" . strtolower($dag));
}

    register_setting('l20cta_settings_group', 'l20cta_main_bg_color');
    register_setting('l20cta_settings_group', 'l20cta_main_icon_color');
}
add_action('admin_init', 'l20cta_register_settings');

// Auto update via GitHub
require_once plugin_dir_path(__FILE__) . 'includes/plugin-update-checker/plugin-update-checker.php';

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/arnemulder/label20-cta-buttons/',
    __FILE__,
    'label20-cta-buttons'
);

$updateChecker->setBranch('main');
// Als je een GitHub-token nodig hebt voor private repo's:
// $updateChecker->setAuthentication('je_github_token');
