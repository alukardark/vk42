<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetPageProperty("title", "Оплата и доставка - Автоcервис-центр «Континент шин»  в Кемерово и Новокузнецке");
$APPLICATION->SetPageProperty("description", "Информация об оплате и доставке сети автосервисов «Континент шин»");
$APPLICATION->SetPageProperty("keywords", "шины, диски, технические жидкости, моторные масла, антифриз, услуги сервис-центров, обслуживание автомобиля, станция технического обслуживания, онлайн-магазин шин, покупка шин и дисков онлнайн, подбор шин по автомобилю, аккумуляторы, «ВК» сервис-центры, легковые шины, грузовые шины, мото шины");
$APPLICATION->SetTitle("Оплата и доставка");

if (!strstr($APPLICATION->GetCurPage(true), "index.php"))
{
    $APPLICATION->AddChainItem($APPLICATION->GetTitle());
}
echo '<section class="section ve">';
?><br>
<h3 style="text-transform: uppercase; margin-bottom: 15px; font-size: 27px; margin-top: 20px;">
Оплата </h3>
<p>
	 Для удобства покупателей нашего интернет-магазина мы предусмотрели несколько способов оплаты заказа:
</p>
<div class="row">
	<div class="col-3 col-sm-24">
 <img alt="Оплата при получении" src="/upload/medialibrary/289/pay_and_deliv_1.png" style="min-width:100px; width:100px;" height="100">
	</div>
	<div class="col-21 col-lg-20 col-md-19 offset-lg-1 offset-md-2 col-sm-24 offset-sm-0" style="margin-bottom: 45px;">
		<p style="font-size:22px; color:#ff6100; margin-bottom: 15px; margin-left: 15px;">
			 Оплата при получении
		</p>
		<p style="margin-left: 15px; margin-bottom: 0px;">
			 После оформления заказа выбранный вами товар отправляется в точку выдачи,&nbsp;либо будет доставлен по указанному вами адресу, а оплачивается при получении. Для того, чтобы оплатить товар при получении, в процессе оформления заказа в качестве способа оплаты выберите «Оплата при получении».
		</p>
	</div>
</div>
<hr style="background: #d4d4d4; width:100%; height:1px; margin-bottom: 45px;">
<div class="row">
	<div class="col-3 col-sm-24">
 <img alt="Онлайн-оплата" src="/upload/medialibrary/a37/pay_and_deliv_3.png" style="min-width:100px; width:100px;" height="100">
	</div>
	<div class="col-21 col-lg-20 col-md-19 offset-lg-1 offset-md-2 col-sm-24 offset-sm-0">
		<p style="font-size:22px; color:#ff6100; margin-bottom: 15px; margin-left: 15px;">
			 Онлайн-оплата
		</p>
		<p style="margin-left: 15px; margin-bottom: 0px;">
			 Заказ оплачивается непосредственно после его оформления с помощью банковской карты. При оформлении заказа необходимо указать «Онлайн-оплата» в качестве способа оплаты, указать размер предоплаты, а затем перейти непосредственно к предоплате. Онлайн-оплата доступна только физическим лицам. Доступна оплата картами: VISA, MasterCard, МИР, JCB и Халва.
		</p>
		<table style="border:0px;">
		<tbody>
		<tr style="border:0px;">
			<td style="padding-left: 15px; border:0px;">
 <img alt="VISA" src="/upload/medialibrary/e2b/card_7.jpg" style="margin-right: 10px;" width="80" height="58"> <img alt="MasterCard" src="/upload/medialibrary/08b/card_6.jpg" style="margin-right: 10px;" width="80" height="58"> <img src="/upload/medialibrary/c5f/card_4.jpg" alt="МИР" style="margin-right: 10px;" width="80" height="58"> <img alt="JCB" src="/upload/medialibrary/086/card_5.jpg" style="margin-right: 10px;" width="80" height="58"> <a href="https://app.halvacard.ru/order/?utm_medium=Partner&utm_source=%7bNAME%7d&utm_campaign=halva" title="Подробнее о карте Халва" target="_blank"><img src="/upload/medialibrary/f72/halva_new2.jpg" alt="Халва" width="80" height="58"></a>
			</td>
		</tr>
		</tbody>
		</table>
		<p style="margin-left: 15px; color: #222222; font-size:14px; margin-bottom: 15px;">
			 При оплате заказа банковской картой, обработка платежа (включая ввод номера карты) происходит на защищенной странице процессинговой системы, которая прошла международную сертификацию. Это значит, что Ваши конфиденциальные данные (реквизиты карты, регистрационные данные и др.) не поступают в интернет-магазин, их обработка полностью защищена и никто, в том числе наш интернет-магазин, не может получить персональные и банковские данные клиента.&nbsp;При работе с карточными данными применяется стандарт защиты информации, разработанный международными платёжными системами Visa и MasterCard - Payment Card Industry Data Security Standard (PCI DSS), что обеспечивает безопасную обработку реквизитов Банковской карты Держателя. Применяемая технология передачи данных гарантирует безопасность по сделкам с Банковскими картами путем использования протоколов Secure Sockets Layer (SSL), Verified by Visa, Secure Code, и закрытых банковских сетей, имеющих высшую степень защиты.&nbsp;В случае возврата, денежные средства возвращаются на ту же карту, с которой производилась оплата.
		</p>
		<p style="margin-left: 15px; font-size:14px; margin-bottom: 50px;">
			 Также информируем Вас о том, что при запросе возврата денежных средств при отказе от покупки, возврат производится исключительно на ту же банковскую карту, с которой была произведена оплата!»
		</p>
	</div>
