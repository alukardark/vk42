<div class="section section-pay-result">
    <div class="section-pay-result-title">
        Переход к оплате
    </div>

    <div class="section-pay-result-row">
        В скором времени будет осуществлён переход на сайт платёжной системы. 
        Если по какой-либо причине это не произошло, вы можете перейти <a href="{{redirect_url}}?session={{session}}">вручную</a>.
    </div>

    <script type="text/javascript">
        setTimeout(
            function() {
                window.location = "{{redirect_url}}?session={{session}}";
            },
            1 * 1000
        );
    </script>
</div>