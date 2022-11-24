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
	var h = $('#content-fcontact'),
		a = $('#name'),
		c = $('#email'),
		d = $('#subject'),
		b = $('#text'),
		g = $('#query'),
		l = $('.text-error'),
		m = $('#btn-csend'),
		f = function(e, i){
      e.attr('disabled', i);
    },
    k = function(e = '', x = !1, y = !1){
      var v = 'background-'+(y ? 'green' : 'red');
      clearTimeout(n), $('#content-lnotify').removeClass('background-green background-red').addClass(v).css((paper_co.dir == 'rtl' ? 'right' : 'left'), (x ? 20 : '')).find('p').text(e), n = setTimeout(function(){
        k()
      }, 5000);
    },
    q = function(v, t = ''){
    	$.post(paper_co.url.contact+"?token="+paper_co.token, {name: a.val(), subject: d.val(), text: b.val(), email: c.val(), query: g.val(), recaptcha: t}, function(e){
      		if(e.S == 200) {
	        	b.parent().removeAttr('style'), h.trigger('reset'), v.removeClass('spinner-is-loading'), k(e.M, !0, !0)
	      	} else {
				f(v.removeClass('spinner-is-loading'), !1)
				if(e.EL != undefined){
				$.each(e.EL, function(i, u){
					$('#'+u).siblings('.text-error').text(e.TX)
					})
				} else {
				k(e.E, !0)
				}
	      	}
      	})
    },
    n = 0;

	$('#btn-dnotify').click(k), d.change(function(){
		var v = b.parent();
		$(this).val() == 'other' ? v.css('height', 106) : v.removeAttr('style')
	}), $('.item_input').on('keyup, change, input', function(){
		f(m, a.val() != '' && d.val() != '' && c.val() != '' && g.val() != '' ? !1 : !0)
	}), m.click(function(){
    var v = $(this);
    f(v.addClass('spinner-is-loading'), !0), l.text('');
    if(paper_co.recaptcha == 'on'){
	    grecaptcha.ready(function() {
	      grecaptcha.execute(paper_co.reCAPTCHA_site_key, {action: 'contact'}).then(function(e) {
	        q(v, e)
	      });
	    });
	  } else {
	    q(v)
	  }
  })
})(paper_co);