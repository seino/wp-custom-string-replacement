<?php

/**
 * Custom String Replacement メインクラス
 *
 * @package CustomStringReplacement
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 文字列置換を行うメインクラス
 */
class Custom_String_Replacement
{
    /**
     * オプション名
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
     * 初期化
     *
     * @return void
     */
    public function init()
    {
        // コンテンツフィルターの追加（優先度を10に設定）
        add_filter('the_content', array($this, 'replace_strings'), 10);
        add_filter('the_title', array($this, 'replace_strings'), 10);
        add_filter('the_excerpt', array($this, 'replace_strings'), 10);

        // REST APIフィルターの追加
        add_filter('rest_prepare_post', array($this, 'filter_rest_response'), 10, 3);
        add_filter('rest_prepare_page', array($this, 'filter_rest_response'), 10, 3);

        // カスタム投稿タイプのフィルター追加
        $post_types = $this->get_enabled_post_types();
        foreach ($post_types as $post_type) {
            if (!in_array($post_type, array('post', 'page'), true)) {
                add_filter("rest_prepare_{$post_type}", array($this, 'filter_rest_response'), 10, 3);
            }
        }
    }

    /**
     * 文字列置換の実行
     *
     * @param string $content 置換対象のコンテンツ
     * @return string 置換後のコンテンツ
     */
    public function replace_strings($content)
    {
        if (!is_string($content)) {
            return $content;
        }

        $replacements = get_option($this->option_name, array());
        if (empty($replacements)) {
            return $content;
        }

        $search = array_column($replacements, 'search');
        $replace = array_column($replacements, 'replace');
        return str_replace($search, $replace, $content);
    }

    /**
     * REST APIレスポンスのフィルタリング
     *
     * @param WP_REST_Response $response レスポンスオブジェクト
     * @param WP_Post $post 投稿オブジェクト
     * @param WP_REST_Request $request リクエストオブジェクト
     * @return WP_REST_Response 修正されたレスポンス
     */
    public function filter_rest_response($response, $post, $request)
    {
        if (!in_array($post->post_type, $this->get_enabled_post_types(), true)) {
            return $response;
        }

        if (isset($response->data['title']['rendered'])) {
            $response->data['title']['rendered'] = $this->replace_strings(
                $response->data['title']['rendered']
            );
        }

        if (isset($response->data['content']['rendered'])) {
            $response->data['content']['rendered'] = $this->replace_strings(
                $response->data['content']['rendered']
            );
        }

        if (isset($response->data['excerpt']['rendered'])) {
            $response->data['excerpt']['rendered'] = $this->replace_strings(
                $response->data['excerpt']['rendered']
            );
        }

        return $response;
    }

    /**
     * 有効な投稿タイプを取得
     *
     * @return array 有効な投稿タイプの配列
     */
    public function get_enabled_post_types()
    {
        $saved_types = get_option($this->post_types_option, array('post', 'page'));
        $registered_types = get_post_types(array('public' => true), 'names');
        return array_intersect($saved_types, $registered_types);
    }

    /**
     * 置換ルールを取得
     *
     * @return array 置換ルールの配列
     */
    public function get_replacements()
    {
        return get_option($this->option_name, array());
    }
}
