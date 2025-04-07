<?php

/**
 * Custom String Replacement 管理画面クラス
 *
 * @package CustomStringReplacement
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 管理画面の機能を提供するクラス
 */
class Custom_String_Replacement_Admin
{
    /**
     * 置換ルールオプション名
     *
     * @var string
     */
    private $option_name = 'custom_string_replacements';

    /**
     * 投稿タイプオプション名
     *
     * @var string
     */
    private $post_types_option = 'csr_enabled_post_types';

    /**
     * メインプラグインオブジェクト
     *
     * @var Custom_String_Replacement
     */
    private $plugin;

    /**
     * コンストラクタ
     *
     * @param Custom_String_Replacement $plugin メインプラグインオブジェクト
     */
    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * 初期化処理
     *
     * @return void
     */
    public function init()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * 管理画面用スクリプトの読み込み
     *
     * @param string $hook 現在の管理画面ページのフック名
     * @return void
     */
    public function enqueue_admin_assets($hook)
    {
        if ('settings_page_string-replacement-settings' !== $hook) {
            return;
        }

        wp_enqueue_style(
            'custom-string-replacement-admin',
            CSR_PLUGIN_URL . 'assets/css/admin.css',
            array(),
            CSR_VERSION
        );

        wp_enqueue_script(
            'custom-string-replacement-admin',
            CSR_PLUGIN_URL . 'assets/js/admin.js',
            array('jquery'),
            CSR_VERSION,
            true
        );
    }

    /**
     * 管理メニューに設定ページを追加
     *
     * @return void
     */
    public function add_admin_menu()
    {
        add_options_page(
            esc_html__('文字列置換設定', 'custom-string-replacement'),
            esc_html__('文字列置換', 'custom-string-replacement'),
            'manage_options',
            'string-replacement-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * 設定を登録
     *
     * @return void
     */
    public function register_settings()
    {
        register_setting(
            'string_replacement_settings',
            $this->option_name,
            array($this, 'sanitize_replacements')
        );

        register_setting(
            'string_replacement_settings',
            $this->post_types_option,
            array($this, 'sanitize_post_types')
        );
    }

    /**
     * 置換ルールデータのサニタイズ
     *
     * @param array $input 入力データ
     * @return array サニタイズされたデータ
     */
    public function sanitize_replacements($input)
    {
        $new_input = array();
        if (is_array($input)) {
            foreach ($input as $item) {
                if (!empty($item['search'])) {
                    $new_input[] = array(
                        'search' => sanitize_text_field($item['search']),
                        'replace' => sanitize_text_field($item['replace'])
                    );
                }
            }
        }
        return $new_input;
    }

    /**
     * 投稿タイプデータのサニタイズ
     *
     * @param array $input 入力データ
     * @return array サニタイズされたデータ
     */
    public function sanitize_post_types($input)
    {
        if (!is_array($input)) {
            return array('post', 'page');
        }
        $registered_types = get_post_types(array('public' => true), 'names');
        return array_intersect($input, $registered_types);
    }

    /**
     * 設定ページのレンダリング
     *
     * @return void
     */
    public function render_settings_page()
    {
        $replacements = $this->plugin->get_replacements();
        $enabled_post_types = $this->plugin->get_enabled_post_types();
?>
        <div class="wrap string-replacement-settings">
            <h1><?php echo esc_html__('文字列置換設定', 'custom-string-replacement'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('string_replacement_settings'); ?>

                <!-- 投稿タイプの設定 -->
                <h2><?php echo esc_html__('対象の投稿タイプ', 'custom-string-replacement'); ?></h2>
                <table class="form-table">
                    <tr>
                        <th><?php echo esc_html__('投稿タイプ', 'custom-string-replacement'); ?></th>
                        <td>
                            <?php
                            $post_types = get_post_types(array('public' => true), 'objects');
                            foreach ($post_types as $post_type) :
                                $checked = in_array($post_type->name, $enabled_post_types, true);
                            ?>
                                <label style="display: block; margin-bottom: 5px;">
                                    <input type="checkbox"
                                        name="csr_enabled_post_types[]"
                                        value="<?php echo esc_attr($post_type->name); ?>"
                                        <?php checked($checked); ?>>
                                    <?php echo esc_html($post_type->label); ?>
                                    (<?php echo esc_html($post_type->name); ?>)
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                </table>

                <!-- 置換ルールの設定 -->
                <h2><?php echo esc_html__('置換ルール', 'custom-string-replacement'); ?></h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th width="40%"><?php echo esc_html__('検索文字列', 'custom-string-replacement'); ?></th>
                            <th width="40%"><?php echo esc_html__('置換後の文字列', 'custom-string-replacement'); ?></th>
                            <th width="20%"><?php echo esc_html__('操作', 'custom-string-replacement'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="replacement-rows">
                        <?php
                        if (!empty($replacements)) {
                            foreach ($replacements as $index => $replacement) {
                                $this->render_replacement_row($index, $replacement);
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <p>
                    <button type="button" class="button" id="add-new-row">
                        <?php echo esc_html__('新しい置換ルールを追加', 'custom-string-replacement'); ?>
                    </button>
                </p>

                <?php submit_button(); ?>
            </form>
        </div>

        <script type="text/template" id="row-template">
            <tr>
                <td>
                    <input type="text"
                           name="custom_string_replacements[{{index}}][search]"
                           class="regular-text">
                </td>
                <td>
                    <input type="text"
                           name="custom_string_replacements[{{index}}][replace]"
                           class="regular-text">
                </td>
                <td>
                    <button type="button" class="button remove-row"><?php echo esc_html__('削除', 'custom-string-replacement'); ?></button>
                </td>
            </tr>
        </script>
    <?php
    }

    /**
     * 置換ルール行のレンダリング
     *
     * @param int $index インデックス
     * @param array $replacement 置換ルールデータ
     * @return void
     */
    private function render_replacement_row($index, $replacement)
    {
    ?>
        <tr>
            <td>
                <input type="text"
                    name="custom_string_replacements[<?php echo esc_attr($index); ?>][search]"
                    value="<?php echo esc_attr($replacement['search']); ?>"
                    class="regular-text">
            </td>
            <td>
                <input type="text"
                    name="custom_string_replacements[<?php echo esc_attr($index); ?>][replace]"
                    value="<?php echo esc_attr($replacement['replace']); ?>"
                    class="regular-text">
            </td>
            <td>
                <button type="button" class="button remove-row"><?php echo esc_html__('削除', 'custom-string-replacement'); ?></button>
            </td>
        </tr>
<?php
    }
}
