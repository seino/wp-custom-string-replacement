<?php

/**
 * Plugin Name: Custom String Replacement
 * Description: 指定した文字列を自動的に置換するプラグイン
 * Version: 1.0
 * Author: Akihiro Seino
 * Author URI: https://github.com/seino
 * Requires PHP: 7.4
 * Text Domain: custom-string-replacement
 * Domain Path: /languages
 *
 * @package CustomStringReplacement
 */

if (!defined('ABSPATH')) {
    exit;
}

// 定数定義
define('CSR_VERSION', '1.0');
define('CSR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CSR_PLUGIN_URL', plugin_dir_url(__FILE__));

// クラスファイルの読み込み
require_once CSR_PLUGIN_DIR . 'includes/class-custom-string-replacement.php';
require_once CSR_PLUGIN_DIR . 'includes/class-custom-string-replacement-admin.php';

/**
 * プラグインの有効化時の処理
 *
 * @return void
 */
function csr_activate()
{
    // デフォルトの置換ルール
    $default_replacements = array(
        array('search' => '☎', 'replace' => '電話'),
        array('search' => '㈱', 'replace' => '(株)'),
        array('search' => '℡', 'replace' => '電話'),
        array('search' => '㈲', 'replace' => '(有)'),
        array('search' => '㈹', 'replace' => '(代)'),
        array('search' => '㎝', 'replace' => 'cm'),
        array('search' => '㎞', 'replace' => 'km'),
        array('search' => '㎏', 'replace' => 'kg'),
        array('search' => '（', 'replace' => '('),
        array('search' => '）', 'replace' => ')')
    );

    // オプションがなければ追加
    if (!get_option('custom_string_replacements')) {
        add_option('custom_string_replacements', $default_replacements);
    }
    if (!get_option('csr_enabled_post_types')) {
        add_option('csr_enabled_post_types', array('post', 'page'));
    }
}
register_activation_hook(__FILE__, 'csr_activate');

/**
 * プラグインの初期化
 *
 * @return void
 */
function csr_init()
{
    $plugin = new Custom_String_Replacement();
    $plugin->init();

    if (is_admin()) {
        $admin = new Custom_String_Replacement_Admin($plugin);
        $admin->init();
    }
}
add_action('init', 'csr_init', 10);

/**
 * アンインストール時の処理
 *
 * @return void
 */
function csr_uninstall()
{
    delete_option('custom_string_replacements');
    delete_option('csr_enabled_post_types');
}
register_uninstall_hook(__FILE__, 'csr_uninstall');
