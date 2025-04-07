<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// プラグインのオプションをすべて削除
delete_option('custom_string_replacements');
delete_option('csr_enabled_post_types');
