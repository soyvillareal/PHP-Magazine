// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

(function(){
	var a = $('#btn-deactivated'),
        b = $('#unlink-popup'),
        c = $('#btn-popup'),
        f = $('.text-error'),
        x = function(e){
            b.addClass('hidden'), c.removeAttr('disabled').attr('aria-expanded', !1);
        },
        q = function(e){
            e.removeClass('spinner-is-loading').removeAttr('disabled')
        };
    c.click(function(){
        b.removeClass('hidden'), c.attr('disabled', !0).attr('aria-expanded', !0).blur();
    }), $('#btn-cancel').click(x), a.click(function(){
        a.addClass('spinner-is-loading').attr('disabled', !0), f.text(''), $.post(paper_au.url.deactivate_account+"?token="+paper_au.token, {tokenu: paper_au.tokenu}, function(e){
            if(e.S == 200){
                $('#unlink-title').text(paper_au.word.the_account_been_deactivated), $('#unlink-pending').addClass('hidden'), $('#unlink-deactivated').removeClass('hidden'), q(a), x()
            } else {
                f.text(e.E), q(a), x()
            }
        })
    })
})(paper_au);