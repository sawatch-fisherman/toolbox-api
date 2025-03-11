# このファイルについて
docker, Laravelコマンドのチートシート

# dockerコマンド
| 項目 | コマンド |
|:---:|:---:|
| コンテナのディレクトリへ移動 | `cd docker` |
| コンテナ起動 | `docker compose up -d` |
| コンテナ停止 | `docker compose stop` |
| コンテナ(Laravel)に入る | `docker exec -it the21st_app bash` |

# Laravel
| 項目 | コマンド |
|:---:|:---:|
| ルーティングを確認 | `php artisan route:list` |


# Laravel(拡張ライブラリ)
| 項目 | コマンド |
|:---:|:---:|
| コード解析 | `vendor/bin/phpstan analyse --memory-limit=1G` |
