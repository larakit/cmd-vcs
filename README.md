# lk-vcs
~~~
$php artisan larakit:vcs
~~~
Достаточно часто, в процессе разработки приходится влезать в свои же vendor-пакеты. 

Поэтому потребовалась возможность быстрого поиска измененных файлов.

Обязательное условие: при обновлении composer использовать опцию **--prefer-dist**
~~~
$composer install --prefer-dist
~~~
~~~
$composer update --prefer-dist
~~~
Результат выполнения команды
~~~
Сканируем установленные пакеты на наличие папок .git и .svn
 66/66 [============================] 100% 21 secs/21 secs 20.0 MiB
Сканирование завершено.
Имеются изменные файлы SVN: 

+-----+---------------------------------+----------------------------------+----------+
| VCS | package                         | file                             | type     |
+-----+---------------------------------+----------------------------------+----------+
| SVN | /vendor/project/backend         | src/Section/Model/MediumType.php | Изменено |
| -   | -                               | -                                | -        |
| SVN | /vendor/larakit/laravel-larakit | src/Larakit/TwigExtension.php    | Изменено |
+-----+---------------------------------+----------------------------------+----------+

~~~
