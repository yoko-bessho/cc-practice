# レビュー結果の出力例

以下は `PostController` を対象にレビューした場合の出力例。実際のレビューでは、対象ファイルの内容に応じて指摘箇所・内容を差し替える。

## 対象コード（例）

```php
<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    function index()
    {
        $posts = Post::all();
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        Post::create($request->all());
        return redirect()->route('posts.index');
    }
}
```

## 出力例

```
## PSR-12 準拠
- [指摘] app/Http/Controllers/PostController.php:10 — `function index()` に可視性修飾子がない。`public function index()` に修正する。

## バリデーション
- [指摘] app/Http/Controllers/PostController.php:18 — `store` アクションでバリデーションが一切行われず、`$request->all()` の値をそのまま `Post::create()` に渡している。Mass Assignment のリスクがあるため、`StorePostRequest` を作成し `$request->validated()` を使う形に修正する。

## N+1 問題
- [指摘] app/Http/Controllers/PostController.php:12 — `Post::all()` で取得した `$posts` に対し、`resources/views/posts/index.blade.php` 内で `$post->user->name` を参照している（要ビュー確認）。一覧のレコード数分クエリが発行されるため、`Post::with('user')->get()` に修正する。

## まとめ
- バリデーション未実装（Mass Assignment のリスク）が最も優先度の高い指摘。次点で N+1 問題、最後に PSR-12 の軽微な指摘。
```

## 指摘がない場合の書き方

該当する観点で問題が見つからなかった場合も、観点自体は省略せず以下のように明記する。

```
## PSR-12 準拠
- 問題なし
```
