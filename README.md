Класс для легкого тестирования моделей в Yii2
============

**Что и зачем тестировать?** Разработчики Yii-фреимворка протестировали и гарантируют правильную работу правил валидации.
Но они не гарантируют, что вы незабыли их прописать в модели или позже не удалили некоторые из них. Поэтому важно писать unit-тесты
для моделей. Данный класс облегчит тестирование правил валидации ваших моделей.

## Установка:##
```bash
$ php composer.phar require developeruz/yii-matcher "*"
```

## Пример использования:##

```php
use developeruz\yii_matcher\ModelMatcher;

class ValidateTest extends TestCase {

    public function testPhoneIsSafeOnlyInRegistration()
    {
         $userModel = new ModelMatcher('app\models\User');
         $userModel->shouldBeSafe('phone', 'registration');
         $userModel->shouldBeNotSafe('phone');
    }
    
     public function testUserHasPostsRelation()
     {
         $userModel = new ModelMatcher('app\models\User');
         $userModel->hasMany('posts', 'app\models\Post', ['user_id' => 'id']);
     }
     
     public function testLoginLength()
     {
          $userModel = new ModelMatcher('app\models\User');
          $userModel->matchLength('login', 3, 20);
     }
}
```
## Доступные методы: ##

- **shouldBeSafe()** и **shouldBeNotSafe()** - проверка на возможность массового присвоения атрибута 
- **shouldBeRequired()** и **shouldBeNotRequired()** - проверка на обязательность заполнения параметра 
- **matchLength()**  - проверка на длинну строки. Для того, чтобы провести проверку только на  *min* или *max*, 
задайте второй параметр как null.
- **hasOne()** и **hasMany()**  - проверка на наличие связей 

Все методы принимают в качестве параметра имя аттрибута и необязательный параметр - сценарий.

*PS: С радостью приму pull-request с дополнительными matcher-ами. Или пишите в issue какие еще валидаторы стоит добавить*