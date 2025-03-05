# このファイルについて
LaravelにLaravel Pintを導入する手順・設置絵について記述する  
Pint（ピント） は、Laravel に公式に推奨されている コードフォーマッター  
PHP-CS-Fixer をベースにしており、Laravel のコーディング規約に沿った整形が可能  

# 手順
導入手順について記述する

##  Laravel Pint(PHP-CS-Fixer) インストール
Docker 内の app コンテナに入って ライブラリをインストールする  
`composer require --dev laravel/pint`  

## 設定ファイル（pint.json）を作成
プロジェクトのルートディレクトリ（app/）に pint.json というファイルを作成し、以下のように記述する

### pint.jsonの記述内容
```
{
    "preset": "laravel",
    "rules": {
        "array_syntax": {"syntax": "short"},
        "no_unused_imports": true,
        "ordered_imports": {"sort_algorithm": "alpha"},
        "binary_operator_spaces": {"default": "single_space"},
        "concat_space": {"spacing": "one"},
        "single_quote": true,
        "phpdoc_order": true,
        "phpdoc_no_empty_return": false
    },
    "exclude": [
        "storage",
        "vendor",
        "bootstrap"
    ]
}
```

### コードを整形する
以下のコマンドを実行すると、コードの静的解析が行われる。  
`vendor/bin/pint`  

完了。

# その他
設定の備考

## pint.jsonの設定値について説明
```
{
    "preset": "laravel",
    "rules": {
        "array_syntax": {"syntax": "short"},        // 配列を `[]` 記法に統一
        "no_unused_imports": true,                 // 未使用の use 文を削除
        "ordered_imports": {"sort_algorithm": "alpha"}, // use 文をアルファベット順に並び替え
        "binary_operator_spaces": {"default": "single_space"}, // `=` や `=>` の前後にスペース
        "concat_space": {"spacing": "one"},        // 文字列連結（`.`）の前後にスペースを入れる
        "single_quote": true,                      // 文字列の `'`（シングルクォート）を統一
        "phpdoc_order": true,                      // PHPDoc のタグ順を整理
        "phpdoc_no_empty_return": false            // `@return void` のような PHPDoc を削除しない
    },
    "exclude": [    // フォーマット対象から除外したい一部のディレクトリ
        "storage",
        "vendor",
        "bootstrap"
    ]
}
```

## preset  の種類
`preset` は、以下のどれかを選択可能です。

| Preset | 説明 |
|--------|------|
| **laravel** | Laravel の標準ルール（デフォルト） |
| **psr12** | PSR-12 に準拠 |
| **symfony** | Symfony コーディング規約 |
| **per** | PHP-CS-Fixer の `per` プリセット |

通常、laravel をそのまま使うのが最適 ですが、 PSR-12 のみを強制したい場合は psr12 を指定 すると良いらしい



以上