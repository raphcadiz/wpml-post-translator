jQuery(function($) {
    $('#translate-post').on('click', function(e){
        e.preventDefault();
        var target = $('select[name=rcpt_translated_lang]').val();
        var data_url = $(this).attr('href');

        window.location.replace(data_url + '&rcpt-action=translate&rcpt-target=' + target);
    })
})