// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

!function($) {
    $.createCarousel = function(t) {
        var h = [],
            n = [];
        var j = function(e, i){
                var a = e.find('.item_caption');
                e.find('.entry_cimage').css('opacity', 0).attr('src', $(e.find('.btn_crepos')[i]).attr('data-image')).animate({'opacity': 1}, 300), a.addClass('hidden'), $(a[i]).removeClass('hidden'), f(e)
            },
            m = function(e, i){
                j(e, i), e.find('.btn_crepos.selected').removeClass('selected'), $(e.find('.btn_crepos')[i]).addClass('selected')
            },
            l = function(e, i){
                clearInterval(n[i]), e.blur().attr('data-type', 'play').find('svg').html('<title>'+t.word.play+'</title><path fill="currentColor" fill-rule="evenodd" d="M18 12.001c0 0.359 -0.366 0.617 -0.366 0.617l-10.273 6.364C6.612 19.472 6 19.109 6 18.179V5.822c0 -0.932 0.612 -1.294 1.362 -0.804l10.273 6.366c-0.001 0 0.365 0.258 0.365 0.617z"/>')
            },
            k = function(e){
                var a = $('.content-carrusel').index(e);
                return {'h': h[a], 'g': a};
            },
            c = function(e){
                return e.find('.content_files').children();
            },
            f = function(e){
                e.find('div[role=group]').attr('aria-label', '1 '+t.word.of+' '+e.find('div[role=progressbar]').attr('aria-valuemax')), e.find('div[role=progressbar]').attr('aria-valuenow', 1)
            },
            q = function() {
              h = [], $('.content-carrusel').each(function(i){
                h.push(0), l($(this).find('.btn_cauto'), i), m($(this), 0)
              }), n = [];
            };
        $('.entry-carousel').map(e => h.push(0)), $(document).on('change', '.carousel_file', function(){
            var x = $(this).parents('.content-thumbnail').prev(),
                g = k(x),
                u = (c(x).length+1);
            u > 1 && x.find('.content-ecbuttons').removeClass('hidden'), h[g['g']] = 0, x.find('div[role=progressbar]').attr('aria-valuemax', u), f(x);
        }).on('click', '.btn_repos', q).on('click', '.btn_adelete', q).on('click', '.btn_cchange', function(){
            var p = $(this).blur(),
                x = p.parents('.content-carrusel'),
                d = p.attr('data-type'),
                o = c(x).length-1,
                g = k(x),
                v = g['h'];
            d == 'back' && v > 0 ? v-- : d == 'next' && v < o && v++, x.find('.btn_crepos').index(x.find('.btn_crepos.selected')) != v && (l(x.find('.btn_cauto'), g['g']), j(x, v), m(x, v)), h[g['g']] = v
        }).on('click', '.btn_cauto', function(){
            var p = $(this),
                x = p.parents('.content-carrusel'),
                s = c(x),
                g = k(x),
                v = g['h'];
            if(p.attr('data-type') == 'play'){
                p.blur().attr('data-type', 'pause').find('svg').html('<title>'+t.word.pause+'</title><path fill="currentColor" fill-rule="evenodd" d="M18 3.6h-2.4c-0.664 0 -1.2 0.058 -1.2 0.72v15.36c0 0.662 0.536 0.72 1.2 0.72h2.4c0.664 0 1.2 -0.058 1.2 -0.72V4.32c0 -0.662 -0.536 -0.72 -1.2 -0.72zM8.4 3.6H6c-0.664 0 -1.2 0.058 -1.2 0.72v15.36c0 0.662 0.536 0.72 1.2 0.72h2.4c0.664 0 1.2 -0.058 1.2 -0.72V4.32c0 -0.662 -0.536 -0.72 -1.2 -0.72z"/>'), n[g['g']] = setInterval(function(){
                    v < (s.length-1) ? v++ : (v = 0, l(p, g['g'])), j(x, v), m(x, v), h[g['g']] = v
                }, 3000);
            } else l(p, g['g']);
        }).on('click', '.btn_crepos', function(){
            var p = $(this),
                x = p.parents('.content-carrusel'),
                v = x.find('.btn_crepos').index(p);
            j(x, v), m(x, v), h[k(x)['g']] = v
        }).on('click', '.btn_cidelete', function(){
            var p = $(this),
                w = $('#btn-post'),
                x = p.parents('.content-carrusel'),
                u = x.find('.btn_crepos.selected'),
                g = k(x),
                o = x.find('.item_caption'),
                v = x.find('.btn_crepos').index(u),
                z = c(x),
                d = (z.length-1);
            l(x.find('.btn_cauto'), g['g']), u.remove();
            $(z[v]).remove(), $(o[v]).remove(), x.find('.entry_cimage').attr('src', x.find('.btn_crepos').first().addClass('selected').attr('data-image')), o.first().removeClass('hidden')
            if(d > 0){
                if(d == 1){
                    x.find('.content-ecbuttons').addClass('hidden')
                }
                h[g['g']] = 0;
            } else {
                h.splice(g['g'], 1), n.splice(g['g'], 1), x.removeClass('border-red boxshadow-red').addClass('hidden').next().removeClass('hidden').siblings('.text-error').text(''), (w.length > 0 ? w : $('.btn_post')).attr('disabled', !0)
            }
        }).on('keyup', '.entry-source .item-input', function(){
            var p = $(this),
                x = p.parents('.entry');
            if(p.val().length > 0){
                x.find('.item_csource').removeClass('hidden').text(p.val()).prev().addClass('hidden')
            } else {
                x.find('.item_csexample').removeClass('hidden').next().addClass('hidden')
            }
        });
    }
}(jQuery);