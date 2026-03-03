# Git Commit

根據專案規範提交變更：

## 步驟

1. 執行 `git status` 和 `git diff` 查看變更
2. 分析變更內容，選擇適當的 type
3. 撰寫繁體中文 commit 訊息
4. 執行 commit（不要加 Co-Authored-By）

## Commit 格式

```
<type>: <描述>
```

## Type 對照表

| Type | 使用時機 |
|------|----------|
| feat | 新功能 |
| fix | 修復 bug |
| docs | 文件變更 |
| style | 程式碼風格（不影響功能） |
| refactor | 重構（不是新功能也不是修 bug） |
| perf | 效能優化 |
| test | 測試相關 |
| chore | 建置、工具、設定變更 |

## 注意事項

- 訊息必須使用**台灣繁體中文**
- 不要標註是由 AI 產生
- 描述要簡潔明瞭，說明「做了什麼」
