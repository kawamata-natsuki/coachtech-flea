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
    
    メール認証はMailtrapを使用しています。
    Mailtrapのアカウントをお持ちでない場合は、https://mailtrap.io から無料登録し、
    自身の受信箱に記載の `MAIL_USERNAME` と `MAIL_PASSWORD` を `.env` に設定してください。
    ```ini
    MAIL_MAILER=smtp
    MAIL_HOST=sandbox.smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username_here
    MAIL_PASSWORD=your_mailtrap_password_here
    MAIL_ENCRYPTION=null
    MAIL_FROM_ADDRESS=no-reply@example.com
    MAIL_FROM_NAME="${APP_NAME}"  
    ```
    #### Stripe設定

    Stripeのアカウントをお持ちでない場合は、[https://dashboard.stripe.com/register](https://dashboard.stripe.com/register) から無料登録し、
    テスト用APIキー（公開キー・秘密キー）を取得して `.env` に設定してください。
    ```ini
    STRIPE_KEY=your_stripe_public_key_here
    STRIPE_SECRET=your_stripe_secret_key_here
    ``` 
    
    ### 補足：Stripe テストカード番号（決済テスト用）
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
テストケースID11「支払方法選択機能」はJavaScriptを含むため、DuskによるE2Eテストは導入せず、**Featureテスト＋手動によるブラウザ確認**で対応しています。　


1. `.env.testing.example` をコピーして `.env.testing` を作成
    ※ `.env.testing.example` はテスト専用の設定テンプレートです。
   ```bash
   cp .env.testing.example .env.testing
   ```

2. マイグレーション（テスト用DB）
    ```
    php artisan migrate --env=testing
    ```

3. テスト実行
    ```
    php artisan test
    もしくは
    ./vendor/bin/phpunit
    ```

### 画像アップロードのテストについて

    このプロジェクトでは画像アップロードのテストに `UploadedFile::fake()->image(...)` を使用しており、PHPのGDライブラリが必要になります。
    DockerfileでGDはインストール済みのため、特別な対応は不要です。


## ダミーデータの作成について

### 1. 商品情報

仕様書に記載された10商品に加えて、画面レイアウトの検証用テスト商品を追加しています。
また、テーブル設計の確認やバリデーションエラー、いいね機能・コメント機能、売り切れ時の表示など、機能面のテストを目的として、以下の項目も入力しています。

| 項目 | 内容 |
|------|------|
| ブランド名 | 最大文字数を入力して、折り返し・はみ出しの検証 |
| カテゴリ | 全カテゴリを選択し、タグ表示時の余白・折り返しを検証 |
| 出品者 | `user_id` を設定 |
| 購入者 | `ordersテーブル` に購入者IDを保持 |
| 販売状況 | `item_status` を `on_sale / sold_out` で切り替え |
| 商品説明 | 長文でのレイアウト崩れを確認 |

※画像ファイル名はすべて英数字に変更済み

### 2. 商品カテゴリ情報

- `app/Constants/CategoryConstants.php` にカテゴリコード・名称を定義  
- `CategorySeeder` によりマスタを一括登録

### 3. ユーザー情報

- `UserSeeder` により、以下のユーザーが自動生成されます：

| 種別 | 内容 |
|------|------|
| 一般ユーザー | 3名（名前・住所などはダミーデータ） |
| 管理者ユーザー | 1名（※管理画面がないため、ログイン確認用のみ） |

## ログイン情報一覧

※ログイン確認用のテストアカウントです。  
※管理者ユーザーは管理画面が存在しないため、ログイン確認用アカウントとしてのみ作成しています。

| ユーザー種別     | メールアドレス         | パスワード   |
|------------------|--------------------------|--------------|
| 一般ユーザー①    | mario@example.com         | 12345678     |
| 一般ユーザー②    | link@example.com          | 12345678     |
| 一般ユーザー③    | pupupu@example.com        | 12345678     |
| 管理者ユーザー   | admin@example.com         | admin1234    |


## 使用技術(実行環境)
- Laravel Framework 8.83.29
- PHP 8.2.28
- MYSQL 8.0.26
- Nginx 1.21.1
- phpMyAdmin 8.2.27


## ER図
![ER図](er.drawio.png)

## URL
- 開発環境：http://localhost/
- データベース：http://localhost:8080
  ※ポート番号は`docker-compose.override.yml`で各自調整してください。


## 【補足】バリデーションエラーについて
全項目に `string` を指定し、不正な配列入力などを防止しています。

## 【補足】会員登録について

### FN003 FN004 バリデーションエラーについて
仕様書に加えて、以下のルールとメッセージを追加しています：

- name：
  - `required`（基本設計書にユーザー名がなかったため）
  - `max:50`（レイアウト崩れ防止のため）
  - `string`
- email：
  - `max:255`
  - `unique:users,email`（既存メールアドレスとの重複チェック）
  - `string`
- password：
  - `confirmed`（確認用パスワードとの一致チェック）
  - `string`
- password_confirmation：
  - `required`（確認用パスワードのフォーム直下にエラー表示させるため）

### プレースホルダーについて
プレースホルダーはUI補助として追加しています。
以下のとおり簡単な入力例を表示しています
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

【placeholder文言一覧】
- name                  ： 例：山田　太郎
- email                 ： 例：user@example.com
- password              ： 8文字以上のパスワードを入力
- password_confirmation ： もう一度パスワードを入力

## 【補足】ログインについて

### プレースホルダーについて
プレースホルダーはUI補助として追加しています。
以下のとおり簡単な入力例を表示しています
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

【placeholder文言一覧】
- email                 ： 例：user@example.com
- password              ： 8文字以上のパスワードを入力

## 【補足】ヘッダーについて

### ヘッダーロゴのリンク対応
仕様書には記載がありませんでしたが、ユーザーがトップページに戻りやすくなるよう、ヘッダーロゴにトップページへのリンクを設定しています。  
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

### FN016 検索機能について
Figma上のデザインには検索窓のみで検索ボタンはありませんでしたが、
リアルタイム検索の実装が難しかったため、明示的な検索ボタンを追加しています。
ユーザーがキーワード入力後に確実に検索を実行できるよう、UXを重視した対応です。  
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

1文字のみの検索だと、濁点・半濁点の違い（例：ぐ／く、ぴ／ひ）によって意図しない検索結果が出るため、部分一致検索は2文字以上で実行されるように制限しています。  

## 【補足】商品一覧画面について

### FN014 FN015 購入済商品の表示について
購入済の商品にはSOLDの表示に加えて、商品画像が暗くなるようにしています。
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

### FN014 商品名の表示について
商品名は最大40文字まで登録可能ですが、一覧表示で全て表示するとレイアウトが崩れるため、一覧画面では商品名を1行で省略表示（末尾に「…」を付けて）する仕様としています。  
詳細ページでは全文を表示します。

## 【補足】商品購入画面について

### FN022 商品購入後の画面表示について
仕様書には明記されていませんが、ユーザーの操作完了を明示するために購入完了ページを実装しています。  
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。 
購入完了メッセージとともに、以下のリンクを表示しています：
- トップページへ戻る
- 購入履歴を見る  

### FN022 商品購入後の画面表示について
仕様書には明記されていませんが、購入キャンセルページを実装しています。  
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。 
購入キャンセルのメッセージとともに、以下のリンクを表示しています：
- トップページへ戻る

### FN023 支払い方法選択機能について
仕様書では「小計画面で変更が即時反映される」と記載されていましたが、HTMLおよびLaravelのサーバー処理だけでは実現が難しかったため、JavaScriptを使用して実装しています。　
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

### FN023 Stripeの決済仕様について
Stripeではコンビニ支払いを選択した場合、30万円を超える商品の購入ができないという仕様があるため、バリデーションエラーとして処理しています。  
この制約に対応するため、ビジネスロジックをOrderController内に実装しています。

### バリデーションエラーについて
仕様書に加えて、以下のルールとメッセージを追加しています：

#### PurchaseRequest.php
- payment_method：
  - `Rule::in`（指定された支払い方法以外が送られてくるのを防ぐため）

## 【補足】プロフィール編集画面について

### FN027 プロフィール画像について
仕様書には明記されていませんが、ユーザー体験の向上を目的として、プロフィール画像を選択した際に即時プレビュー表示されるようにJavaScriptを使用して実装しています。
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

### バリデーションエラーについて
仕様書に加えて、以下のルールとメッセージを追加しています：

#### ProfileRequest.php
- profile_image：
  - `image`（拡張子だけでなく、ファイルの中身が本当に画像かどうかも検証するため）

## 【補足】商品出品画面について

### FN029 商品画像のアップロード
Figma上のデザインから、ドラッグ＆ドロップによる画像アップロードが可能な仕様と判断し、JavaScriptを使用して実装しています。　
また、仕様書には明記されていませんが、ユーザー体験の向上を目的として、選択した商品画像の即時プレビュー表示も実装しました。
※この仕様追加についてはクライアント（コーチ）に事前相談し、了承を得ています。

### バリデーションエラーについて
仕様書に加えて、以下のルールとメッセージを追加しています：

#### ExhibitionRequest.php
- name：
  - `max:40`（レイアウト崩れ防止のため）
- item_image：
  - `image`（拡張子だけでなく、ファイルの中身が本当に画像かどうかも検証するため）
- category_codes：
  - `array`（カテゴリーを複数選択できる仕様のため）
  - `distinct`
  - `Rule::in`（指定されたカテゴリコード以外が送られてくるのを防ぐため）
- condition_code：
  - `Rule::in`（指定された状態コード以外が送られてくるのを防ぐため）
- price：
  - `max:9999999`（サービス内で想定される取引価格の上限に合わせ、異常な価格やレイアウト崩れを防ぐため）
- brand：
  - `nullable`（任意入力のため）
  - `max:100`（レイアウト崩れ防止のため）


