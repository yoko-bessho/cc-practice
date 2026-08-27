# N+1 問題の検出パターンと修正例

Laravel Controller で発生しやすい N+1 問題のパターンと、その修正方法をまとめる。

## 典型的なパターン1: 一覧表示でのリレーションアクセス

```php
// 指摘対象の例
public function index()
{
    $posts = Post::all();

    return view('posts.index', compact('posts'));
}
```

```blade
{{-- resources/views/posts/index.blade.php --}}
@foreach ($posts as $post)
    <p>{{ $post->title }} by {{ $post->user->name }}</p>
@endforeach
```

`$post->user` へのアクセスがループ内で行われるたびにクエリが発行され、投稿数を N とすると N+1 回のクエリが発生する。Controller だけを見てもビュー側のアクセスまでは分からないため、**ビュー・API リソース側で実際に参照しているリレーションを確認した上で** 指摘する。

### 修正例

```php
public function index()
{
    $posts = Post::with('user')->get();

    return view('posts.index', compact('posts'));
}
```

## 典型的なパターン2: API リソースでのネストしたリレーション

```php
// 指摘対象の例
public function index()
{
    return PostResource::collection(Post::all());
}
```

```php
// app/Http/Resources/PostResource.php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'author' => $this->user->name,
        'comment_count' => $this->comments->count(),
    ];
}
```

`PostResource` が `user` と `comments` を参照しているため、Controller 側でこれらを eager loading していないと N+1 が発生する。`comments->count()` のようにコレクション全体をロードしてから件数を数えている場合は、`withCount()` を使う方が効率的なので合わせて指摘する。

### 修正例

```php
public function index()
{
    $posts = Post::with('user')->withCount('comments')->get();

    return PostResource::collection($posts);
}
```

```php
public function toArray($request)
{
    return [
        'id' => $this->id,
        'title' => $this->title,
        'author' => $this->user->name,
        'comment_count' => $this->comments_count,
    ];
}
```

## 典型的なパターン3: ループ内で個別にクエリを発行している

```php
// 指摘対象の例（Eloquent のリレーション経由ではなく、明示的なループ内クエリ）
public function index()
{
    $userIds = Post::pluck('user_id')->unique();
    $users = [];

    foreach ($userIds as $id) {
        $users[$id] = User::find($id);
    }

    // ...
}
```

これは Eloquent のリレーション遅延読み込みによる N+1 ではないが、同じ問題（ループ内での個別クエリ発行）として指摘する。

### 修正例

```php
public function index()
{
    $userIds = Post::pluck('user_id')->unique();
    $users = User::whereIn('id', $userIds)->get()->keyBy('id');

    // ...
}
```

## 見落としやすいケース

- `paginate()` を使っている一覧でも eager loading は同様に必要（ページ内の件数分だけ N+1 が発生する）
- `with()` で1段階のリレーションを eager loading していても、さらにネストしたリレーション（例: `$post->user->profile`）にアクセスしている場合はドット記法（`with('user.profile')`）が必要
- 条件付きリレーション（`whereHas`）を使うクエリ自体は N+1 の原因にならないが、結果セットに対して個別にリレーションへアクセスする箇所がないか別途確認する
- `Model::find($id)` のような単一レコード取得（`show` アクション等）では、そのレコードに対するリレーションアクセスは基本的に N+1 にならない（N=1 のため実害は小さい）。ただし詳細ページでネストが深い場合は eager loading した方が可読性・パフォーマンス双方で望ましいため、軽微な指摘として触れてもよい

## 検出のためのヒント

- レビュー対象の Controller のアクションが返すデータ（ビューまたは API リソース）を確認し、そこで参照されているリレーション名を洗い出す
- Controller のクエリ発行部分（`::all()`, `::get()`, `::paginate()`, `::find()` 等）に、洗い出したリレーション名が `with()`/`load()` で含まれているか突き合わせる
- 開発環境で実際のクエリ数を確認したい場合、Laravel Telescope や Debugbar の導入を提案してもよい（本プロジェクトには未導入のため、導入を強制する指摘はしない）
