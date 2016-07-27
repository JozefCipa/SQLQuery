# SQLQuery
Loads SQL queries from files to your code.

Just define queries with logical context in separate files, add `@SQLName name` to each part and simply import your query to code.

It's making your code **shorter**, **cleaner** and **maintainable** for you and your collaborators.
## How to use it ?

```php

//index.php

require "SQLQuery.php";

//set dir where your sql files are located
//default is ./sql
SQLQuery::setSqlDir("./your_sql_dir");

//DB connection...
$db = "your db object";

$db->query(SQLQuery::import('getById')->from('user'));
//bind :id param as usually
//execute query
//...
```

```sql
--user.sql

--@SQLName getById
SELECT * FROM users WHERE id=:id

--@SQLName otherQueries
...
```

```
Directory tree:
.
+--your_sql_dir
|  +--user.sql
|
+--index.php
+--SQLQuery.php
```
