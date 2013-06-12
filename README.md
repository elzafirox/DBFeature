## Feature
Класс Feature это библиотека php для работы с Базой Данных с использованием PDO.

В классе Useful можно создавать полезные функции, которые не особо зависят от остальных данных

Постепенно в ДрайверБД (class.driverdb.php) будет добавляться функционал

В файле model.feature.php создана модель Feature, в которой пишутся пользовательские функции

Для того чтобы обращаться к методам вы должны сначала обратиться к Таблице, к которой эти Методы нужно применить

Например если у вас есть Таблица `users`, то обращаться к ней нужно так (учитывая регистр имени таблицы!):

```php
$m = new Feature();

// users - это имя Вашей таблицы !!!
$m->users()->GetData();
```

методы:

->GetData();
Получет все данные из Таблицы в виде ассоциативного массива

->DelOne($idn,$val);
Удаляет Запись из Таблицы где Поле [$idn] равно Значению [$val]

->ExistsRow($clm,$rc);
Проверка существует ли Запись с Полем [$clm], содержащим Значение [$rc] в Таблице

->UpdOne($clm,$upd,$idn,$val); 
Обновить Поле [$clm] Значением [$upd] где Поле [$idn] равно Значению [$val]

->GetOne($clm,$idn,$val);
Получает данные Поля [$clm] из Таблицы где Поле [$idn] равно Значению [$val]

->CountRows($clm,$idn,$val);
Подсчет кол-ва записей Поля [$clm] в Таблице, где Поле [$idn] равно Значению [$val]

->GetRec($idn,$val);
Фетч всех Полей Записи где Поле [$idn] равно Значению [$val] в Ассоциативный массив из Таблицы

->GetEvery($clm,$idn,$val);
Фетч всех Полей [$clm] Таблицы, где Поле [$idn] равно Значению [$val] в Ассоциатвиный массив

->PutData($params = array());
Вставка данных в Таблицу, Ключ массива это Поле, а значение массива это Данные, которые вставляются в это поле

->SetTable($t);
Установка текущей таблицы

Чтобы использовать данную либу подключите к своему сценарию php файл model.feature.php, как это показано в тестовом контроллере.

Если у вас все получилось правильно подключить, то вы должны увидеть фразу "Привет, мир!". Удалите ее из конструктора модели (model.feature.php) чтобы она вам не мешала.