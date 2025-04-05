# このファイルについて
LaravelにXdebugを導入する手順・設置について記述する


# 手順
導入手順について記述する

##  Xdebug インストール
Docker 内の app コンテナに入って ライブラリをインストールする  
`composer require --dev nunomaduro/larastan`  

## 設定ファイル（Dockerfile）の修正
`docker/php/Dockerfile` に、以下のように追記する

### Dockerfileの記述内容
```
# Xdebugのインストール
RUN pecl install xdebug

# Xdebugの設定をコンテナ内にコピー
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
```

## 設定ファイル（xdebug.ini）の修正
Xdebugの動作モードや、WSL2で正しく動作するために、  
`docker/php/xdebug.ini` に、以下のように記述する

### xdebug.iniの記述内容
```
[xdebug]
zend_extension=xdebug.so
xdebug.mode=debug
xdebug.start_with_request=yes

# WSL2ではホストに接続するために必要
xdebug.client_host=host.docker.internal
xdebug.client_port=9004
xdebug.log=/var/log/xdebug.log
xdebug.log_level=7
```

## 設定ファイル（docker-compose.yml）の修正
extra_hosts を追加（WSL2環境で host.docker.internal を使えるように、  
`docker/docker-compose.yml` に、以下のように追記する

### docker-compose.ymlの記述内容
Laravelのコンテナのエリアに記述する
```
extra_hosts:
  - "host.docker.internal:host-gateway"  # WSL2でホストマシンに接続するための設定
```

## 設定ファイル（launch.json）の修正
`.vscode/launch.json` を作成する（VSCodeでXdebugを実行するとファイルを作成するか問われる文言が表示されるので作成する）  
以下のように追記する

### launch.jsonの記述内容
- pathMappings を設定（コンテナ内 /var/www をローカルの app にマッピング）   ←後の工程でブレークポイントが通らない場合、だいたいここのパスが正しくない（AIに問い合せるなどして確認する）
- Xdebugのポートを 9004 に指定
- hostname: "0.0.0.0" を追加（WSL2では必要）

```
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
            "hostname": "0.0.0.0",  // WSL2では必要
            "port": 9004,  // Xdebug のポートを9004に設定
            "pathMappings": {
                "/var/www": "${workspaceFolder}/app"  // コンテナ内のパスをローカルのパスにマッピング
            },
            "log": true
        }
    ]
}
```

## Dockerコンテナを再ビルド
`docker-compose down`  
`docker-compose build`  
`docker-compose up -d`  

## Xdebugの動作確認
コンテナ内に入って、 php -m | grep xdebug でXdebugが有効になっているか確認する
`docker exec -it the21st_app bash`  
`php -m | grep xdebug`  

## VSCodeのデバッグを開始
1. VSCodeのデバッグパネル から 「Listen for Xdebug」 を選択
1. デバッグを開始（F5キー）
1. breakpoint を設定し、リクエストを送信（Postmanなど）
   - → breakpoint で止まれば成功！

## Postmanでリクエスト
ヘッダーに下記の情報を追加する
キー：Cookie
値：XDEBUG_SESSION=PHPSTORM