</div>
<hr style="background: #d4d4d4; width:100%; height:1px; margin-bottom: 60px;">
<div class="row" style="margin-bottom: 45px;">
	<div class="col-3 col-sm-24">
 <img alt="Банковский перевод" src="/upload/medialibrary/7dc/pay_and_deliv_2.png" style="min-width:100px; width:100px;" height="100">
	</div>
	<div class="col-21 col-lg-20 col-md-19 offset-lg-1 offset-md-2 col-sm-24 offset-sm-0">
		<p style="font-size:22px; color:#ff6100; margin-bottom: 15px; margin-left: 15px;">
			 Банковский перевод
		</p>
		<p style="margin-left: 15px; margin-bottom: 0px;">
			 Приобретаемый товар оплачивается посредством банковского перевода по формируемому на сайте счету. Для формирования счета, в момент оформления заказа в качестве способа оплаты необходимо выбрать «Банковский перевод», после перехода к оплате сайт сформирует счет и предложит его скачать.
		</p>
	</div>
</div>
<hr style="background: #d4d4d4; width:100%; height:1px; margin-bottom: 45px;">
<div class="row" style="margin-bottom: 45px;">
	<div class="col-3 col-sm-24">
 <img alt="Покупка в кредит" src="/upload/medialibrary/406/pay_and_deliv_4.png" style="min-width:100px; width:100px;" height="100">
	</div>
	<div class="col-21 col-lg-20 col-md-19 offset-lg-1 offset-md-2 col-sm-24 offset-sm-0">
		<p style="font-size:22px; color:#ff6100; margin-bottom: 15px; margin-left: 15px;">
			 Покупка в кредит
		</p>
		<p style="margin-left: 15px; margin-bottom: 0px;">
			 Для того, чтобы приобрести товар в кредит, в процессе оформления заказа необходимо выбрать соответствующий способ оплаты. После завершения оформления заказа потребуется заполнение онлайн-анкеты. По результатам анкетирования вы получите предварительную информацию о возможности получения кредита.&nbsp; &nbsp; &nbsp;
		</p>
		<p style="margin-left: 15px; margin-bottom: 0px;">
		</p>
	</div>
</div>
 <a name="ankor"></a>
<h3 style="text-transform: uppercase; margin-bottom: 15px; font-size: 27px;">
Центры выдачи товаров</h3>
<p>
	 В процессе оформления заказа вы можете выбрать подходящую точку выдачи, на которую мы доставим товар в кратчайшие сроки. На данный момент доступны следующие точки выдачи товара:
