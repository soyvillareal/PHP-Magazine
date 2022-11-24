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
	var x = $('#btn-login'),
        h = $('#username').focus(),
        v = $('#password'),
        q = $('.disable_before'),
        f = v.siblings('.text-error'),
        y = function(e, i){
            e.attr('disabled', i)
        },
        w = function(e){
            return e.addClass('border-red boxshadow-red');
        },
        p = function(e){
            e.removeClass('border-red boxshadow-red');
        },
        c = function(e){
            y(e.removeClass('spinner-is-loading'), !1);
        },
        a = 0,
        d = function(e, i = ''){
        	e.text(i).removeClass('color-green').addClass('color-red')
        },
        g = function(o, s, t = ''){
            $.post(paper_al.url.login+"?token="+paper_al.token, {username: o, password: s, recaptcha: t, save_session: $('#staylogged-checkbox:checked').val(), return_url: paper_al.return_url, return_param: paper_al.return_param}, function(e){
                console.log(e)
                if(e.S == 200){
                    window.location = e.UR
                } else {
                    if(e.S == 401){
                        f.html(e.E), w(h), w(v), y(q, !1), c(x), e.EE == 'pending' && $('#btn-rcode').click(function(){
                            var r = $(this);
                            y(r.text(paper_al.word.please_wait), !0), $.post(paper_al.url.resend_code+"?token="+paper_al.token, {tokenu: e.TK, type: 'verify_email'}, function(e){
                                if(e.S == 200){
                                    f.text(e.M).removeClass('color-red').addClass('color-green'), p(h), p(v), a = setTimeout(function(){
                                        y(r, !1), d(f)
                                    }, 3000)
                                } else {
                                    y(r, !1), d(f, e.E)
                                }
                            })
                        })
                    } else {
                        d(f, e.E), w(h), w(v), y(q, !1), c(x)
                    }
                }
            })
        };

    x.click(function(e){
        var o = h.val(),
            s = v.val(),
            t = o.length == 0,
            u = s.length == 0;
        if(t || u){
        	e.preventDefault(), w((t ? h : v)).siblings('.text-error').text('*'+paper_al.word.this_field_is_empty);
        } else if(!u){
            y(q, !0), x.addClass('spinner-is-loading'), f.html(''), p(h), p(v), clearTimeout(a);
            if(paper_al.recaptcha == 'on'){
                grecaptcha.ready(function() {
                    grecaptcha.execute(paper_al.reCAPTCHA_site_key, {action: 'login'}).then(function(e) {
                        g(o, s, e)
                    });
                });
            } else {
                g(o, s)
            }
        }
    }), $('.item_input').on('keyup, input', function(){
        var o = $(this);
        h.val() != '' && v.val() != '' ? y(x, !1) : y(x, !0), p(h), p(v), f.text(''), o.val().length > 0 ? o.addClass('active') : o.removeClass('active');
    });
})(paper_al);