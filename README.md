```php
$sql1 = (string) DML::query()->select('name')->from('user')->where('id = 1');

$sql2 = (string) DML::query()->from('user')->where('id = 1')->select('name');

$sql3 = (string) DML::query()->where('id = 1')->select('name')->from('user');

$q = DML::query()
$q->from('user');
$q->where('id = 1');
$q->select('name');
$sql4 = (string) $q;
```

As a result `$sql1`, `$sql2`, `$sql3` and `$sql4` contain same string:

```SQL
SELECT name FROM users WHERE id = 1
```

**Method** | **Parameters** | **Example** | **Resulting SQL**
:--- | :---: | :--- | :---
select | nothing, single, multiple | `select()` | `SELECT *`
insert | single | `insert('user')` | `INSERT INTO user`
update | single | `update('user')` | `UPDATE user`
delete | nothing | `delete()` | `DELETE`
from | single, subquery | `from('user')` | `FROM user`
where | single | `where('id = 1')` | `WHERE id = 1`
whereNot | single | `whereNot('id = 1')` | `WHERE NOT id = 1`
whereAnd | multiple | `whereAnd('id > 1', 'name = "John"')` | `WHERE id > 1 AND name = "John"`
whereOr | multiple | `whereOr('id = 1', 'name = "John"')` | `WHERE id = 1 OR name = "John"`
join | single, three | `join('order')` | `INNER JOIN order`
leftJoin | single, three | `leftJoin('order')` | `LEFT JOIN order`
rightJoin | single, three | `rightJoin('order')` | `RIGHT JOIN order`
on | single | `on('user.id = order.user_id')` | `ON user.id = order.user_id`
and | single | `and('id = 1')` | `AND id = 1`
or | single | `or('id = 1')` | `OR id = 1`
groupBy | single | `groupBy('is_active')` | `GROUP BY is_active`
having | single | `having('COUNT(*) > 1')` | `HAVING COUNT(*) > 1`
orderBy | single | `orderBy('id')` | `ORDER BY id`
limit | single | `limit(10)` | `LIMIT 10`
set | two | `set('is_active', 1)` | `SET is_active = 1`
fields | multiple | `fields(name, is_active)` | `(name, is_active)`
values | multiple | `values('"John"', 1)` | `VALUES ("John", 1)`