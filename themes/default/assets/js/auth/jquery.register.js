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
	var x = $('#btn-register'),
        q = $('#username').focus(),
        s = $('#email'),
        v = $('#password'),
        n = $('#re-password'),
        o = $('#accept-checkbox'),
        b = $('.item_input'),
        m = s.siblings('.text-error'),
        c = n.siblings('.text-error'),
        y = function(e){
            o.is(':checked') && f(x, e)
        },
        p = function(e){
            e.addClass('border-red boxshadow-red');
        },
        f = function(e, i){
            e.attr('disabled', i)
        },
        h = function(e){
        	f(e.removeClass('spinner-is-loading'), !1)
        },
        r = !0,
        i = !1;
    (function(){
    	var d = $('#fields-one'),
    		k = $('#container-signup'),
    		l = $('#fields-two'),
    		u = function(g, w, t, y = ''){
    			i && $.post(paper_are.url.register+"?token="+paper_are.token, {username: g, email: w, password: t, re_password: n.val(), recaptcha: y, accept_checkbox: $('#accept-checkbox:checked').val(), return_url: paper_are.return_url, return_param: paper_are.return_param}, function(e){
		            if(e.S == 200){
		            	window.location = e.UR
		            } else {
		            	c.text(e.E), p(b), h(x)
		            }
		        })
    		};
    	o.change(function(){
	        var z = o.is(':checked');
	        r && (f(x, !z), y(!z));
	    }), x.click(function(){
	        var g = q.val(),
	            w = s.val(),
	            t = v.val(),
	            j = function(e){
	            	p(e), e.siblings('.text-error').text('*'+paper_are.word.this_field_is_empty);
	            };
		    if(g.length == 0){
		    	j(q)
		    } else if(r){
		        if(w.match(/^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/)){
		        	if(w.length == 0){
		        		m.text('*'+paper_are.word.this_field_is_empty), p(s)
		        	} else {
		        		k.addClass('has-back-button'), x.attr('type', 'submit').find('span').text(paper_are.word.create_account), d.addClass('hidden'), l.removeClass('hidden'), y(!0), v.focus(), r = !1
		        	}
		        } else {
		       		m.text('*'+paper_are.word.enter_a_valid_email), p(s)
		        }
		    } else if(t.length == 0){
		    	j(v)
		    } else {
		        f(x.addClass('spinner-is-loading'), !0);
		        if(paper_are.recaptcha == 'on'){
	                grecaptcha.ready(function() {
	                    grecaptcha.execute(paper_are.reCAPTCHA_site_key, {action: 'register'}).then(function(e) {
	                        u(g, w, t, e)
	                    });
	                });
	            } else {
	                u(g, w, t)
	            }
		    }
	    }), $('#btn-back-email').click(function(){
	        k.removeClass('has-back-button'), x.attr('type', 'button').find('span').text(paper_are.word.next), l.addClass('hidden'), d.removeClass('hidden'), y(!1), s.focus(), r = !0;
	    })
    })(), (function(){
    	var a = $('#supported-symbols');
    	$('#show-symbols').click(function(){
	        var d = $(this),
	            l = JSON.parse(d.attr('aria-expanded'));
	        !l ? a.css('height', a.children().first().height()) : a.removeAttr('style'), d.attr('aria-expanded', !l);
	    })
    })(), (function(){
    	var w = $('#text-validate'),
	        o = function(e){
	            e.removeClass('border-red boxshadow-red');
	        };

    	b.on('keyup, input', function(){
	        var k = $(this),
	            x = k.attr('id'),
	            a = q.val(),
	            b = n.val(),
	            g = k.val(),
        		z = q.siblings('.text-error'),
	            d = $('#valid-password').children(),
	            l = function(){
	               	$(d.splice(0, 2)).addClass('border-orange'), w.text(paper_are.word.password_security_half);
	            },
	            j = function(e, t){
	               	if(e){
	                  	t.removeClass('color-grey').addClass('color-green')
	               	} else {
	                  	t.removeClass('color-green').addClass('color-grey')
	               	}
	            },
	            u = function(){
	               	d.removeClass('border-red border-orange border-green')
	            },
		        f = function(i, e){
		            var a = i.parent();
		            a.addClass('spinner-is-loading'), $.post(paper_are.url.validate+"?token="+paper_are.token, e, function(e){
		               	if(e.S == 200){
		               		h(a), i.siblings('.text-error').text(e.E), p(i)
		               	} else h(a);
		            })
		        };
	        if(x == 'username'){
	            z.text(''), o(q);
	            if(a.match(/^[a-zA-Z0-9]+$/)){
	               f(q, {username: a}), y(!1);
	            } else if(a.length > 0){
	               z.text('*'+paper_are.word.write_only_numbers_letters), p(q), y(!0)
	            }
	            if(g.length > 16) {
	               k.val(g.slice(0, 16))
	            }
	        } else if(x == 'email'){
	            m.text(''), o(s), f(s, {email: s.val()});
	        } else if(x == 'password'){
	            if(!r){
	               	c.text(''), y(!0), o(n.val('').removeClass('active')), u();
	               	if (g.match(/^(?=.{12,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)) {
	                  	d.addClass('border-green'), w.text(paper_are.word.password_security_strong)
	               	} else if(g.match(/^(?=.{8,})(?=.*[a-z])(?=.*[0-9])(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)){
	                  	$(d.splice(0, 3)).addClass('border-green'), w.text(paper_are.word.password_security_good);
	               	} else if(g.match(/^(?=.{8,})(?=.*[¡!\"#$%&'()*+,-./:;<=>¿?@\]\[^_`{|}~]).*$/)){
	                  	l()
	               	} else if (g.match(/^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$/)) {
	                  	l()
	               	} else {
	                  	$(d[0]).addClass('border-red'), w.text(paper_are.word.password_security_weak);
	               	}
	               	j(g.length >= 8, $('#icon-five')), j(g.match(/[¡!"#$%&'()*+,-./:;<=>¿?@[\]^_`{|}~]/), $('#icon-supported')), g.length == 0 && (u(), w.text(paper_are.word.write_a_password));
	            }
	        } else if(x == 're-password'){
	            if(b.length > 0 && v.val() != b){
	               	c.text('*'+paper_are.word.passwords_not_match), p(n), y(!0), i = !1;
	            } else {
	               	c.text(''), o(n), b.length > 0 ? y(!1) : y(!0), i = !0;
	            }
	        }
	        g.length > 0 ? k.addClass('active') : k.removeClass('active');
	    });
    })();


})(paper_are);