</p>
<h4><span style="color: #ff6100;"><u>Кемерово</u></span></h4>
<div class="row" style="background-color: #f0f0f0;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
			<a class="zoom" href="/upload/medialibrary/51e/noviy_magazin_rebrand_hd.jpg"><img src="/upload/medialibrary/bbf/noviy_magazin_rebrand.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150px"></a> 
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин» «на&nbsp;Карболитовской»</b> <br>
		 ул. Карболитовская, 20<br>
 <br>
 <b><a href="tel:+73842777321">8 (3842) 777-321</a></b><br>
 <a href="https://goo.gl/4csBVd" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Понедельник — Суббота</b><br>
		 c 9:00 до 20:00<br>
 <br>
 <b>Воскресенье</b><br>
		 с 9:00 до 18:00
	</div>
</div>
<div class="row" style="background-color: #f8f8f8;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
 <a class="zoom" href="/upload/medialibrary/6d4/tv_bp_2.jpg"><img src="/upload/medialibrary/a50/tv_2.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150px"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин» «На&nbsp;Марковцева»</b> <br>
		 б-р Строителей, 56, корпус 1<br>
 <br>
 <b><a href="tel:+73842777319">8 (3842) 777-319</a></b><br>
 <a href="https://goo.gl/wy3ANS" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Круглосуточно</b>
	</div>
</div>
<div class="row" style="background-color: #f0f0f0;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
 <a href="/upload/medialibrary/f01/tv_bp_1.jpg" class="zoom"><img src="/upload/medialibrary/a4e/tv_1.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин»<br>
		 «Аврора»</b> <br>
		 б-р Строителей, 2<br>
 <br>
 <b><a href="tel:+73842777306">8 (3842) 777-306</a></b><br>
 <a href="https://goo.gl/EXXSs6" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Круглосуточно</b><br>
	</div>
</div>
<div class="row" style="background-color: #f8f8f8;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
			  <a href="/upload/medialibrary/53e/noviy_magazin_rebrand_2_hd.jpg" class="zoom"><img src="/upload/medialibrary/6fa/noviy_magazin_rebrand_2.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин»<br>
		 «Заводский»</b> <br>
		 пр. Кузнецкий, 43<br>
 <br>
 <b><a href="tel:+73842777307">8 (3842) 777-307</a></b><br>
 <a href="https://goo.gl/dcKttd" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Круглосуточно</b><br>
	</div>
</div>
<div class="row" style="background-color: #f0f0f0;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
 <a href="/upload/medialibrary/6af/tv_center_hq.jpg" class="zoom"><img src="/upload/medialibrary/2c5/tv_center.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин»<br>
		 «Центральный»</b> <br>
		 пр. Октябрьский, 30а<br>
 <br>
 <b><a href="tel:+73842777311">8 (3842) 777-311</a></b><br>
 <a href="https://goo.gl/BH6MMB" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Круглосуточно</b><br>
	</div>
</div>
 <br>
 <br>
<h4><span style="color: #ff6100;"><u>Новокузнецк</u></span></h4>
 <!-- <div class="row" style="background-color: #f0f0f0;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
 <a class="zoom" href="/upload/medialibrary/a81/stroiteley.jpeg"><img width="150px" src="/upload/medialibrary/5ca/adres_1_3.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин» «на&nbsp;Строителей»</b> <br>
		 пр. Строителей, 95<br>
 <br>
 <b><a href="tel:+73843200510">8 (3843) 200-510</a></b><br>
 <a href="https://goo.gl/iRQK8N" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Ежедневно</b><br>
		 c 9:00 до 21:00
	</div>
