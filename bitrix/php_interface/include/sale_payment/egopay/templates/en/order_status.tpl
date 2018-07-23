<p>Заказ принят в обработку (текущий статус: {{order_status}}), вы можете дождаться результата проверки платежа или перейти к <a href="{{detail_url}}">подробностям заказа</a>.</p>
<script type="text/javascript">
    setTimeout(
        function() {
            window.location = "{{redirect_url}}";
        },
        15 * 1000
    );
</script>
