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
   var n = $(window),
       m = paper_h.post_ids;
   m.length > 0 && (function(){
      var z = $('#content-mlload'),
          c = $('#content-left-posts'),
          b = $('#content-slider'),
          a = c.parent(),
          f = !0;
      a.scroll(function(){
         var t = a.scrollTop();
         if(f && (t + a.height()) >= (c.height() - 100)){
            f = !1, z.removeClass('hidden'), $.post(paper_h.url.load_main_left+"?token="+paper_h.token, {post_ids: JSON.stringify(m)}, function(e){
               if(e.S == 200){
                  c.append(e.HT), paper_h.post_ids = e.IDS, z.addClass('hidden'), f = !0;
               } else z.addClass('hidden');
            })
         }
      })
   })(), m.length > 0 && (function(){
      var k = !0,
          b = !0,
          g = $('#global-content'),
          x = $('#content-mposts'),
          u = $('#content-vposts'),
          f = $('#content-lhload'),
          h = $('#content-vhload'),
          a = $('.btn_bvideo[data-type="prev"]'),
          d = u.parent(),
          v = u.children(),
          j = function(e, i){
            e.attr('disabled', i)
          },
          t = function(){
            v.each(function(e, i){
              var y = $(i).width();
               if(s < (d.width() - (y + 31))){
                  s += y + 31;
               }
            })
            return s;
          },
          o = !0,
          l = !0,
          s = 0,
          q = 0,
          w = 0;
      $(document).scroll(function(){
         (n.scrollTop() + n.height()) > g.height() && k && (k = !1, f.removeClass('hidden'), $.post(paper_h.url.load_more+"?token="+paper_h.token, {'post_ids': JSON.stringify(m)}, function(e) {
            console.log(e);
            if(e.S == 200){
               x.append(e.HT), paper_h.post_ids = e.IDS, k = !0, f.addClass('hidden')
            } else f.addClass('hidden');
         }));
      }), d.scroll(function(){
         var o = d.scrollLeft(),
             x = paper_h.dir == 'rtl' ? (o*-1) : o;
         (x + n.width()) > u.width() && b && (b = !1, h.removeClass('hidden'), $.post(paper_h.url.load_videos+"?token="+paper_h.token, {'post_ids': JSON.stringify(m)}, function(e) {
            if(e.S == 200){
               u.append(e.HT), paper_h.post_ids = e.IDS, b = !0, h.addClass('hidden')
            } else h.addClass('hidden');
         }));
      }), n.resize(function(){
         l && (o = l = !1, d.animate({scrollLeft: 0}, 750, function(){
            s = q = w = 0, o = l = !0, j(a, !0)
         }))
      }), $('.btn_bvideo').click(function(){
         if(o){
            var p = $('.btn_bvideo[data-type="next"]'),
                r = ((d.width() - ($(v[0]).width() + 31)) <= 0),
                y = $(v[0]).width() + 31;
            if($(this).attr('data-type') == 'next'){
               j(a, !1), o = !1, r ? (w < v.length ? w += 1 : v.length, q += y) : q += t(), d.animate({scrollLeft: (paper_h.dir == 'rtl' ? -q : q)}, 750, function(){
                  var k = d.scrollLeft();
                  o = !0, ((paper_h.dir == 'rtl' ? -k : k) + d.width()) == u.width() && j(p, !0)
               })
            } else {
               j(p, !1), (q-s) >= 0 && (o = !1, r ? (w > 0 ? w -= 1 : 0, q -= y) : q -= t(), d.animate({scrollLeft: (paper_h.dir == 'rtl' ? -q : q)}, 750, function(){
                  o = !0
               }), q == 0 && j(a, !0))
            }
         }
      })
   })();
})(paper_h);