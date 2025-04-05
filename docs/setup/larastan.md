# このファイルについて
LaravelにLarastan（PHPStan）を導入する手順・設置について記述する


# 手順
導入手順について記述する

##  Larastan（PHPStan） インストール
Docker 内の app コンテナに入って ライブラリをインストールする  
`composer require --dev nunomaduro/larastan`  

## 設定ファイル（phpstan.neon）を作成
プロジェクトのルートディレクトリ（app/）に phpstan.neon というファイルを作成し、以下のように記述する

### phpstan.neonの記述内容
```
includes:
    - vendor/nunomaduro/larastan/extension.neon

parameters:
    level: 7
    paths:
        - app
        - database
    ignoreErrors: []
    tmpDir: storage/phpstan
```

## PHPStanのキャッシュフォルダを作成
`mkdir -p storage/phpstan`

### コードを解析する
以下のコマンドを実行すると、コードの静的解析が行われる。  
`vendor/bin/phpstan analyse`  
`vendor/bin/phpstan analyse --memory-limit=1G`      ←メモリー不足のエラーが表示された場合は、メモリー制限を増やして実行する  

導入時にエラーが発生した場合、
levelをエラーが出ないレベルまで下げる。エラーが発生する一番最下位のレベルからエラーを解消する。

完了。

# その他
設定の備考

## phpstan.neonの設定値について説明
- includes（外部設定の読み込み）：
  - Larastan（Laravel 用の PHPStan 拡張）を読み込む設定。
  - これにより Eloquent のリレーションや App\Models\User::find() など、Laravel 特有の処理を考慮した解析が可能になる。
- parameters（PHPStan の動作設定）
  - level: （解析レベル）
    - 静的解析の厳しさを指定（0〜9 の範囲）
    - 7 は 型安全を重視するレベル であり、Laravel のダイナミックなメソッド呼び出しで警告が増える可能性あり
    - Laravel の標準的なプロジェクトなら 5 〜 7 が推奨
  - paths: [...]（解析対象のディレクトリ）
    - 静的解析を実行するディレクトリを指定
    - app/ → Laravel のコアロジック（モデル、コントローラー、サービスなど）
    - database/ → マイグレーション、シーダーなどの解析も可能（不要なら削除してもOK）
  - ignoreErrors: []（無視するエラー）
    - ignoreErrors は 特定のエラーを無視する設定
    - level: 7 以上では Laravel の動的プロパティやメソッド に関するエラーが増えるので、必要に応じて追加
      - 例：Laravel は Eloquent::someMethod() のようにリレーションメソッドを自動生成するが、PHPStan では「未定義メソッド」と誤判定されることがある。
  - tmpDir: storage/phpstan（一時ファイルの保存先）
    - PHPStan のキャッシュファイルを保存するディレクトリ
    - デフォルトでは tmp/ を使用するが、Laravel の storage/ を指定すると整理しやすい
    - このディレクトリが存在しないとエラーが出るので、事前に作成が必要


## Larastan レベルごとのチェック内容

| レベル | チェック内容 | 特徴 |
|--------|-------------|------|
| **0**  | ほぼ何もチェックしない | 最も緩い |
| **1**  | 基本的な型チェック（関数の引数や戻り値） | 明らかなバグを検出 |
| **2**  | クラス・メソッドの存在チェック | Laravel の `__call` によるメソッド呼び出しで誤検出が増える可能性あり |
| **3**  | 配列キーの存在チェック | Laravel の `Arr::get()` や `Collection` の操作が警告になる場合あり |
| **4**  | より厳密な型チェック（null 許容、Union 型） | optional chaining (`?->`) の考慮が必要 |
| **5**  | `@param` や `@return` のアノテーションを厳密にチェック | Laravel のモデルに PHPDoc コメントを追加する必要が出る |
| **6**  | プロパティの型定義の厳密化 | `@var` コメントが適切でないと警告が増える |
| **7**  | 未定義プロパティの警告を厳しくチェック | `$this->someProperty` の使用に注意 |
| **8**  | クラスのジェネリクスを厳しくチェック | Laravel の `Collection` で多くの警告が出る |
| **9**  | 最も厳密な型チェック | Laravel の Eloquent のメソッドチェーンが厳しく評価される |

### 設定するレベルの考え方

| レベル | チェック内容 | 特徴 |
|--------|-------------|------|
| **0** | ほぼ何もチェックしない | 最も緩い |
| **3** | 型チェック・基本的な静的解析 | 初心者向け |
| **5** | Laravel の実運用に適した標準レベル | 推奨 |
| **7** | 未定義プロパティの警告が厳しくなる | 型安全を向上 |
| **9** | 最も厳しい解析 | Laravel では誤検出が増えやすい |