</div> -->
<div class="row" style="background-color: #f8f8f8;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
 <a class="zoom" href="/upload/medialibrary/50f/kurako.jpg"><img src="/upload/medialibrary/5df/kurako_min.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150px"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Сервис-центр «Континент шин» «на&nbsp;Курако»</b> <br>
		 пр. Курако, 21<br>
 <br>
 <b><a href="tel:+73843200509">8 (3843) 200-509</a></b><br>
 <a href="https://goo.gl/FxgdYL" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Ежедневно</b><br>
		 c 9:00 до 21:00
	</div>
</div>
 <br>
 <br>
<h4><span style="color: #ff6100;"><u>Ленинск-Кузнецкий</u></span></h4>
<div class="row" style="background-color: #f8f8f8;">
	<div class="col-3 offset-2 offset-xl-1 col-xl-4 col-lg-8 offset-lg-2 col-md-8 col-sm-24 offset-sm-0">
		<div style="padding-top: 15px; padding-left: 25px; padding-bottom: 10px;">
 <a class="zoom" href="/upload/medialibrary/8f1/vianor.jpg"><img src="/upload/medialibrary/52e/vianor_small.jpg" style="-moz-border-radius: 120px; -webkit-border-radius: 120px; border-radius: 120px; overflow: hidden;" width="150px"></a>
		</div>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-1 offset-lg-0 offset-sm-0 col-md-12" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px;">
 <b>Шинный центр VIANOR</b> <br>
		 ул. Шакурина, 4/4<br>
 <br>
 <b> <a href="tel:+73845649560">8 (38456) 49-560</a></b><br>
 <a href="https://goo.gl/5kvdL3" title="Посмотреть на Google-картах" target="_blank" rel="nofollow">Посмотреть на карте</a>
	</div>
	<div class="col-8 col-lg-12 col-sm-24 offset-2 offset-lg-0 offset-xl-1 col-md-12 offset-sm-0" style="padding-top: 25px; padding-bottom: 25px; padding-left: 25px; padding-right:25px;">
 <b>Будни </b>
		c 09:00 до 19:00 <br>
 <b>Суббота</b>
		c 09:00 до 16:00 <br>
 <b>Воскресенье</b>
		c 09:00 до 15:00 <br>
	</div>
</div>
 <br>
 <a name="dostavka"></a>
<p style="margin-bottom: 50px;">
	 Срок доставки может варьироваться в зависимости от наличия того или иного товара в Вашем городе. Узнать срок доставки конкретного товара вы можете, перейдя на страницу с детальным описанием товара. Ориентировочная дата поступления заказа в выбранную Вами точку выдачи видна в процессе оформления заказа.
</p>
<h3 style="text-transform: uppercase; margin-bottom: 15px; font-size: 27px; ">
Доставка</h3>
<p>
 <b><span style="color: #f16522;">Адресная доставка</span></b>&nbsp;&nbsp;
</p>
<p>
	 Ваш заказ мы доставим по указанному адресу. Услуга доступна для городов: <b>Кемерово, Белово, Полысаево,&nbsp;Киселевск, Прокопьевск</b><b>.</b>
</p>
<p>
 <i>Стоимость услуги:</i>
</p>
<p>
</p>
<ul>
	<li>
	<p>
		 Кемерово - 200 р. Оплата услуги производится водителю в момент получения заказа.
	</p>
 </li>
	<li>
	<p>
		 Белово, Полысаево, Киселевск, Прокопьевск - бесплатно, если в Вашем заказе имеются автошины и (или) диски общим количеством 4 и более штуки. В остальных случаях стоимость услуги доставки составляет 500 р. Оплата услуги производится водителю в момент получения заказа.
	</p>
 </li>
</ul>
<p>
</p>
<p>
	 Точное время доставки с Вами согласует менеджер интернет-магазина после оформления заказа.<br>
</p>
<p>
 <b><span style="color: #f16522;">Доставка товара транспортными компаниями</span></b>
</p>
<p>
</p>
<p>
	 Ваш заказ мы отправим транспортной компанией в любой регион России. Для оформления доставки товара через транспортную компанию необходимо связаться с менеджером интернет-магазина.
</p>
<p>
 <br>
</p><?
echo '</section>';
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>