<p>
    Заказ стоимостью <strong>{{sum}} RUB</strong> wiil be будет оплачен через систему <strong>EgoPay</strong>.
</p>
<form action="{{action}}" method="post">
    <input name="ready_to_pay" value="1" type="hidden" />
    <input name="BuyButton" value="Pay" type="submit" />
</form>