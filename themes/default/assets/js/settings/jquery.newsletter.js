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
	var w = $('#content-newsletter'),
        f = $('#personalize-newsletter'),
        y = $('#content-error'),
        j = w.find('.item_place'),
        g = $('#unsubscribe-popup'),
        h = g.find('.text-error'),
        k = $('#btn-unsubscribe'),
        r = $('#other'),
        u = function(e, x, i){
            e.removeClass('spinner-is-loading'), l(x, i)
        },
        x = function(e){
            e.addClass('spinner-is-loading'), l(e, !0)
        },
        l = function(e, i){
            e.attr('disabled', i)
        };
    paper_sn.newsletter_exists && (function(){
    	var v = $('#btn-popup'),
        	z = $('#reason');
	    v.click(function(){
	        g.removeClass('hidden'), l(v, !0), l(j.slice(0, -4), !0), v.attr('aria-expanded', !0), v.blur();
	    }), $('#btn-cancel').click(function(e){
	        g.addClass('hidden'), l(v, !1), l(j.slice(0, -4), !1), v.attr('aria-expanded', !1);
	    }), k.click(function(){
	        x(k), $.post(paper_sn.url.unsubscribe+"?token="+paper_sn.token, {slug: paper_sn.slug, reason: (z.val() == 'other' ? r.val() : z.val())}, function(e){
	            if(e.S == 200){
	            	u(k, j, !0), y.removeClass('border-red color-red').addClass('border-green color-green'), setTimeout(function(){
		                g.addClass('hidden'), y.text(e.M)
		            }, 300), setTimeout(function(){
		                window.location = paper_sn.loggedin ? paper_sn.url.settings : paper_sn.url.newsletter;
		            }, 3300)
	            } else {
	            	h.text(e.E), u(k, k, !0)
	            }
	        })
	    })
	})(), (function(){
		var m = $('#btn-newsletter'),
			a = $('#newsletter-email'),
	        b = a.siblings('.text-error'),
	        d = $('#newsletter-category'),
        	t = w.find('.text_all'),
        	c = w.find('.text_none'),
	        n = function(){
	            f.css('height', f.children().first().height())
	        },
	        i = function(e){
	        	e.removeClass('border-red boxshadow-red')
	        },
	        g = function(){
	        	t.toggleClass('hidden'), c.toggleClass('hidden'), s = !s
	        },
        	s = paper_sn.count_exists;
        (function(){
        	var q = d.children(),
	        	p = $('#text-rcount'),
	        	v = p.parent(),
        		y = r.parent();
		    w.find('.select_all').click(function(){
		        q.prop('selected', s), g(), l(m, !1), $(this).blur();
		    }), j.on('keyup, change, input', function(){
		        var z = $(this),
		            u = z.attr('id'),
		            o = z.val(),
		            g = o.length;
					
		        if(u == 'newsletter-email'){
		            g > 0 ? (i(z.addClass('active')), b.text(''), l(m, !1)) : z.removeClass('active')
		        } else if(u == 'other'){
					var w = paper_sn.settings.max_words_unsub_newsletter;

		            g > 0 ? (i(z.addClass('active')), h.text(''), l(k, !1)) : z.removeClass('active'), (g > (w*.8)) ? (v.addClass('color-red').removeClass('color-green color-orange')) : ((g > (w*.5)) ? (v.addClass('color-orange').removeClass('color-red color-green')) : (v.addClass('color-green').removeClass('color-red color-orange'))), p.text(g), z.val(o.slice(0, (w-1)))
		        } else if(u == 'reason'){
		            o == 'other' ? (y.removeClass('hidden'), v.removeClass('hidden')) : (y.addClass('hidden'), v.addClass('hidden'))
		        } else if(u == 'newsletter-type'){
		            o == 'personalized' ? n() : f.removeAttr('style');
		        } else if(u == 'newsletter-category'){
		            q.length == g ? (t.addClass('hidden'), c.removeClass('hidden'), s = !1) : (t.removeClass('hidden'), c.addClass('hidden'), s = !0);
		        }
		        (g > 0 || u == 'newsletter-category') && ((paper_sn.newsletter_exists && u != 'reason' && u != 'other') || (!paper_sn.newsletter_exists && a.val() != '')) && l(m, !1)
		    });
		})(), (function(){
	    	var r = $('#newsletter-type'),
	        	k = $('#newsletter-popular'),
		        o = function(e){
		            e.removeClass('border-green color-green').addClass('border-red color-red');
		        },
		        q = function(e){
		        	u(m, j, !0), y.removeClass('border-red color-red').addClass('border-green color-green'), setTimeout(function(){
			            y.text(e)
			        }, 300)
		        },
		        p = function(){
		        	f.removeAttr('style'), w.trigger("reset")
		        };
		    m.click(function(){
		        var z = r.val();
		        x(m), b.text(''), i(a), paper_sn.newsletter_exists ? $.post(paper_sn.url.update+"?token="+paper_sn.token, {slug: paper_sn.slug, type: z, frequency: $('#newsletter-frequency').val(), popular: k.is(':checked'), cats: JSON.stringify(d.val())}, function(e){
		            if(e.S == 200){
		            	q(e.M), setTimeout(function(){
			                y.text(''), o(y), z == 'all' && (p(), r.val('all'), k.prop('checked', !1)), l(j, !1), l(m, !1)
			            }, 3300)
		            } else {
		            	o(y), y.text(e.E), m.removeClass('spinner-is-loading')
		            }
		        }) : $.post(paper_sn.url.subscribe+"?token="+paper_sn.token, {email: a.val(), type: z, frequency: $('#newsletter-frequency').val(), popular: k.is(':checked'), cats: JSON.stringify(d.val())}, function(e){
			        if(e.S == 200){
			        	q(e.M), setTimeout(function(){
				            a.removeClass('active'), y.text('').addClass('border-red color-red').removeClass('border-green color-green'), p(), g(), l(j, !1)
				        }, 3300)
			        } else {
			        	b.text(e.E), a.addClass('border-red boxshadow-red'), m.removeClass('spinner-is-loading')
			        }
			    });
		    });
		})(), paper_sn.frequency != 'all' && setTimeout(n, 500)
	})();
})();