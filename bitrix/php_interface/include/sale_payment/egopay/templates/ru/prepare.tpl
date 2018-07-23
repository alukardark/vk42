<div class="section section-pay-result">
    <div class="section-pay-result-title">
        Оплата
    </div>

    <div class="section-pay-result-row">
        Заказ стоимостью <strong>{{sum}} р.</strong> будет оплачен через систему <strong>EgoPay</strong>.
    </div>

    <div class="section-pay-result-row">
        <form action="{{action}}" method="post">
            <input name="ready_to_pay" value="1" type="hidden" />
            <input name="BuyButton" value="Оплатить" type="submit" />
        </form>
    </div>
</div>