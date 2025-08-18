# このファイルについて
LaravelにSwagger(darkaonline/l5-swagger)を導入する手順・設置について記述する


# 手順
導入手順について記述する

##  darkaonline/l5-swagger をインストール
Docker 内の app コンテナに入って ライブラリをインストールする  
`composer require "darkaonline/l5-swagger"`  

## 公開ファイルを vendor:publish
`php artisan vendor:publish --provider="L5Swagger\L5SwaggerServiceProvider"`  
Laravel の vendor:publish 機能を使って、L5SwaggerServiceProvider が提供するファイルを、アプリケーションの中にコピー（公開）する。

## 対象のファイルに
Swaggerを作成したクラスとメソッドにSwaggerアノテーションを記述する

### 例：クラス
```
/**
 * @OA\Info(
 *     title="Playground API",
 *     version="1.0.0",
 *     description="自由な実験的なコードを書くためのコントローラ"
 * )
 */
class PlaygroundController extends Controller
{
    省略
}
```

### 例：メソッド
```
/**
* @OA\Get(
*     path="/api/playground/sample",
*     summary="サンプルAPI",
*     description="これはお試し用のサンプルAPIです。",
*     tags={"Playground"},
*     @OA\Response(
*         response=200,
*         description="成功時のレスポンス",
*         @OA\JsonContent(
*             @OA\Property(property="message", type="string", example="This is a sample API response.")
*         )
*     )
* )
*/
public function sample(): JsonResponse
{
    return response()->json(['message' => 'This is a sample API response.']);
}
```

## Swaggerを生成
`php artisan l5-swagger:generate`  
`storage/api-docs/api-docs.json` が生成される

## Swagger にアクセス確認
http://localhost:8089/api/documentation  
ドキュメントが表示される

# darkaonline/l5-swaggerの説明
Laravel 用に作られた以下のライブラリをラッパーしたライブラリで、次の2つのSwagger関連ツールを組み合わせている
1. zircote/swagger-php
PHPのコメントアノテーションから OpenAPI仕様 (Swagger) の JSON/YAML ファイル を生成します。
Laravelに特化していない「汎用のPHP用ライブラリ」です。

1. swagger-api/swagger-ui
JSONやYAMLで記述された OpenAPI Specification をもとに、ブラウザで見やすい APIドキュメントのUI を提供するツールです。

| 組み込まれているツール | 役割                                                                 | 備考                                                                 |
|------------------------|----------------------------------------------------------------------|----------------------------------------------------------------------|
| swagger-php            | PHPのコメント（アノテーション）から OpenAPI仕様（JSON/YAML） を自動生成 | バックエンドで `php artisan l5-swagger:generate` を実行すると使用される |
| Swagger UI             | 上記で生成した JSON/YAML をもとに、ブラウザで見やすいAPIドキュメントページを表示 | デフォルトでは `/api/documentation` にアクセスすると表示される        |
