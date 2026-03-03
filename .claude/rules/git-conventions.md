# Git 開發規範

## Merge 必須使用 `--no-ff`

所有 merge 一律使用 `--no-ff`（no fast-forward），保留完整分支歷史：

```bash
# ✓ 正確
git merge --no-ff feat/my-feature

# ✗ 禁止：fast-forward merge 會丟失分支資訊
git merge feat/my-feature
```

這是鐵則，無例外。
