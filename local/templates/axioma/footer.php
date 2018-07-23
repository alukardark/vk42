<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

</div><!-- end div.body-content -->

<? \Axi::GF("footer"); ?>
<? \Axi::GF("nav_mobile"); ?>


<? //if (strstr($curAlias, 'catalog-page') || strstr($curAlias, 'actions-page')): ?>
<? if (0): ?>
    <div id="buy_one_click" class="form-one-click-container">
        <i  title="Закрыть" onclick="Form.closeBuyOneClickForm(this)" class="ion-ios-close-empty"></i>
        <?
        $APPLICATION->IncludeFile("/include/forms/form_buy_one_click.php");
        ?>
    </div>
<? endif; ?>

<? if ((isAdmin() || SHOW_HELP_AKB) && \CSite::InDir('/akkumulyatory/')): ?>
    <div id="help_akb" class="form-one-click-container">
        <i  title="Закрыть" data-form="#help_akb" onclick="Form.closeForm(this)" class="ion-ios-close-empty"></i>
        <?
        $APPLICATION->IncludeFile("/include/forms/form_help_akb.php");
        ?>
    </div>
<? endif; ?>

<? if (($USER->IsAdmin() || SHOW_DELIVERY_CALC) /* && \CSite::InDir('/akkumulyatory/') */): ?>
    <div id="delivery_calc" class="form-one-click-container form-one-click-container--delivery-calc">
        <i  title="Закрыть" data-form="#delivery_calc" onclick="Form.closeForm(this)" class="ion-ios-close-empty"></i>
        <?
        $APPLICATION->IncludeFile("/include/forms/form_delivery_calc.php");
        ?>
        <div id="delivery_calc-results" class="delivery_calc-results"></div>
        <div class="delivery-calc-note"><? \Axi::GT("delivery-calc-note", 'text'); ?></div>
    </div>

    <div id="delivery-calc-button-container" class="delivery-calc-button-container" style="display: none;">
        <div
            id="delivery-calc-button-button"
            class="delivery-calc-button-container-button noselect"
            title="Расчет доставки"
            onclick="Form.toggleForm(this);"
            data-form="#delivery_calc"
            >
            <i class="ion-ios-information-outline"></i><span>Доставка транспортной компанией</span>
        </div>
    </div>
<? endif; ?>


<div id="form-support" class="form-one-click-container">
    <i  title="Закрыть" data-form="#form-support" onclick="Form.closeForm(this)" class="ion-ios-close-empty"></i>
    <?
    $APPLICATION->IncludeFile("/include/forms/form_support.php");
    ?>
</div>

</div><!-- end div.body -->

<div class="popup-map" id="map"></div>

<div id="wait"><figure><i></i></figure></div>

<div id="alert">
    <div class="alert-icon-close" title="Закрыть" onclick="App.hideAlert();"><i class="ion-ios-close-empty"></i></div>

    <div class="alert-icon-warn"><i class="ion-alert"></i></div>

    <div class="alert-content">
        <div class="alert-content-title">Ошибка</div>
        <div class="alert-content-text"></div>
        <div class="alert-content-button"><button onclick="App.hideAlert();">Хорошо</button></div>
    </div>
</div>

<? if ((isAdmin() || SHOW_HELP_AKB) && \CSite::InDir('/akkumulyatory/')): ?>
    <div id="help-akb" class="help help-akb">
        <div
            id="help-akb-button"
            class="help-button help-button-akb noselect"
            title="Помощь в подборе аккумулятора"
            onclick="Form.toggleForm(this);"
            data-form="#help_akb"
            >
            <i class="ion-ios-information-outline"></i><span>Помощь в подборе аккумулятора</span>
        </div>
    </div>
<? else : ?>
    <div id="help" class="help">
        <div class="help-button noselect" title="Информация о поддержке" onclick="App.showHelp()">
            <i class="ion-ios-help-outline"></i><span>Нашли ошибку на сайте? Пишите нам!</span>
        </div>

        <div class="help-content">
            <div class="help-text"><? \Axi::GT("footer/help-text", "help-text"); ?></div>
            <div class="help-phone phone"><? \Axi::GT("footer/help-phone", "help-phone"); ?></div>
            <i title="Закрыть" onclick="App.hideHelp()" class="ion-ios-close-empty"></i><span></span>

            <button
                title="Задать вопрос"
                class="noselect"
                data-form="#form-support"
                onclick="Form.toggleForm(this);"
                >
                <span>Задать вопрос</span>
            </button>
        </div>
    </div>
<? endif; ?>


<? if (isAdmin() || SHOW_SERVICE_ENTRY): ?>
    <div id="service-entry" class="btn-se">
        <div
            id="btn-se"
            class="btn-se__button noselect"
            title="Записаться на услуги"
            onclick="Form.toggleForm(this);"
            data-form="#form-service-entry"
            >
            <i class="ion-ios-information-outline"></i><span>Записаться на услуги</span>
        </div>
    </div>


    <div id="form-service-entry" class="form-one-click-container form-se__container">
        <i  title="Закрыть" data-form="#service_entry" onclick="Form.closeForm(this)" class="ion-ios-close-empty"></i>
        <?
        $APPLICATION->IncludeFile("/include/forms/form_service_entry.php");
        ?>
    </div>
<? endif; ?>



<div id="attention" class="attention <?= $APPLICATION->get_cookie("USER_READED_ATTENTION") ? "" : "opened" ?> ">
    <div class="attention-content">
        <div class="attention-text"><? \Axi::GT("footer/attention-text", "attention-text"); ?></div>

        <div class="attention-actions row">
            <a class="float-left" href="/info/privacy-policy/" title="Подробнее" target="_blank">Подробнее</a>

            <button
                title="Согласен"
                class="float-right noselect"
                onclick="App.hideAttention();"
                >
                <span>Согласен</span>
            </button>
        </div>
    </div>
</div>

<button title="Наверх" id="up-button" class="noselect"><i class="ion-ios-arrow-up"></i></button>


</body>

<? if ($curAlias == 'index-page' || strstr($curAlias, 'services-page') || strstr($curAlias, 'uslugi-page')): ?>
    <script async src="//api-maps.yandex.ru/2.1/?lang=ru_RU&onload=yml.loadedSetTrue"></script>
<? endif; ?>

<!-- Yandex.Metrika counter -->
<script async>
                    (function (d, w, c) {
                        (w[c] = w[c] || []).push(function () {
                            try {
                                w.yaCounter12153865 = new Ya.Metrika({
                                    id: 12153865,
                                    clickmap: true,
                                    trackLinks: true,
                                    accurateTrackBounce: true,
                                    webvisor: true,
                                    trackHash: true,
                                    ut: "noindex"
                                });
                            } catch (e) {
                            }
                        });

                        var n = d.getElementsByTagName("script")[0],
                                s = d.createElement("script"),
                                f = function () {
                                    n.parentNode.insertBefore(s, n);
                                };
                        s.type = "text/javascript";
                        s.async = true;
                        s.src = "https://mc.yandex.ru/metrika/watch.js";

                        if (w.opera === "[object Opera]") {
                            d.addEventListener("DOMContentLoaded", f, false);
                        } else {
                            f();
                        }
                    })(document, window, "yandex_metrika_callbacks");
</script>
<!-- /Yandex.Metrika counter -->

<script async>
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

    ga('create', 'UA-100225776-1', 'auto');
    ga('send', 'pageview');

</script>
</html>