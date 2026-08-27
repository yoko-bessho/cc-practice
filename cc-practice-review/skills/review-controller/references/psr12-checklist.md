# PSR-12 準拠チェックリスト（Laravel Controller 向け）

Laravel の Controller クラスをレビューする際に確認する PSR-12 の主要ルールをまとめる。Laravel の慣例（Sail 経由での実行、`./vendor/bin/sail pint` によるフォーマット等）と併せて確認する。

## ファイル全体

- ファイルは `<?php` タグで始まり、閉じタグ `?>` を含まない
- ファイルの文字コードは UTF-8（BOM なし）
- インデントはスペース4つ。タブは使用しない
- 行末に不要な空白がない
- ファイル末尾に余分な空行が複数連続していない

## namespace / use 文

- `namespace` 宣言の後に空行を1つ入れる
- `use` 文は `namespace` の直後にまとめて記述する
- `use` 文の間に他のコードを挟まない
- 未使用の `use` 文が残っていないか確認する（`use App\Models\Xxx;` を宣言しているのに `Xxx` を参照していない等）
- グループ化されていない `use` 文が並んでいる場合、標準ライブラリ → Laravel フレームワーク → アプリケーション固有 の順で並んでいると読みやすい（PSR-12 自体は順序を強制しないため、これは可読性の観点での指摘に留める）

## クラス宣言

```php
class PostController extends Controller
{
    // ...
}
```

- クラス名は `StudlyCaps`（例: `PostController`）
- クラス宣言の開き波括弧 `{` は次の行に置く
- `extends` / `implements` は同じ行、または長すぎる場合は複数行に整形されているか

## メソッド宣言

```php
public function store(StorePostRequest $request): RedirectResponse
{
    // ...
}
```

- メソッド名は `camelCase`（例: `store`, `showEdit`）
- 可視性（`public`/`protected`/`private`）を必ず明示する。省略（デフォルト public 扱い）は PSR-12 違反として指摘する
- メソッド宣言の開き波括弧 `{` は次の行に置く
- 引数リストが長い場合、1引数1行で整形されているか
- 戻り値の型宣言（`: RedirectResponse` 等）がある場合、コロンの前にスペースを入れない

## 制御構造

```php
if ($request->has('foo')) {
    // ...
} elseif ($request->has('bar')) {
    // ...
} else {
    // ...
}
```

- `if`/`foreach`/`while`/`switch` 等のキーワードの後に半角スペースを1つ入れる
- 開き波括弧 `{` は同じ行に置く（クラス・メソッド宣言とは異なるので注意）
- `elseif` は `else if` ではなく1語で書く

## その他

- 1行の長さは目安として120文字以内（ソフトリミット。PSR-12 では厳密な上限は定めていないが、可読性の観点で長すぎる行は指摘する）
- 型宣言（プロパティ型、引数型、戻り値型）を可能な限り明示しているか（PHP 8.4 の機能を活用できているか）
- `declare(strict_types=1);` を使用する方針がプロジェクトにある場合、宣言されているか確認する（`CLAUDE.md` に明記がなければ必須指摘にはしない）

## 自動修正について

このプロジェクトでは `./vendor/bin/sail pint` で PSR-12 準拠のフォーマットを自動チェック・修正できる。目視でのレビューに加えて、コマンド実行を提案してよい。
