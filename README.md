# coachtechフリマ

## 環境構築
1. リポジトリをクローン 
    ```bash
    `git clone git@github.com:kawamata-natsuki/coachtech-flea.git`
    ```
2. `.env`ファイルの準備
    ```bash
    `cp .env.docker.example .env`
    ```
    ※`.env`ファイルはDocker用の設定ファイルです。
      自分の環境に合わせて`.env`の`UID/GID`を設定してください。
    ※Linux/macOSの場合、以下のコマンドで確認できます：
      ```bash
      id -u  # UID
      id -g  # GID
      ```

3. `docker-compose.override.yml`の作成

    ```bash
    `touch docker-compose.override.yml`
    ```
    ※`docker-compose.override.yml`はGit管理対象外なので、各自の環境に合わせて作成してください。
    ```yaml
    services:
      nginx:
        ports:
          - "8084:80"

      php:
        user: "${UID}:${GID}"

      phpmyadmin:
        ports:
          - 8085:80
    ```

4. Dockerイメージのビルドと起動
    ```bash
    docker-compose up -d --build
    ```
    > MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
    エラーが発生する場合は、`docker-compose.yml`ファイルの`mysql`に以下のように追記してください
    ```yaml
    mysql:
        platform: linux/x86_64(この行を追加)
        image: mysql:8.0.26
        environment:
    ```
5. Laravelのセットアップ

    PHPコンテナに入ります
    ```bash
    docker-compose exec php bash
    ```

    Composerのインストール
    ```bash
    composer install
    ```

    アプリケーションキーの生成
    ```bash
    php artisan key:generate
    ```

6. `.env`ファイルの設定

    `.env`ファイルに以下の内容を追記・修正してください：
    #### メール設定
    ```ini
    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=86721f057eae5a
    MAIL_PASSWORD=1c38873d2cf2fb
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=no-reply@example.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```
    #### Stripe設定
    ```ini
    STRIPE_KEY=pk_test_51RFxk9FYmDOFjDEsqcXkKVtXfBeBu5dDp4nb4EgRibPVHW1Jg7GWQU2DxxlhXYvRoZsFCXWAr4rbmrZLcJmoHAIt00tD7xrg9O
    STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
    ``` 
    
    #### Stripe テストカード番号
    - 成功：4242 4242 4242 4242  
    - 失敗：4000 0000 0000 9995  
    - 有効期限：任意の未来日（例：04/34）  
    - CVC：適当な3桁（例：123）
    
7.  権限設定

    `storage`および`bootstrap/cache`に適切な権限を設定します。
    `src`ディレクトリに移動し、以下のコマンドを実行してください。
    ```bash
    sudo chmod -R 775 storage
    sudo chmod -R 775 bootstrap/cache
    ```

8. `.gitignore`の修正

    `.gitignore`をプロジェクトのルートディレクトリ（最上位のディレクトリ）に移動させます
    ```bash
    `mv .gitignore ../`
    ```
    `.gitignore`に以下の項目を追加してください。
    ```
    /src/node_modules
    /src/public/hot
    /src/public/storage
    /src/storage/*.key
    /src/vendor
    /src/.env
    /src/.env.backup
    /src/.phpunit.result.cache
    /src/Homestead.json
    /src/Homestead.yaml
    /src/npm-debug.log
    /src/yarn-error.log
    /src/.idea
    /src/.vscode
    /docker/mysql/data/
    docker-compose.override.yml
    .env
    ```

9. マイグレーションの実行
    ```bash
    php artisan migrate
    ```

10. シーディングの実行
    ```bash
    php artisan db:seed
    ```

11. ストレージのシンボリックリンク作成

    `public/storage` を `storage/app/public` にリンクするためのコマンドです
    画像ファイルを`storage/app/public/items/abc.jpg`に保存しておくと、 
    ブラウザから`http://localhost/storage/items/abc.jpg`のようにアクセス可能になります
    ```bash
    php artisan storage:link
    ```

## テスト実行方法まとめ

### Featureテスト（PHPUnit）

主にバリデーションやコントローラーのロジックを検証します。

1. `.env.testing` を作成
   ```bash
   cp .env .env.testing
   ```

2. `.env.testing` に以下を含めてください
    ```
    APP_ENV=testing
    APP_URL=http://nginx
    SESSION_DOMAIN=.nginx
    ```

3. マイグレーション（テスト用DB）
    ```
    php artisan migrate --env=testing
    ```

4. テスト実行
    ```
    php artisan test
    もしくは
    ./vendor/bin/phpunit
    ```

### Laravel Dusk を使ったブラウザテスト

実際のブラウザ操作を通して、UIの挙動や画面遷移などを確認します。

1. Laravel Duskのセットアップ
    ```bash
    composer require --dev laravel/dusk
    php artisan dusk:install
    cp .env .env.dusk.local
    ```
    `.env.dusk.local` は Gitにコミットしないように .gitignore に追加してください
    ```

2. `.env.dusk.local` に以下を含めてください
    ```
    APP_ENV=testing
    APP_URL=http://nginx
    DB_CONNECTION=mysql
    ```
    
3.  dusk コンテナでマイグレーション実行（※テスト用DBに対して）
    ```
    docker compose exec dusk php artisan migrate
    ```

4.  Dusk テストの実行
    ```
    docker compose exec dusk php artisan dusk
    ```

### 画像アップロードのテストについて

    このプロジェクトでは画像アップロードのテストに `UploadedFile::fake()->image(...)` を使用しており、PHPのGDライブラリが必要になります。
    DockerfileでGDはインストール済みのためため、特別な対応は不要です。

## 使用技術(実行環境)
- Laravel Framework 8.83.29
- PHP 8.4.3
- MYSQL 8.0.26
- Nginx 1.21.1
- phpMyAdmin 8.2.27

## ER図
![ER図](er.drawio.png)

## URL
- 開発環境：http://localhost/
- データベース：http://localhost:8080

  ※ポート番号は`docker-compose.override.yml`で各自調整してください。