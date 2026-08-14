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

## セットアップ / よく使うコマンド

### PHP / Laravel
- 依存関係インストール: `composer install`
- `.env` 作成: `cp .env.example .env` → `php artisan key:generate`
- マイグレーション実行: `php artisan migrate`
- 開発サーバー起動: `php artisan serve`
- Docker (Sail) で起動: `./vendor/bin/sail up`（`compose.yaml` に MySQL 8.4 コンテナ定義あり）

### フロントエンド（Vite）
- 依存関係インストール: `npm install`
- 開発ビルド（HMR）: `npm run dev`
- 本番ビルド: `npm run build`

### テスト（PHPUnit）
- 全テスト実行: `php artisan test` または `vendor/bin/phpunit`
- 単一テスト実行: `php artisan test --filter=テスト名` / `vendor/bin/phpunit --filter=テスト名`
- スイート指定: `php artisan test --testsuite=Unit`（または `Feature`）
- `phpunit.xml` でテスト実行時は `APP_ENV=testing`, `DB_DATABASE=testing`, `QUEUE_CONNECTION=sync` などが強制される

### Lint / Format
- PHP コードフォーマット: `./vendor/bin/pint`

## アーキテクチャ

標準的な Laravel 10 系の MVC 構成で、カスタマイズはほぼ入っていない。

- `routes/web.php` — `/` に `welcome` ビューを返すのみ
- `routes/api.php` — `auth:sanctum` で保護された `/user` エンドポイントのみ（Sanctum トークン認証のサンプル実装）
- `app/Models/User.php` — `HasApiTokens`（Sanctum）, `HasFactory`, `Notifiable` を使用したデフォルトの User モデル。他のモデル・コントローラーは未作成
- `database/migrations/` — デフォルトのテーブルのみ（users, password_reset_tokens, failed_jobs, personal_access_tokens）
- Git リポジトリは未初期化（`.git` なし）

新機能を追加する際は、標準 Laravel のディレクトリ構成（`app/Models`, `app/Http/Controllers`, `routes/`, `database/migrations` 等）に従うこと。
