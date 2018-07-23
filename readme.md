### Installation

1. Установить Node.js версии **4.x** или **6.x**. Настройки оставить по умолчанию. Брать отсюда https://nodejs.org/en/
2. Установить Ruby версии **2.2.x** (**x86**-версию, не 64bit). **Обязательно** поставить галку "Add Ruby to your PATH". Брать тут http://rubyinstaller.org/downloads/
3. Создать SSL-сертификат для Ruby. В папке `C:\Ruby22\lib\ruby\2.2.0\rubygems\ssl_certs` создать файл `root-r1.pem` и скопировать в него ключ "R1 GlobalSign Root Certificate" (нажать "View Base64") отсюда https://support.globalsign.com/customer/portal/articles/1426602-globalsign-root-certificates 
4. Далее работаем в cmd, запущенной под админом. Поочередно выполнить команды:

```sh
$ npm install -g bower
$ npm install -g gulp-cli
$ npm install -g webpack
$ gem install sass
$ gem install compass
```

5. Перейти в папку `/local/dev/` проекта. Исходники скриптов лежат в папке `./es6`, исходники стилей - в папке `./sass`
6. Выполнить команды:

```sh
$ bower install
$ npm install
$ gulp
```

7. Команда `gulp` запускает task gulp-а в режиме watch, который будет следать за изменениями исходных js- и scss-файлов и автоматически компилировать рабочие файлы.
Первая компиляция может идти достаточно долго (до минуты), затем - доли секунды.
8. Команда `gulp dev` компилирует рабочие файлы без вырезания `console.log` и без минификации скриптов.


### Замечание по `/local/dev/sass/`
    в папке `include` не должно быть *.scss-файлов, которые генерируют реальный css. Только миксины и %-классы.
Это необходимо для того, чтобы избежать дублирования стилей.

### Agents
| Agent | Period | Description |
| ------ | ------ |
| updateBrandsPictures() | every 3 hours | Обновляет картинки брендов из 1С |
| updateStoresInfo() | every 1 hour | Обновляет инфу по складам из 1С |


### GRID SYSTEM 24

# классы для родительских div-ов: 
    **.container** - скачкообразное изменение ширины контейнера
    **.container-fluid** - резиновый контейнер
    **.row** - просто сбрасывает float после div-а

# Классы для колонок
    **X** - число от 0 до 24
    **SIZE** - код ширины (XXL, XL, LG, MD, SM, XS)

    **.col-X**, **col-SIZE-X**  - ширина элемента X колонок
    **.offset-X**, **offset-SIZE-X**  - отступ слева шириной X колонок
 
    **.float-SIZE-left**, **.float-SIZE-right**, **.float-SIZE-none** - жестко задать float при ширине экрана SIZE
    **.hidden-SIZE-down**, **.hidden-SIZE-up** - скрыть элемент при ширине экрана меньше/больше, чем SIZE