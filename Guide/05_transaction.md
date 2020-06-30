# transaction
資料庫交易，只要在 transaction function 內回傳 **非錯** 結果，就會 **commit**，若回傳的值為 **錯** 則會 **rollback**

回傳結果若為 `null`、`false`、`0`、`[]`、`""`、`"0"`，皆為錯，所以會 rollback

```php
$result = \M\transaction(function() {
  $user = \M\User::create([
    'name' => 'OA'
  ]);

  return false;
});

// 因為 transaction 最後回傳為 false，所以沒有新增，全部數量為 0
echo \M\User::count(); // 0
```