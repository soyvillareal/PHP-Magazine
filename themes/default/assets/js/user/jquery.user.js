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
   var w = function(e, i){
         e.attr('disabled', i);
       },
       z = function(e){
         w(e.addClass('spinner-is-loading'), !0)
       },
       r = function(e){
         w(e.removeClass('spinner-is-loading'), !1)
       },
       l = paper_u.profile_ids,
       b = !0;

   (function(){
      var u = $('body'),
          s = $('#alert-ureport'),
          x = s.find('.text-error'),
          y = s.find('.item_rtextarea'),
          q = $('#container-sposts'),
          c = $('#content-sresults'),
          n = $('#content-load-sc'),
          o = $('#item-followers'),
          k = $('#item-acontent'),
          m = $('#item-ralert'),
          a = $('#item-abuttons'),
          h = $('#btn-poptions'),
          j = $('#btn-rsend'),
          p = $('#alert-pblock'),
          g = $('#alert-rtitle'),
          e = $('.item_rinput'),
          f = function(e){
            var e = this == window ? e : $(this);
            e.attr('aria-expanded', !JSON.parse(e.attr('aria-expanded'))).blur()
          },
          d = function(){
           var x = ($(window).height() - 85),
               y = $('.item_rinput:checked ~ .content_description'),
               v = (x - (g.height() + a.height()));
            if(v < (k.children().height() - (y.length > 0 ? y.height() : 0))){
               m.css('height', x), s.css('top', 'calc(50% - '+(x / 2)+'px)'), k.css('height', v)
            } else t();
          },
          t = function(){
            k.removeAttr('style'), m.removeAttr('style'), s.removeAttr('style')
          },
          i = function(){
            s.addClass('hidden'), e.prop('checked', !1), y.val('')
          };
      (function(){
         var u = $('#pfilter'),
             t = function(){
               b = !1, w(u, !0), n.removeClass('hidden')
             },
             r = function(){
               w(u, !1), n.addClass('hidden')
             },
             o = function(){
               c.removeClass('hidden'), r(), b = !0
             };

         l.length > 0 && $(document).scroll(function(){
            if(b && ($(document).scrollTop() + $(window).height()) >= q.height()){
               t(), $.post(paper_u.url.load_posts+"?token="+paper_u.token, {user_id: paper_u.user_id, filter_by: u.val(), profile_ids: JSON.stringify(l)}, function(e){
                  if(e.S == 200) {
                     c.append(e.HT), l = e.IDS, r(), b = !0
                  } else r();
               })
            }
         }), u.change(function(e){
            c.addClass('hidden'), t(), $.post(paper_u.url.filter+"?token="+paper_u.token, {user_id: paper_u.user_id, filter_by: u.val()}, function(e){
               console.log(e);
               if(e.S == 200) {
                  c.html(e.HT), l = e.IDS, o()
               } else o();
            });
         });
      })(), $(document).click(function(e){
         !$(e.target).is('#container-poptions, #container-poptions *') && h.attr('aria-expanded') == 'true' && f(h)
      }), paper_u.settings.nodejs == 'on' && SOCKET.on('setOutfollow', function(e){
         o.text(e.TX)
      }), h.click(f), $('#btn-follow').click(function(){
         var v = $(this),
             p = function(e){
               console.log(e);
               if(e.S == 200) {
                  o.text(e.TX), v.attr('type', e.T).attr('aria-label', e.L), r(v)
               } else r(v);
             },
             w = {user_id: paper_u.user_id};

         z(v);
         if(paper_u.settings.nodejs == 'off'){
            $.post(paper_u.url.follow+"?token="+paper_u.token, w, p)
         } else {
            SOCKET.emit('setInfollow', w, p)
         }
      }), $('#btn-pbalert').click(function(){
         p.removeClass('hidden'), u.addClass('overflow-hidden'), f(h)
      }), $('#btn-pbcancel').click(function(){
         p.addClass('hidden'), u.removeClass('overflow-hidden')
      }), $('#btn-pblock, #btn-punlock').click(function(){
         var v = $(this);
         z(v), $.post(paper_u.url.block+"?token="+paper_u.token, {profile_id: paper_u.user_id}, function(e){
            if(e.S == 200){
               window.location.reload();
            } else r(v)
         })
      }), $('#btn-preport').click(function(){
         u.addClass('overflow-hidden'), s.removeClass('hidden'), t(), d()
      }), $('#btn-rcancel').click(function(){
         i(), t(), s.addClass('hidden'), u.removeClass('overflow-hidden'), w(j, !0), x.text('')
      }), e.change(function(){
         $('.item_rinput:checked') && w(j, !1), x.text(''), y.val('').next().find('.text_count').text(0), d()
      }), y.on('keyup, input', function(){
         var x = $(this),
             y = x.parent(),
             u = y.find('.text_count'),
             o = u.parent(),
             d = x.val(),
             v = d.length,
             q = paper_u.settings.max_words_report;

         (v > (q*.8)) ? (o.addClass('color-red').removeClass('color-green color-orange')) : ((v > (q*.5)) ? (o.addClass('color-orange').removeClass('color-red color-green')) : (o.addClass('color-green').removeClass('color-red color-orange'))), u.text(v), x.val(d.slice(0, (q-1)))
      }), $(window).resize(d), (function(){
         var m = $('#alert-spalert');
         $('#btn-suclose').click(function(){
            m.addClass('hidden'), u.removeClass('overflow-hidden')
         }), j.click(function(){
            var h = $(this),
                y = $('.item_rinput:checked'),
                u = y.parent(),
                g = u.find('.item_rtextarea'),
                t = y.val(),
                o = {reported_id: paper_u.user_id, type: t, place: 'user'};
            g && (o = {reported_id: paper_u.user_id, type: t, place: 'user', description: g.val()}), x.text(''), w(e, !0), z(h), $.post(paper_u.url.report+"?token="+paper_u.token, o, function(c){
               if(c.S == 200){
                  i(), m.removeClass('hidden'), h.removeClass('spinner-is-loading'), w(e, !1);
               } else {
                  x.text(c.E), r(h.removeClass('spinner-is-loading'), !1), w(e, !1), d()
               }
            });
         });
      })();

   })();
})(paper_u);