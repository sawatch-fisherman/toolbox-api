# 作成日
25/04/12

# 概要
リポジトリをcloneした際に、この手順書を実施する

# 手順

## Docker をビルド・起動
`cd docker  `  
`docker-compose up -d --build`  

## コンテナに入る
`docker exec -it コンテナ名 bash`  
┗例:`docker exec -it toolbox_app bash`  

## Laravel の依存パッケージをインストール（composer）
`composer install`  

## .envのコピーとAPP_KEYの生成
`cp .env.example .env`  
`php artisan key:generate`  

## .env の設定を変更
WSL環境だと権限の関係で保存できないので、パーミッションを変更
`chmod -R 777 .env`  

```
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=user
DB_PASSWORD=password
```

# storage フォルダの準備
`mkdir -p storage/framework/{cache,sessions,views}`  
`mkdir -p storage/logs`  
`chmod -R 775 storage bootstrap/cache`  

## Laravel が書き込めるようにパーミッションを設定
`chmod -R 775 storage bootstrap/cache`  
`chown -R www-data:www-data storage bootstrap/cache`    # 環境に応じて変更（例：`www-data` など）

WSL 環境でさらに権限が必要な場合
`chmod -R 777 storage`  

## マイグレーション実行
`php artisan migrate:status`

下記が表示されればOK（SQLへの接続確認できれば良い）
```
 ERROR  Migration table not found.  
```

`php artisan migrate`

`exit`

## dockerの再確認
dockerフォルダへ移動  
`cd docker`  
docker ps でコンテナが起動していることを確認  

動いていない場合、再ビルド
`docker-compose down`
`docker-compose up -d --build`

## サイトへアクセス
http://localhost:8089/  
ポート番号はdocker/docker-compose.ymlのwebのportsに合わせる  
```
ports:
  - "8089:80"
```

## ログファイルのパーミッションを変更
WSL特有だが、ログファイルのアクセス権限がないとエラーが発生する  
例：file_put_contents(/var/www/storage/framework/views/187f828346b000af3c7029fb05171792.php): Failed to open stream: Permission denied

appコンテナ内で、ログファイルの権限を変更する
`docker exec -it toolbox_app bash`  
`chmod -R 777 storage`  

再度、http://localhost:8089/  にアクセスするとLaravelのトップ画面が表示される

必要なディレクトリを作成
`mkdir -p storage/framework/{cache,sessions,views}`  
`mkdir -p bootstrap/cache`  

Laravel が書き込めるようにパーミッションを設定
`chmod -R 775 storage bootstrap/cache`  
`chown -R www-data:www-data storage bootstrap/cache`    # 環境に応じて変更（例：`www-data` など）

WSL 環境でさらに権限が必要な場合
`chmod -R 777 storage`  

## キャッシュのクリア（必要に応じて）
`php artisan config:clear`  
`php artisan route:clear`  
`php artisan view:clear`  
`php artisan cache:clear`  

