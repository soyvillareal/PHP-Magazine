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
    $.fn.InitCarousel = function(t) {
        var a = this.find('.content-carrusel'),
            s = t.entry_cimages,
            j = function(e, i){
                var g = a.index(e);
                e.find('.entry_cimage').css('opacity', 0).attr('src', s[g][i]['image']).attr('alt', s[g][i]['caption']).animate({'opacity': 1}, 300), e.find('figcaption span').text(s[g][i]['caption']), e.find('div[role=group]').attr('aria-label', (i+1)+' '+t.word.of+' '+t.max_cimages[g]), e.find('div[role=progressbar]').attr('aria-valuenow', (i+1))
            },
            m = function(e, i){
                j(e, i), e.find('.btn_crepos.selected').removeClass('selected'), $(e.find('.btn_crepos')[i]).addClass('selected')
            },
            l = function(e, i){
                clearInterval(n[i]), e.blur().attr('data-type', 'play').find('svg').html('<title>'+t.word.play+'</title><path fill="currentColor" fill-rule="evenodd" d="M18 12.001c0 0.359 -0.366 0.617 -0.366 0.617l-10.273 6.364C6.612 19.472 6 19.109 6 18.179V5.822c0 -0.932 0.612 -1.294 1.362 -0.804l10.273 6.366c-0.001 0 0.365 0.258 0.365 0.617z"/>')
            },
            h = [],
            n = [];

        a.each(function(){
            h.push(0), n.push(0)
        }), a.find('.btn_cchange').click(function(){
            var p = $(this).blur(),
                x = p.parents('.content-carrusel'),
                d = p.attr('data-type'),
                g = a.index(x),
                o = s[g].length-1,
                v = h[g];
            d == 'back' && v > 0 ? v-- : d == 'next' && v < o && v++, x.find('.btn_crepos').index(x.find('.btn_crepos.selected')) != v && (l(x.find('.btn_cauto'), g), j(x, v), m(x, v)), h[g] = v
        }), a.find('.btn_cauto').click(function(){
            var p = $(this),
                x = p.parents('.content-carrusel'),
                g = a.index(x),
                v = h[g];
            if(p.attr('data-type') == 'play'){
                p.blur().attr('data-type', 'pause').find('svg').html('<title>'+t.word.pause+'</title><path fill="currentColor" fill-rule="evenodd" d="M18 3.6h-2.4c-0.664 0 -1.2 0.058 -1.2 0.72v15.36c0 0.662 0.536 0.72 1.2 0.72h2.4c0.664 0 1.2 -0.058 1.2 -0.72V4.32c0 -0.662 -0.536 -0.72 -1.2 -0.72zM8.4 3.6H6c-0.664 0 -1.2 0.058 -1.2 0.72v15.36c0 0.662 0.536 0.72 1.2 0.72h2.4c0.664 0 1.2 -0.058 1.2 -0.72V4.32c0 -0.662 -0.536 -0.72 -1.2 -0.72z"/>'), n[g] = setInterval(function(){
                    v < (s[g].length-1) ? v++ : (v = 0, l(p, g)), j(x, v), m(x, v), h[g] = v
                }, 3000);
            } else l(p, g);
        }), $('.btn_crepos').click(function(){
            var p = $(this),
                x = p.parents('.content-carrusel'),
                v = x.find('.btn_crepos').index(p);
            j(x, v), m(x, v), h[a.index(x)] = v
        });
    }
}(jQuery);