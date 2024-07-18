<script type="text/javascript">
    var frm = $('#contactForm1');

    frm.submit(function(e) {

        e.preventDefault();

        $.ajax({
            type: frm.attr('method'),
            url: frm.attr('action'),
            data: frm.serialize(),
            success: function(data) {
                console.log('Submission was successful.');
            },
            complete: function() {
                $("#message").focus().val('');
                $('#shoutboxstaff').load('<?php echo URLROOT; ?>/adminshoutbox/loadchat');
            },
            error: function(data) {
                console.log('An error occurred.');
            },
        });
    });
</script>

<script>
function updatestaffShouts(){
            // Assuming we have #shoutbox
            $('#shoutboxstaff').load('<?php echo URLROOT; ?>/adminshoutbox/loadchat');
        }
        setInterval( "updatestaffShouts()", 300000 );
		updatestaffShouts();
</script>