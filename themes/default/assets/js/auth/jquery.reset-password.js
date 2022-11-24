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
	var a = $('#supported-symbols'),
        x = $('#btn-change'),
        w = $('#text-validate'),
        v = $('#password').focus(),
        o = $('#re-password'),
        r = o.siblings('.text-error'),
        y = function(e){
            x.attr('disabled', e)
        },
        s = function(e){
            e.addClass('border-red boxshadow-red');
        },
        p = function(e){
            e.removeClass('border-red boxshadow-red');
        };
    $('#show-symbols').click(function(){
        var b = $(this),
            c = JSON.parse(b.attr('aria-expanded'));
        !c ? a.css('height', a.children().first().height()) : a.removeAttr('style'), b.attr('aria-expanded', !c);
    }), x.click(function(){
        x.addClass('spinner-is-loading'), y(!0), $.post(paper_ar.url.reset_password+"?token="+paper_ar.token, {password: v.val(), re_password: o.val(), tokenu: paper_ar.tokenu}, function(e){
            if(e.S == 200){
                window.location.href = paper_ar.url.r_login
            } else {
                r.text(e.E), s(v), s(o), x.removeClass('spinner-is-loading'), y(!1)
            }
        })
    }), $('.item_input').on('keyup, input', function(){
        var q = $(this),
            h = q.prop('id'),
            m = o.val(),
            g = q.val(),
            d = $('#valid-password').children(),
            l = function(){
               $(d.splice(0, 2)).addClass('border-orange'), w.text(paper_ar.word.password_security_half);
            },
            j = function(e, t){
               	if(e){
                  	t.removeClass('color-grey').addClass('color-green')
               	} else {
                  	t.removeClass('color-green').addClass('color-grey')
               	}
            },
            n = function(){
               	d.removeClass('border-red border-orange border-green')
            },
            k = g.length >= 8;
        if(h == 'password'){
            r.text(''), y(!0), p(o.val('').removeClass('active')), n();
            if (g.match(/^(?=.{12,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)) {
                d.addClass('border-green'), w.text(paper_ar.word.password_security_strong)
            } else if(g.match(/^(?=.{8,})(?=.*[a-z])(?=.*[0-9])(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)){
                $(d.splice(0, 3)).addClass('border-green'), w.text(paper_ar.word.password_security_good);
            } else if(g.match(/^(?=.{8,})(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)){
                l()
            } else if (g.match(/^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$/)) {
                l()
            } else {
                $(d[0]).addClass('border-red'), w.text(paper_ar.word.password_security_weak);
            }
            j(k, $('#icon-five')), j(g.match(/[¡!"#$%&'()*+,-./:;<=>¿?@[\]^_`{|}~]/), $('#icon-supported')), g.length == 0 && (n(), w.text(paper_ar.word.write_a_password));
        } else if(h == 're-password'){
            p(v), (m.length > 0 && v.val() != m) ? (r.text('*'+paper_ar.word.passwords_not_match), s(o), y(!0)) : (r.text(''), p(o), m.length > 0 ? y(!1) : y(!0));
        }
        g.length > 0 ? q.addClass('active') : q.removeClass('active');
    });
})(paper_ar);