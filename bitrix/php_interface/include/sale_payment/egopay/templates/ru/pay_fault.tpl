<div class="section section-pay-result">
    <div class="section-pay-result-title">
        Произошла ошибка
    </div>

    <div class="section-pay-result-row">
        Произошла ошибка при оплате, пожалуйства, попробуйте снова.
    </div>

    <div class="section-pay-result-row">
        <form action="{{form_url}}" method="post">
            <input name="ready_to_pay" value="1" type="hidden" />
            <input name="BuyButton" value="Оплатить" type="submit" />
        </form>
    </div>

    <div class="section-pay-result-row">
        {{e}}
    </div>
</div>