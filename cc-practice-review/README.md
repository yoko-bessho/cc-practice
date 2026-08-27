# cc-practice-review

Laravel プロジェクトのコードレビューを支援する Claude Code Plugin。

## 含まれる Skill

### review-controller

Laravel の Controller クラス（`app/Http/Controllers/` 配下）を以下の3観点でレビューする。

- **PSR-12 準拠** — インデント、命名規則、可視性修飾子など
- **バリデーションの有無** — Form Request の利用状況、Mass Assignment 対策、認可
- **N+1 問題** — 一覧・詳細表示での eager loading の過不足

## インストール（ローカルでの試用）

```bash
cc --plugin-dir /path/to/cc-practice-review
```

## 使い方

Claude Code で以下のように話しかけると Skill が自動的に読み込まれる。

- 「`app/Http/Controllers/PostController.php` をレビューして」
- 「このコントローラーが PSR-12 に準拠しているか確認して」
- 「N+1 問題がないか調べて」

詳細は `skills/review-controller/SKILL.md` を参照。
