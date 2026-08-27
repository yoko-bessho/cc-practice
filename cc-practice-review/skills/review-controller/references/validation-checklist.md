# バリデーション確認チェックリスト

Laravel Controller のアクションが、ユーザー入力を安全に扱えているかを確認するためのチェックリスト。

## 確認する対象のアクション

主に以下のような、外部からの入力を受け取るアクションを重点的に確認する。

- `store`（新規作成）
- `update`（更新）
- 検索・フィルタ条件をクエリパラメータから受け取る `index`

## Form Request を使っているか

```php
// 望ましい例
public function store(StorePostRequest $request)
{
    Post::create($request->validated());
}
```

```php
// 指摘対象の例（バリデーションが存在しない）
public function store(Request $request)
{
    Post::create($request->all());
}
```

- 専用の Form Request クラス（`app/Http/Requests/`）が定義され、`rules()` メソッドでルールが宣言されているか
- Form Request を使わずインラインで `$request->validate([...])` を呼んでいる場合、ルールの網羅性を個別に確認する（下記「ルールの妥当性」参照）
- **バリデーションが一切存在しない**（`$request->all()` や `$request->input()` の値をそのまま `create()`/`update()`/`save()` に渡している）場合は、重大な指摘として扱う

## ルールの妥当性

- 必須項目に `required` が付与されているか
- 型に応じたルール（`string`, `integer`, `numeric`, `date`, `boolean` 等）が指定されているか
- 文字列長の上限（`max:255` 等）がカラム定義（migration）と整合しているか
- 外部キーやリレーション先の存在チェック（`exists:table,column`）が必要な項目に付与されているか
- 一意制約が必要な項目に `unique` が付与されているか（`update` 時は対象自身のレコードを除外するルール — `Rule::unique(...)->ignore($id)` — になっているか）
- ファイルアップロードがある場合、`file`, `image`, `mimes`, `max`（サイズ）が指定されているか

## Mass Assignment 対策

- `create()`/`update()`/`fill()` に渡す配列が `$request->validated()`（Form Request 使用時）または明示的にホワイトリスト化された配列になっているか
- `$request->all()` をそのまま渡していないか（バリデーション未実施かどうかに関わらず、意図しない属性の上書きを許してしまうため指摘する）
- Model 側の `$fillable`/`$guarded` の設定と実際に渡している属性が整合しているか（Model ファイルが確認できる場合のみ）

## 認可（Authorization）

バリデーションと合わせて、認可漏れがないかも確認する。

- `update`/`destroy` など、対象レコードの所有者以外が操作できてしまう余地がないか
- Form Request の `authorize()` メソッドが `true` 固定になっていないか（固定の場合、別途 Controller や Policy で認可していない限り指摘する）
- `Gate::allows()` や `$this->authorize()`、Policy が必要な操作に対して呼ばれているか

## バリデーションエラー時の挙動

- バリデーション失敗時のレスポンスが用途に合っているか（Web: リダイレクト + エラーメッセージ、API: 422 の JSON レスポンス）
- API Controller の場合、`FormRequest` のバリデーション失敗時に自動で 422 が返る Laravel の標準挙動を上書きしていないか（上書きしている場合、意図した挙動か確認する）
