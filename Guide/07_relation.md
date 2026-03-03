# 預先關聯（Eager Loading）

## 情境設定

以下範例使用三個 Model：

**User 資料表**

| id | name | age |
|----|------|-----|
| 1 | OA | 18 |
| 2 | OB | 28 |
| 3 | OC | 15 |

**Article 資料表**

| id | userId | title | pageView |
|----|--------|-------|----------|
| 1 | 1 | 文章 1 | 11 |
| 2 | 2 | 文章 2 | 23 |
| 3 | 1 | 文章 3 | 31 |
| 4 | 1 | 文章 4 | 42 |
| 5 | 4 | 文章 5 | 42 |

**Comment 資料表**

| id | articleId | content |
|----|-----------|---------|
| 1 | 2 | 留言 1 |
| 2 | 2 | 留言 2 |
| 3 | 1 | 留言 3 |
| 4 | 1 | 留言 4 |
| 5 | 3 | 留言 5 |

```php
namespace Model;

class User extends \Orm\Model {
  public function articles() {
    return $this->hasMany(Article::class);
  }
}

class Article extends \Orm\Model {
  public function comments() {
    return $this->hasMany(Comment::class);
  }
}

class Comment extends \Orm\Model {}
```

---

## N+1 問題

使用關聯查詢時，若在迴圈中存取關聯屬性，會產生大量 SQL 查詢。

### 問題情境

```php
$users = User::all();
foreach ($users as $user) {
  echo $user->name;

  foreach ($user->articles as $article) {
    echo $article->title;
  }
}
```

Query Log：

```sql
SELECT * FROM `User`                              -- 1 次
SELECT * FROM `Article` WHERE `userId` = 1         -- N 次
SELECT * FROM `Article` WHERE `userId` = 2
SELECT * FROM `Article` WHERE `userId` = 3
```

共執行 **1 + N** 次查詢。當 User 有 100 筆時，就是 101 次查詢。

---

## relation() 解法

使用 `relation()` 預先載入關聯，將 N 次查詢合併為 1 次 `WHERE IN`：

```php
$users = User::relation('articles')->all();
foreach ($users as $user) {
  echo $user->name;

  foreach ($user->articles as $article) {
    echo $article->title;
  }
}
```

Query Log：

```sql
SELECT * FROM `User`                                     -- 1 次
SELECT * FROM `Article` WHERE `userId` IN (1, 2, 3)      -- 1 次
```

只需 **2 次**查詢，且系統會自動將 Article 按 `userId` 分配回各自的 User。

**方法簽名**：`static relation(string ...$args): Builder`

---

## 多個關聯

一次預載多個關聯：

```php
// 預載 User 的 articles 和 profile
$users = User::relation('articles', 'profile')->all();
```

---

## 執行原理

`relation()` 的內部流程：

1. 先執行主查詢，取得所有 Model（如 `User::all()`）
2. 對每個預載的關聯名稱（如 `articles`），呼叫每個 Model 的關聯方法取得 Builder
3. 收集所有外鍵值（如所有 User 的 `id`）
4. 用 `WHERE IN` 一次查詢所有關聯資料（如 `Article WHERE userId IN (1,2,3)`）
5. 按外鍵將結果分配回各自的 Model

### 等效的手動實作

```php
$users = User::all();

// 收集所有 User ID
$userIds = array_map(function ($user) {
  return $user->id;
}, $users);

// 一次查詢所有 Article
$allArticles = Article::whereIn('userId', $userIds)->all();

// 按 userId 分組
$articlesByUser = [];
foreach ($allArticles as $article) {
  if (!isset($articlesByUser[$article->userId])) {
    $articlesByUser[$article->userId] = [];
  }
  $articlesByUser[$article->userId][] = $article;
}

// 使用
foreach ($users as $user) {
  $userArticles = $articlesByUser[$user->id] ?? [];

  foreach ($userArticles as $article) {
    echo $article->title;
  }
}
```

`relation()` 在系統層面自動完成上述所有分類邏輯。

---

## 搭配條件

`relation()` 可與其他查詢條件組合：

```php
$users = User::where('age', '>', 10)
  ->relation('articles')
  ->order('name ASC')
  ->all();
```

---

## 注意事項

- `relation()` 只對 `all()` 有效，對 `one()` / `first()` / `last()` 無效（單筆查詢不需要預載）
- 預載的關聯會直接填入 Model 的快取，後續以屬性存取時不再執行查詢
- 可以透過 `\Orm\Model::setQueryLogFunc()` 開啟 Query Log 來驗證查詢次數（詳見 [初始設定](00_config.md)）
