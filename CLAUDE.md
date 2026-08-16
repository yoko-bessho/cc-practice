# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## プロジェクト概要

Claude Code の学習用に作られた Laravel プロジェクト。現時点では `laravel/laravel` のスケルトンからほぼ変更されておらず、独自のルート・コントローラーはまだ実装されていない。

## 技術スタック

- **フロントエンド**: React / TypeScript / Vite
- **スタイリング**: Tailwind CSS
- **バックエンド**: laravel 10、PHP 8.4.1
- **データベース**: mysql 8.4
- **認証**: JWT
- **テスト**: Vitest（ユニット）/ Playwright（E2E）
- **Lint/Format**: ESLint / Prettier

上記は導入予定の構成。現状の実装との差分:

- フロントエンドは Blade + 素の JS（`resources/js/app.js`）のみ。React / TypeScript / Tailwind / ESLint / Prettier は未導入（設定ファイルなし）
- 認証は `laravel/sanctum` のトークン認証のみ実装済み。JWT 関連パッケージは未導入
- テストは PHPUnit（`tests/Feature`, `tests/Unit`）のみ。Vitest / Playwright は未導入（`package.json` に依存関係なし）

## 開発ルール

- コミットメッセージは日本語で書く
- コードのコメントは、そのコードを選択した理由を日本語で書く（何をしているかではなく、なぜそうしたかを書く）
- テストは PHPUnit で書く（Pest は使わない）
- 開発環境のコマンドは Sail 経由で実行する（例: `./vendor/bin/sail artisan test`）
- コーディング規約は PSR-12 に準拠する

## セットアップ / よく使うコマンド

すべて Sail（`./vendor/bin/sail`）経由で実行する。初回セットアップのみホスト側の `composer` が必要。

### PHP / Laravel
- 初回のみ依存関係インストール: `composer install`
- `.env` 作成: `cp .env.example .env`
- コンテナ起動: `./vendor/bin/sail up`（`compose.yaml` に MySQL 8.4 コンテナ定義あり。`-d` でバックグラウンド起動）
- APP_KEY 生成: `./vendor/bin/sail artisan key:generate`
- マイグレーション実行: `./vendor/bin/sail artisan migrate`

### フロントエンド（Vite）
- 依存関係インストール: `./vendor/bin/sail npm install`
- 開発ビルド（HMR）: `./vendor/bin/sail npm run dev`
- 本番ビルド: `./vendor/bin/sail npm run build`

### テスト（PHPUnit）
- 全テスト実行: `./vendor/bin/sail test` または `./vendor/bin/sail artisan test`
- 単一テスト実行: `./vendor/bin/sail test --filter=テスト名`
- スイート指定: `./vendor/bin/sail artisan test --testsuite=Unit`（または `Feature`）
- `phpunit.xml` でテスト実行時は `APP_ENV=testing`, `DB_DATABASE=testing`, `QUEUE_CONNECTION=sync` などが強制される

### Lint / Format
- PHP コードフォーマット（PSR-12 準拠チェック）: `./vendor/bin/sail pint`

## アーキテクチャ

標準的な Laravel 10 系の MVC 構成で、カスタマイズはほぼ入っていない。

- `routes/web.php` — `/` に `welcome` ビューを返すのみ
- `routes/api.php` — `auth:sanctum` で保護された `/user` エンドポイントのみ（Sanctum トークン認証のサンプル実装）
- `app/Models/User.php` — `HasApiTokens`（Sanctum）, `HasFactory`, `Notifiable` を使用したデフォルトの User モデル。他のモデル・コントローラーは未作成
- `database/migrations/` — デフォルトのテーブルのみ（users, password_reset_tokens, failed_jobs, personal_access_tokens）
- Git リポジトリは初期化済み

新機能を追加する際は、標準 Laravel のディレクトリ構成（`app/Models`, `app/Http/Controllers`, `routes/`, `database/migrations` 等）に従うこと。
