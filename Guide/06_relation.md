# 關聯定義

## 情境設定

以下範例使用 `User` 和 `Article` 兩個 Model：

**User 資料表**

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵 |
| name | varchar(255) | 名稱 |
| age | int | 年齡 |

**Article 資料表**

| 欄位 | 型別 | 說明 |
|------|------|------|
| id | int | 主鍵 |
| userId | int | 外鍵，關聯 User.id |
| title | varchar(255) | 標題 |
| pageView | int | 瀏覽數 |

範例資料：

| User.id | name | age |
|---------|------|-----|
| 1 | OA | 18 |
| 2 | OB | 28 |
| 3 | OC | 15 |

| Article.id | userId | title | pageView |
|------------|--------|-------|----------|
| 1 | 1 | 文章 1 | 11 |
| 2 | 2 | 文章 2 | 23 |
| 3 | 1 | 文章 3 | 31 |
| 4 | 1 | 文章 4 | 42 |
| 5 | 4 | 文章 5 | 42 |

```php
namespace Model;

class User extends \Orm\Model {}

class Article extends \Orm\Model {}
```

---

## 關聯類型總覽

| 方法 | 方向 | 回傳（屬性存取） | 無資料時 |
|------|------|------------------|----------|
| `hasMany` | 一對多 | `array`（Model 陣列） | `[]` |
| `hasOne` | 一對一 | `?Model` | `null` |
| `belongsTo` | 反向一對一 | `?Model` | `null` |
| `belongsToMany` | 反向一對多 | `array`（Model 陣列） | `[]` |

---

## hasMany

一個 User **擁有多篇** Article。在 `User` Model 定義關聯：

```php
class User extends \Orm\Model {
  public function articles() {
    return $this->hasMany(Article::class);
  }
}
```

使用方式：

```php
$user = User::one(1);
foreach ($user->articles as $article) {
  echo $article->title;
  // 依序印出 "文章 1"、"文章 3"、"文章 4"
}

$user = User::one(3);
echo count($user->articles); // 0
```

**方法簽名**：`hasMany(string $class, ?string $fk = null, ?string $pk = 'id'): Builder`

---

## belongsTo

`belongsTo` 是 `hasMany` 的反向。一篇 Article **屬於** 一個 User：

```php
class Article extends \Orm\Model {
  public function user() {
    return $this->belongsTo(User::class);
  }
}
```

使用方式：

```php
$article = Article::one(2);
echo $article->user->name; // OB

$article = Article::one(5);
var_dump($article->user); // null（userId=4 無對應 User）
```

**方法簽名**：`belongsTo(string $class, ?string $fk = null, ?string $pk = 'id'): Builder`

---

## hasOne

與 `hasMany` 類似，但只取**第一筆**。一個 User 有一篇（第一篇）Article：

```php
class User extends \Orm\Model {
  public function article() {
    return $this->hasOne(Article::class);
  }
}
```

使用方式：

```php
$user = User::one(1);
echo $user->article->title; // 文章 1

$user = User::one(3);
var_dump($user->article); // null
```

**方法簽名**：`hasOne(string $class, ?string $fk = null, ?string $pk = 'id'): Builder`

---

## belongsToMany

`belongsTo` 的多筆版本。與 `hasMany` 方向相反，從子表角度取得**多筆**父表資料：

```php
class Article extends \Orm\Model {
  public function users() {
    return $this->belongsToMany(User::class);
  }
}
```

**方法簽名**：`belongsToMany(string $class, ?string $fk = null, ?string $pk = 'id'): Builder`

---

## 外鍵自動推導

系統會根據命名規則自動推導外鍵，不需要每次都手動指定。

### 駝峰命名（CASE_CAMEL）

| 關聯 | 自動推導的 FK | 對應 PK |
|------|---------------|---------|
| `User→hasMany(Article)` | Article 表的 `userId` | User 表的 `id` |
| `User→hasOne(Article)` | Article 表的 `userId` | User 表的 `id` |
| `Article→belongsTo(User)` | Article 表的 `userId` | User 表的 `id` |
| `Article→belongsToMany(User)` | Article 表的 `userId` | User 表的 `id` |

- `has*`：FK = 當前 Model 名稱（首字小寫）+ `Id`，如 `User` → `userId`
- `belongs*`：FK = 目標 Model 名稱（首字小寫）+ `Id`，如 `User` → `userId`

### 蛇形命名（CASE_SNAKE）

| 關聯 | 自動推導的 FK | 對應 PK |
|------|---------------|---------|
| `User→hasMany(Article)` | articles 表的 `user_id` | users 表的 `id` |
| `Article→belongsTo(User)` | articles 表的 `user_id` | users 表的 `id` |

### 自訂外鍵

當欄位不符合命名規則時，手動指定外鍵和主鍵：

```php
class User extends \Orm\Model {
  public function articles() {
    // 第二參數：外鍵（Article 表的欄位）
    // 第三參數：主鍵（User 表的欄位）
    return $this->hasMany(Article::class, 'userId', 'id');
  }
}

class Article extends \Orm\Model {
  public function user() {
    // 第二參數：外鍵（Article 表的欄位）
    // 第三參數：主鍵（User 表的欄位）
    return $this->belongsTo(User::class, 'userId', 'id');
  }
}
```

---

## 屬性存取 vs 函式呼叫

關聯的使用方式有兩種，行為不同：

### 屬性存取（無括號）

直接以**屬性**存取，會自動執行查詢並**快取**結果：

```php
$user->articles   // 第一次：執行 SQL，回傳 Model 陣列
$user->articles   // 第二次：回傳快取結果，不再查詢
```

### 函式呼叫（有括號）

以**函式**呼叫，回傳 `Builder`，可繼續附加條件：

```php
$user->articles()                          // 回傳 Builder（不執行查詢）
$user->articles()->where('pageView', '>', 20)->all()  // 附加條件後執行
```

---

## 條件關聯

利用函式呼叫回傳的 Builder 附加條件：

```php
class User extends \Orm\Model {
  public function articles() {
    return $this->hasMany(Article::class);
  }
}

$user = User::one(1);

// 取得 pageView > 20 的文章
$articles = $user->articles()->where('pageView', '>', 20)->all();
foreach ($articles as $article) {
  echo $article->title;
  // 依序印出 "文章 3"、"文章 4"
}

// 取得文章數量
$count = $user->articles()->count();
echo $count; // 3
```

---

## 關聯快取

以屬性存取的結果會被快取在 Model 實例上：

```php
$user = User::one(1);

// 第一次存取：執行 SQL
$articles = $user->articles;

// 第二次存取：使用快取，不再查詢
$articles = $user->articles;
```

> 函式呼叫（`$user->articles()`）**不會**使用快取，每次都回傳新的 Builder。
