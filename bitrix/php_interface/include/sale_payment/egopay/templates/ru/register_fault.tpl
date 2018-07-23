<div class="section section-pay-result">
    <div class="section-pay-result-title">
        Возникла ошибка
    </div>

    <div class="section-pay-result-row">
        В процессе оплаты возникла ошибка: {{error_code}} {{error_string}}
    </div>

    <div class="section-pay-result-row">
        Вы можете попробовать оплатить снова. В случае, если ошибка повторится, обратитесь к службе поддержки магазина.
    </div>

    <div class="section-pay-result-row">
        <form action="{{form_url}}" method="post">
            <input name="ready_to_pay" value="1" type="hidden" />
            <input name="BuyButton" value="Оплатить" type="submit" />
        </form>
    </div>
</div>