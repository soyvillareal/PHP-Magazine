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
	var x = $('#btn-pchange'),
        w = $('#text-validate'),
        u = $('#current-password'),
        v = $('#password'),
        h = $('#re-password'),
        s = h.siblings('.text-error'),
        o = u.focus().siblings('.text-error'),
        y = function(e){
            x.attr('disabled', e)
        },
        r = function(e){
            e.addClass('border-red boxshadow-red');
        };
    $('#show-symbols').click(function(){
        var b = $(this),
            g = $('#supported-symbols'),
        	c = JSON.parse(b.attr('aria-expanded'));
        !c ? g.css('height', g.children().first().height()) : g.removeAttr('style'), b.attr('aria-expanded', !c);
    }), x.click(function(){
        x.addClass('spinner-is-loading'), y(!0), $.post(paper_sc.url.change_password+"?token="+paper_sc.token, {user_id: paper_sc.user_id, current_password: u.val(), password: v.val(), re_password: h.val()}, function(e){
            console.log(e)
            if(e.S == 200){
                window.location.href = e.UR
            } else {
                if(e.EX == 1){
                    o.text(e.E), r(u)
                } else {
                    s.text(e.E), r(v), r(h)
                }
                x.removeClass('spinner-is-loading'), y(!1)
            }
        })
    }), $('.item_input').on('keyup, input', function(){
        var f = $(this),
            m = f.prop('id'),
            p = h.val(),
            g = f.val(),
            d = $('#valid-password').children(),
            l = function(){
               	$(d.splice(0, 2)).addClass('border-orange'), w.text(paper_sc.word.password_security_half);
            },
            j = function(e, t){
               	if(e){
                  	t.removeClass('color-grey').addClass('color-green')
               	} else {
                  	t.removeClass('color-green').addClass('color-grey')
               }
            },
            n = function(){
               	d.removeClass('border-red border-orange').removeClass('border-green')
            },
	        x = function(e){
	            e.removeClass('border-red boxshadow-red');
	        },
            q = g.length,
            k = q >= 8;
        if(m == 'current-password'){
            o.text(''), x(u)
        } else if(m == 'password'){
            s.text(''), y(!0), x(h.val('').removeClass('active')), n();
            if (g.match(/^(?=.{12,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)) {
                d.addClass('border-green'), w.text(paper_sc.word.password_security_strong)
            } else if(g.match(/^(?=.{8,})(?=.*[a-z])(?=.*[0-9])(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)){
                $(d.splice(0, 3)).addClass('border-green'), w.text(paper_sc.word.password_security_good);
            } else if(g.match(/^(?=.{8,})(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)){
                l()
            } else if (g.match(/^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$/)) {
                l()
            } else {
                $(d[0]).addClass('border-red'), w.text(paper_sc.word.password_security_weak);
            }
            j(k, $('#icon-five')), j(g.match(/[¡!"#$%&'()*+,-./:;<=>¿?@[\]^_`{|}~]/), $('#icon-supported')), g.length == 0 && (n(), w.text(paper_sc.word.write_a_password));
        } else if(m == 're-password'){
            x(v), (p.length > 0 && v.val() != p) ? (s.text('*'+paper_sc.word.passwords_not_match), r(h), y(!0)) : (s.text(''), x(h), p.length > 0 ? y(!1) : y(!0));
        }
        q > 0 ? f.addClass('active') : f.removeClass('active');
    });
})(paper_sc)