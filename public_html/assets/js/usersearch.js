<script type="text/javascript">
    $(document).ready(function() {
        $("#search-box").keyup(function() {
            $.ajax({
                type: "POST",
                url: "<?php echo CONFIG['URLROOT']; ?>/message/findUser",
                data: 'keyword=' + $(this).val(),
                beforeSend: function() {
                    $("#search-box").css("background", "#FFF url(<?php echo CONFIG['URLROOT']; ?>/LoaderIcon.gif) no-repeat 165px");
                },
                success: function(data) {
                    $("#suggesstion-box").show();
                    $("#suggesstion-box").html(data);
                    $("#search-box").css("background", "#FFF");
                }
            });
        });
    });

    function userCountry(val) {
        $("#search-box").val(val);
        $("#suggesstion-box").hide();
    }
</script>