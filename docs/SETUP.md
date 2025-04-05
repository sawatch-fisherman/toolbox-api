# 作成日
25/02/22

# 概要
docker + laravelの環境を作成する  
dockerとlaravelのフォルダは分離する  
親フォルダ  
┗app(laravel)  
┗docker  

# 手順

## フォルダ作成
親フォルダ(例：The21stCentury)  
┗app(laravel)  
┗docker  

##  Docker 環境を作成
### Dockerfileなどを作成
ファイルを作成する
- docker/php/Dockerfile
- docker/nginx/default.conf
- docker/docker-compose.yml
- docker/.env

### 名前を編集
- docker/docker-compose.yml
    - container_nameを設定
- docker/.env
    - COMPOSE_PROJECT_NAMEを設定
- docker/nginx/default.conf
    docker/docker-compose.ymlのappのcontainer_nameに変更する
    - 修正前：fastcgi_pass php:9000;
    - 修正後：fastcgi_pass the21st_app:9000;

### Docker をビルド・起動
cd docker  
`docker-compose up -d --build`  

##  Laravel インストール
Docker 内の app コンテナに入って Laravel 11 をインストール

### コンテナに入る
`docker exec -it コンテナ名 bash`  
┗例:docker exec -it the21st_app bash

### Laravel 11 をインストール
composer create-project laravel/laravel . "11.*"

### PHPとLaravelのバージョン確認
`php artisan --version`

Laravel Framework 11.43.2

`php -v`

PHP 8.2.27 (cli) (built: Feb  4 2025 04:26:00) (NTS)  
Copyright (c) The PHP Group  
Zend Engine v4.2.27, Copyright (c) Zend Technologies

### .envのコピーとAPP_KEYの生成

```bash
cp .env.example .env
php artisan key:generate
```

### .env の設定を変更
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

### マイグレーション実行
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
http://localhost:8088/  
ポート番号はdocker/docker-compose.ymlのwebのportsに合わせる  
```
ports:
  - "8088:80"
```

### ログファイルのパーミッションを変更
WSL特有だが、ログファイルのアクセス権限がないとエラーが発生する  
例：file_put_contents(/var/www/storage/framework/views/187f828346b000af3c7029fb05171792.php): Failed to open stream: Permission denied

appコンテナ内で、ログファイルの権限を変更する
`docker exec -it the21st_app bash`  
`chmod -R 777 storage`  

再度、http://localhost:8088/  にアクセスするとLaravelのトップ画面が表示される

### Laravel 権限設定（重要）
必要なディレクトリを作成
`mkdir -p storage/framework/{cache,sessions,views}`  
`mkdir -p bootstrap/cache`  

Laravel が書き込めるようにパーミッションを設定
`chmod -R 775 storage bootstrap/cache`  
`chown -R www-data:www-data storage bootstrap/cache`    # 環境に応じて変更（例：`www-data` など）

WSL 環境でさらに権限が必要な場合
`chmod -R 777 storage`  

### キャッシュのクリア（必要に応じて）
`php artisan config:clear`  
`php artisan route:clear`  
`php artisan view:clear`  
`php artisan cache:clear`  


## .gitignoreの修正
app(Laravel)フォルダ配下の無視するように書き換える

```
app/vendor/
app/node_modules/
app/npm-debug.log
app/yarn-error.log

# Laravel 4 specific
app/bootstrap/compiled.php
app/app/storage/

# Laravel 5 & Lumen specific
app/public/storage
app/app/public/hot

# Laravel 5 & Lumen specific with changed public path
app/public_html/storage
app/public_html/hot

app/storage/*.key
app/.env
app/Homestead.yaml
app/Homestead.json
app//.vagrant
app/.phpunit.result.cache

app//public/build
app//storage/pail
app/.env.backup
app/.env.production
app/.phpactor.json
app/auth.json
```