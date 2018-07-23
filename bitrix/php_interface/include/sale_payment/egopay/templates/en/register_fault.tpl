<p>В процессе оплаты возникла ошибка: {{error_code}} {{error_string}}</p>
<p>Вы можете попробовать оплатить снова. В случае, если ошибка повторится, обратитесь к службе поддержки магазина.</p>
<form action="{{form_url}}" method="post">
    <input name="ready_to_pay" value="1" type="hidden" />
    <input name="BuyButton" value="Pay" type="submit" />
</form>