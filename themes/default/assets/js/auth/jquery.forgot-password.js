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
	var x = $('#btn-rpassword'),
        q = $('#email').focus(),
        f = q.siblings('.text-error'),
        y = function(e, i){
            e.attr('disabled', i)
        },
        w = function(e){
            e.addClass('border-red boxshadow-red');
        },
        p = function(e){
            e.removeClass('border-red boxshadow-red');
        },
        c = function(e){
            y(e.removeClass('spinner-is-loading'), !1);
        },
        t = 0,
        u = function(e, i = ''){
        	e.text(i).removeClass('color-green').addClass('color-red')
        },
        g = function(o, t = ''){
        	$.post(paper_af.url.forgot_password+"?token="+paper_af.token, {email: o, recaptcha: t}, function(e){
		        console.log(e)
		        if(e.S == 200){
		            c(x), f.text(e.M).removeClass('color-red').addClass('color-green'), p(q), t = setTimeout(function(){
		                y(x, !1), u(f)
		            }, 3000)
		        } else {
		           	if(e.S == 401){
						f.html(e.E), w(q), c(x), e.EE == 'pending' && $('#btn-rcode').click(function(){
				            var r = $(this);
				            y(r.text(paper_af.word.please_wait), !0), $.post(paper_af.url.resend_code+"?token="+paper_af.token, {tokenu: e.TK, type: 'verify_email'}, function(e){
				                if(e.S == 200){
				                	f.text(e.M).removeClass('color-red').addClass('color-green'), p(q), t = setTimeout(function(){
					                    y(r, !1), u(f)
					                }, 3000)
				               	} else {
				               		y(r, !1), f.text(e.E)
				               	}
				            })
				        })
		           	} else {
		           		u(f, e.E), w(q), c(x)
		            }
		        }
		    })
        };
    x.click(function(e){
        var o = q.val();
        if(o.length == 0){
        	e.preventDefault(), f.text('*'+paper_af.word.this_field_is_empty), w(q)
        } else {
        	if(o.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/)){
        		y(x.addClass('spinner-is-loading'), !0), f.html(''), p(q), clearTimeout(t)
        		if(paper_af.recaptcha == 'on'){
	                grecaptcha.ready(function() {
	                    grecaptcha.execute(paper_af.reCAPTCHA_site_key, {action: 'forgot_password'}).then(function(e) {
	                        g(o, e)
	                    });
	                });
	            } else {
	                g(o)
	            }
        	} else {
        		e.preventDefault(), f.text('*'+paper_af.word.enter_a_valid_email), w(q)
        	}
        }
    }), $('.item_input').on('keyup, input', function(){
        var o = $(this);
        q.val() != '' ? y(x, !1) : y(x, !0), p(q), f.text(''), o.val().length > 0 ? o.addClass('active') : o.removeClass('active');
    });
})(paper_af);