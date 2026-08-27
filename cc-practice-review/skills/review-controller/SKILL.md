---
name: review-controller
description: This skill should be used when the user asks to "コントローラーをレビューして", "Controller のコードレビューをして", "review this controller", "PSR-12 に準拠しているか確認して", "バリデーションが実装されているか確認して", "N+1 問題がないか調べて", or requests a code review of a Laravel Controller class (files under app/Http/Controllers). Reviews Laravel Controllers from three perspectives: PSR-12 compliance, presence of request validation, and N+1 query problems.
version: 0.1.0
---

# Laravel Controller レビュー

Laravel の Controller クラス（`app/Http/Controllers/` 配下）を、PSR-12 準拠・バリデーションの有無・N+1 問題の3つの観点でレビューする。

## レビューの進め方

1. 対象の Controller ファイルを読む。ファイルパスが指定されていない場合は `app/Http/Controllers/` 配下から対象を確認し、ユーザーに候補を提示する。
2. 関連するファイルを合わせて読む。具体的には以下を確認する。
   - 対応する Model（`app/Models/`）— リレーション定義を確認するため
   - 対応する Form Request（`app/Http/Requests/`）— バリデーションの実装場所を確認するため
   - ルート定義（`routes/web.php`, `routes/api.php`）— アクションの呼び出され方（一覧表示か詳細表示か等）を確認するため
   - 対応するビュー（`resources/views/`）または API リソース（`app/Http/Resources/`）— アクションが返すデータで実際に参照しているリレーションを確認するため（N+1 判定に必須）。ビューパスは `return view('posts.index', ...)` の第一引数から、リソースクラスは `return PostResource::collection(...)` 等の戻り値から特定する
3. 下記「レビュー観点」の3軸それぞれについて、Controller の各メソッド（アクション）を1つずつ確認する。
4. 「出力フォーマット」に従って指摘事項をまとめる。指摘が1件もない観点についても「問題なし」として明示する。

## レビュー観点

### 1. PSR-12 準拠

コーディングスタイルが PSR-12 に準拠しているかを確認する。主なチェック項目は以下の通り。

- インデントは 4 スペース（タブ不可）
- クラス名は `StudlyCaps`、メソッド名は `camelCase`
- 波括弧の位置（クラス宣言・メソッド宣言は改行後、`if`/`foreach` 等の制御構造は同じ行）
- `use` 文の並び順とグルーピング、未使用 `use` の有無
- メソッドの可視性（`public`/`protected`/`private`）が明示されているか
- 1行の長さが目安（120文字）を超えていないか
- ファイル末尾に余分な空行や閉じタグ `?>` がないか

詳細なチェックリストは `references/psr12-checklist.md` を参照する。指摘の際は該当箇所を `ファイルパス:行番号` の形式で示す。

### 2. バリデーションの有無

Controller のアクション内で、ユーザー入力（`$request` 経由の値）を DB 操作やビジネスロジックに使う前にバリデーションしているかを確認する。

- `store`/`update` 等、入力を受け取るアクションで Form Request（`$request->validate()` ではなく専用の `FooRequest` クラス）を使っているか
- インラインの `$request->validate([...])` を使っている場合、ルールが不足していないか（`required`, 型指定, `exists`/`unique` 等の関連整合性チェック）
- バリデーションを経由せずに `$request->input()` / `$request->all()` の値を直接 `create()`/`update()` に渡していないか（Mass Assignment のリスク）
- 認可チェック（`authorize()` や `Gate`/`Policy`）がバリデーションと合わせて必要な箇所に存在するか

詳細な確認項目は `references/validation-checklist.md` を参照する。

### 3. N+1 問題

一覧表示・詳細表示のアクションで、リレーションを含むクエリが N+1 を引き起こしていないかを確認する。

- ループ（`@foreach`、`->map()` 等）の中でリレーションプロパティ（例: `$post->user->name`）に初めてアクセスしている箇所がないか
- 一覧取得のクエリ（`Model::all()`, `Model::paginate()`, `Model::get()` 等）で、後続処理・Blade テンプレートで使うリレーションに対して `with()` による eager loading が指定されているか
- API リソース（`JsonResource`）やビューが参照するリレーションと、Controller 側で eager loading しているリレーションが一致しているか
- 既に eager loading していても、条件付きの追加クエリ（`loadCount()` が必要な集計など）が漏れていないか

具体的な検出パターンと修正例は `references/n-plus-one-patterns.md` を参照する。

## 出力フォーマット

指摘事項は観点ごとに整理し、以下の形式でまとめる。

```
## PSR-12 準拠
- [指摘 or 問題なし] ファイルパス:行番号 — 内容と修正案

## バリデーション
- [指摘 or 問題なし] ファイルパス:行番号 — 内容と修正案

## N+1 問題
- [指摘 or 問題なし] ファイルパス:行番号 — 内容と修正案

## まとめ
- 重大度の高い指摘の要約（あれば）
```

出力例は `examples/review-output-example.md` を参照する。

## レビュー時の注意点

- 指摘は「何が問題か」だけでなく「なぜ問題か」「どう直すか」を具体的に示す。特に N+1 問題は、修正しなくても動作上は正しく見えるため、パフォーマンスへの影響を明示する。
- プロジェクトの `CLAUDE.md` に開発ルールがある場合はそれを優先する（例: PHPUnit を使う、コメントは日本語で書く 等）。このプラグインの指摘内容がプロジェクト固有のルールと矛盾する場合は、プロジェクト側のルールを優先する。
- 該当観点に問題がない場合でも、レビュー結果には必ずその観点を含め「問題なし」と明記する。指摘の省略はレビュー漏れと区別できないため避ける。

## 参考資料

### リファレンスファイル

- `references/psr12-checklist.md` — PSR-12 準拠チェックリスト（Laravel Controller 向け）
- `references/validation-checklist.md` — バリデーション確認チェックリスト
- `references/n-plus-one-patterns.md` — N+1 問題の検出パターンと修正例

### サンプル

- `examples/review-output-example.md` — レビュー結果の出力例
