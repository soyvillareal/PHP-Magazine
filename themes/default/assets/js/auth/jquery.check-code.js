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
	var k = $('.input_key'),
        l = $('.number'),
        m = $('#one'),
        n = $('#two'),
        o = $('#three'),
        p = $('#four'),
        q = $('#five'),
        w = $('#six'),
        x = $('#btn-check'),
        j = $('#btn-rcode'),
        g = m.parent().siblings('.text-error'),
        r = function(){
            var c = m.val() != '' && n.val() != '' && o.val() != '' && p.val() != '' && q.val() != '' && w.val() != '',
            	a = paper_ac.page != 'change-email',
            	d = m.val()+n.val()+o.val()+p.val()+q.val()+w.val(),
            	z = paper_ac.url.sverify_code,
            	b = {code: d};
            k.each(function(){
               var h = $(this);
               h.val() == '' ? (f(h), g.text('*'+paper_ac.word.this_field_is_empty)) : (h.removeClass('border-red'), h.removeClass('boxshadow-red'), c && g.text(''));
            });
            if(!c) return false;
            y(x.addClass('spinner-is-loading'), !0), a && (z = paper_ac.url.tverify_code, b = {code: d, return_url: paper_ac.return_url, type: paper_ac.type}), $.post(z+"?token="+paper_ac.token, b, function(e){
                if(e.S == 200){
                    window.location = (a ? e.UR : paper_ac.url.r_settings)
                } else {
                    x.removeClass('spinner-is-loading'), y(x, !1), f(k), g.text(e.E)
                }
            })
        },
        y = function(e, i){
            e.attr('disabled', i)
        },
        f = function(e){
            e.addClass('border-red boxshadow-red')
        },
        v = paper_ac.tokenu,
        t = 0;

    paper_ac.descode ? r() : m.focus(), x.click(r), j.click(function(){
    	var z = paper_ac.url.sresend_code;
        y(j.text(paper_ac.word.please_wait), !0), clearTimeout(t), paper_ac.page != 'change-email' && (z = paper_ac.url.tresend_code), $.post(z+"?token="+paper_ac.token, {tokenu: v, type: paper_ac.type}, function(e){
            if(e.S == 200){
                v = e.TK, window.history.pushState({state:'new'}, '', paper_ac.url_check+'/'+v+paper_ac.return_param), j.addClass('color-green').removeClass('color-black').text(e.M), t = setTimeout(function(){
                    y(j.addClass('color-black').removeClass('color-green').text(paper_ac.word.resend_code), !1);
                }, 5000)
            } else {
                j.addClass('color-red').removeClass('color-black').text(e.E), t = setTimeout(function(){
                    y(j.addClass('color-black').removeClass('color-red').text(paper_ac.word.resend_code), !1);
                }, 5000)
            }
        })
    }), l.on('input', function(){
        $(this).val() == '' ? y(x, !0) : $(this).val($(this).val().replace(/[^0-9]/gi, ''));
    }).on('paste', function(e){
        var u = e.originalEvent.clipboardData.getData('text');
        if(u && !u.match(/[^0-9]/)) {
            if(u.length == 1){
                $(this).val(u);
            } else {
                l.each(function(i){
                    $(this).val(u.slice(i,(i+1)));
                }), setTimeout(function(){
                    (u.length < l.length ? $(k[u.length]) : k.last()).focus(), r()
                })
            }
        }
    }), k.on('input', function(e){
        m.val() != '' && n.val() != '' && o.val() != '' && p.val() != '' && q.val() != '' && w.val() != '' && y(x, !1), m.val() == '' && n.val() == '' && o.val() == '' && p.val() == '' && q.val() == '' && w.val() == '' && (k.removeClass('border-red boxshadow-red'), g.text('')), $(this).val().length > 1 && $(this).val($(this).val().slice(0, 1))
    }).keyup(function(e){
        var s = $(this),
            z = k.index(s),
            d = $(k[z-1]),
            t = e.which || e.keyCode,
            a = {48: 0, 49: 1, 50: 2, 51: 3, 52: 4, 53: 5, 54: 6, 55: 7, 56: 8, 57: 9},
            b = {96: 0, 97: 1, 98: 2, 99: 3, 100: 4, 101: 5, 102: 6, 103: 7, 104: 8, 105: 9},
            c = function(){
               	setTimeout(function(){
                  	$(k[z+1]).focus();
               	})
            };
        ([13, 8, 37, 38, 39, 40].indexOf(t) == -1 && ((t > 47 && t < 58) || (t > 95 && t < 106)) && !((e.ctrlKey || e.metaKey) && t == 86))
        ?
        (s.val().length > 0 && ((t > 47 && t < 58) ? s.val(a[t]) : s.val(b[t])), c())
        : ((t == 8) 
            ? 
            ((m.val() == '' || n.val() == '' || o.val() == '' || p.val() == '' || q.val() == '' || w.val() == '') && y(x, !0), setTimeout(function(){
               	$(k[z]).val(''), d.focus();
            }))
            :
            ((t == 37 || t == 40)
               	?
               	setTimeout(function(){
                  	d.focus();
               	})
               	:
               	(t == 39 || t == 38) && c()))
    });
})(paper_ac);