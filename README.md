# Custom String Replacement（カスタム文字列置換）

コンテンツ内の指定された文字列を自動的に置換する WordPress プラグイン。

## 説明

Custom String Replacement は、Web サイトのコンテンツ全体で特定の文字列を代替文字列に自動的に置換できるシンプルな WordPress プラグインです。これは、用語の標準化、一般的な誤字の修正、特殊文字をより Web 向けの代替文字に置き換えるのに特に便利です。

このプラグインは、以下に対して置換を適用します：

- 投稿コンテンツ
- 投稿タイトル
- 投稿抜粋
- REST API レスポンス

## 機能

- 置換ルールを管理するための使いやすい管理インターフェース
- 置換を適用する投稿タイプの選択
- 特殊文字用の有用なデフォルト置換セット
- WordPress REST API とのシームレスな統合
- パフォーマンスへの影響を最小限に抑えた軽量設計

## インストール

1. `custom-string-replacement`フォルダを`/wp-content/plugins/`ディレクトリにアップロード
2. WordPress の「プラグイン」メニューからプラグインを有効化
3. 設定 > 文字列置換に移動して置換ルールを構成

## 設定

### 置換ルールの設定

1. WordPress 管理パネルの設定 > 文字列置換に移動
2. プラグインには事前設定された複数のデフォルト置換があります
3. 「新しい置換ルールを追加」ボタンをクリックして、新しい置換ルールを追加
4. 各ルールについて、以下を指定：
   - 検索文字列：置換したいテキスト
   - 置換後の文字列：代わりに表示したいテキスト
5. 「変更を保存」をクリックしてルールを適用

### 投稿タイプの選択

置換を適用する投稿タイプを選択できます：

1. 設定ページで、「対象の投稿タイプ」の下でチェックボックスをオン/オフ
2. デフォルトでは、標準の投稿とページに置換が適用されます
3. カスタム投稿タイプもサポートされています

## デフォルトの置換

プラグインには以下のデフォルト置換が付属しています：

| 検索 | 置換 |
| ---- | ---- |
| ☎    | 電話 |
| ㈱   | (株) |
| ℡    | 電話 |
| ㈲   | (有) |
| ㈹   | (代) |
| ㎝   | cm   |
| ㎞   | km   |
| ㎏   | kg   |
| （   | (    |
| ）   | )    |

## 開発者向け情報

### フックとフィルター

プラグインは WordPress フィルターを使用して置換を適用します：

- `the_content`
- `the_title`
- `the_excerpt`
- `rest_prepare_{post_type}`

### オプション

プラグインは WordPress データベースに 2 つのオプションを保存します：

- `custom_string_replacements`：置換ルールの配列
- `csr_enabled_post_types`：置換を適用する投稿タイプ名の配列

### ディレクトリ構造

```
custom-string-replacement/
├── assets/
│   ├── css/
│   │   └── admin.css
│   └── js/
│       └── admin.js
├── includes/
│   ├── class-custom-string-replacement.php
│   └── class-custom-string-replacement-admin.php
├── custom-string-replacement.php
├── README.md
├── readme.txt
└── uninstall.php
```

## ライセンス

このプラグインは GPL v2 以降の下でライセンスされています。

## 作者

- Akihiro Seino
- GitHub: [https://github.com/seino](https://github.com/seino)

## 変更履歴

### 1.0

- 初回リリース
