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
   var t = $(window),
       x = $('body'),
       j = $('#content-sfilter'),
       r = $('#content-sinfo'),
       s = $('#content-load-si'),
       n = $('#content-load-sc'),
       d = $('#key-reset'),
       u = $('#key-search'),
       c = $('#content-sresults'),
       w = function(e, i){
         e.attr('disabled', i);
       },
       g = function(e){
         e.attr('aria-expanded', !JSON.parse(e.attr('aria-expanded'))).blur()
       },
       i = function(e){
         return $('.btn_ddown[data-type='+e+']').siblings().find('.btn_dfilter.selected').attr('data-type')
       },
       y = function(e){
         var z = $('.btn_dfilter'),
             k = $('#btn-search'),
             p = function(e){
               t[0].history.pushState({state:'new'}, '', e[0]), $('#type-date').val(e[1]), $('#type-category').val(e[2]), $('#type-author').val(e[3]), $('#type-sort').val(e[4])
             };

         w(z, !0), u.val().length > 0 ? (w(k, !1), d.removeClass('hidden')) : (w(k, !0), d.addClass('hidden')), r.addClass('hidden'), c.addClass('hidden'), s.removeClass('hidden'), n.removeClass('hidden'), $.post(paper_s.url.normal_search+"?token="+paper_s.token, {keyword: u.val(), date: i('date'), category: i('category'), author: i('author'), sort: i('sort')}, function(e){
            if(e.S == 200){
               p([e.URL, e.DT, e.CT, e.AT, e.ST]), s.addClass('hidden'), e.KW && r.removeClass('hidden').html(e.IO), n.addClass('hidden'), c.html(e.HT).removeClass('hidden'), l = e.IDS, b = !0, w(z, !1);
            } else {
               p([e.URL, e.DT, e.CT, e.AT, e.ST]), c.removeClass('hidden').html(e.HT), r.addClass('hidden'), s.addClass('hidden'), n.addClass('hidden'), w(z, !1)
            }
         })
       },
       l = paper_s.search_ids,
       b = !0;

   (function(){
      var m = $('.btn_ddown'),
          q = function(){
            var v = $('.btn_ddown[aria-expanded=true]'),
                o = v.siblings(),
                z = v.parent();
            v.length > 0 && o.css({'left': ((z.width() - o.width() + (t.width() - x.width())) / 2) + z.position().left})
          },
          a = !0;
      m.click(function(){
         var v = $(this),
             o = v.siblings(),
             p = $('.btn_ddown[aria-expanded=true]'),
             f = m.index(v),
             z = v.parent(),
             k = 0;

         m.parent().each(function(i){
            if(i < f) k += $(this).width();
         }), a = !1, p.length > 0 && (p.siblings().addClass('hidden'), g(p)), $(document).scrollTop(0), x.addClass('overflow-hidden'), g(v), o.removeClass('hidden'), o.css({'left': ((z.width() - o.width() + (t.width() - x.width())) / 2) + z.position().left}), j.width() < t.width() && j.animate({scrollLeft: (f == 0 ? 0 : (f == m.length - 1 ? j.children().width() : k))}, 750, function(){
            a = !0
         });
      }), j.scroll(function(){
         var v = $('.btn_ddown[aria-expanded=true]'),
             o = v.siblings(),
             z = j.width(),
             p = (j.scrollLeft() + (t.width() - 40));
         ((m.index(v) == 0 && p > z || m.index(v) == 2 && p < j.children().width()) && a) && (o.addClass('hidden'), x.removeClass('overflow-hidden')), q()
      }), $('.btn_dfilter').click(function(){
         var p = $(this),
             z = p.parents('.item_dfilter'),
             o = z.prev(),
             v = o.attr('data-type');
         z.find('.btn_dfilter.selected').removeClass('selected'), p.addClass('selected'), y(), o.text((v == 'date' ? paper_s.word.date : (v == 'category' ? paper_s.word.category : (v == 'author' ? paper_s.word.author : paper_s.word.sort_by)))+' ('+$.trim(p.text())+')');
      }), t.resize(q), $(document).click(function(e){
         var v = $('.btn_ddown[aria-expanded=true]');
         $('.item_dfilter, .item_dfilter *, .btn_ddown, .btn_ddown *').index(e.target) == -1 && v.length > 0 && (v.siblings().addClass('hidden'), j.removeClass('pointer-events-none'), g(v), x.removeClass('overflow-hidden'))
      })
   })(), (function(){
      var q = $('#container-sposts');
      d.click(function(){
         u.val(''), r.addClass('hidden'), y()
      }), u.keyup(y), l.length > 0 && $(document).scroll(function(){
         if(b && ($(document).scrollTop() + t.height()) >= q.height()){
            b = !1, n.removeClass('hidden'), $.post(paper_s.url.table_search+"?token="+paper_s.token, {keyword: u.val(), search_ids: JSON.stringify(l), date: i('date'), category: i('category'), author: i('author'), sort: i('sort')}, function(e){
               e.S == 200 ? (c.append(e.HT), l = e.IDS, n.addClass('hidden'), b = !0) : n.addClass('hidden')
            })
         }
      })
   })();
})(paper_s);