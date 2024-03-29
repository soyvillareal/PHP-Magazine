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
   var l = $('#header-fixed'),
       o = $('#btn-hfshare'),
       j = $('#hfshare-modal'),
       b = $('body'),
       a = paper_p.post_url,
       s = a;
   (function(){
      var w = function(e){
            var k = $(e).children().first();
            k.addClass('copied'), setTimeout(function(){
               k.removeClass('copied');
            }, 300);
          };
      o.click(function(){
         j.toggleClass('hidden'), l.css('height', 'auto'), o.attr('aria-expanded', !$.parseJSON(o.attr('aria-expanded')));
      }), $(document).click(function(e){ 
         if (!$(e.target).parents().is('#btn-hfshare') && !$(e.target).parents().is('#hfshare-modal .item-share')){
            j.addClass('hidden'), l.removeAttr('style');
         }
      }).on('click', '.btn_clink', function(){
         var x = $(this),
             q = $(x).attr('data-link'),
             t = document.body.createTextRange,
             y,
             k,
             o;
         if(t || window.getSelection){
            y = $('<div>'), b.append(y.text(q ? q : s));
            if(t) {
               k = document.body.createTextRange(), k.moveToElementText(y[0]), k.select();
            } else {
               o = window.getSelection(), k = document.createRange(), k.selectNodeContents(y[0]), o.removeAllRanges(), o.addRange(k);
            }
         } else {
            y = $('<input>'), b.append(y.val(q ? q : s)), y.select();
         }
         document.execCommand("copy"), w(x), y.remove()
      })
   })(), (function(){
      var h = l.find('.btn_save'),
          n = [paper_p.post_id],
          r = function(e, i){
            e.attr('disabled', i);
          },
          i = function(e, i){
            r(e.addClass('spinner-is-loading'), i)
          };

      (function(){
         var o = $('#alert-pdelete'),
             a = $('#btn-pdelete');

         JSON.parse(paper_p.loggedin) && ($(document).on('click', '.btn_save', function(){
            var d = $(this),
                u = d.attr('data-id'),
                t = $('.btn_save[data-id='+u+']'),
                o = t.find('path'),
                w = 0,
                m = 0;

            r(t, !0), $.post(paper_p.url.save+"?token="+paper_p.token, {'post_id': u}, function(e){
               console.log(e);
               if(e.S == 200){
                  if(e.AC == 'delete'){
                     o.attr('fill', 'none'), t.attr('aria-checked', !1), r(t, !1)
                  } else {
                     var c = d.siblings('.content_saved');

                     c.addClass('saved-animation-image'), clearTimeout(w), w = setTimeout(function(){
                        o.attr('fill', 'currentColor'), t.attr('aria-checked', !0), c.removeClass('saved-animation-image'), t.addClass('saved-animation-button'), clearTimeout(m), m = setTimeout(function(){
                           t.removeClass('saved-animation-button'), r(t, !1)
                        }, 100);
                     }, 600)
                  }
                  t.blur();
               }
            })
         }).on('click', '.btn_dalert', function(){
            b.addClass('overflow-hidden'), o.removeClass('hidden'), a.attr('data-id', $(this).attr('data-id'))
         }), $('#btn-pcancel').click(function(){
            b.removeClass('overflow-hidden'), o.addClass('hidden'), a.removeAttr('data-id')
         }), a.click(function(){
            var v = $(this);
            i(v, !0), $.post(paper_p.url.delete+"?token="+paper_p.token, {post_id: v.attr('data-id')}, function(e){
               if(e.S == 200) {
                  window.location = e.LK
               } else r(v.removeClass('spinner-is-loading'), !1);
            });
         }));
      })(), (function(){
         var x = $('#posts'),
             f = $('#spinner-load'),
             y = $('#header').height(),
             v = 0,
             u = 0,
             k = !0,
             d = !1,
             z = !0,
             p,
             s,
             r,
             q,
             g;
         $(document).scroll(function(){
            var w = $(window).scrollTop(),
                m = $(p).length == 1 ? parseInt(((w - p.offset().top) / p.height()) * 100) : 0,
                t = m <= 0 ? 0 : m;
            $('#header-progress').css('width', t+'%');
            if(w < v){
               (t <= 1 && n[u-1] != undefined) && (u--, d = !0), w <= y && (z = !0, j.addClass('hidden'), l.removeAttr('style'), o.attr('aria-expanded', !1), b.removeClass('header-fixed'));
            } else {
               if(((w + $(window).height()) - x.offset().top) >= (x.height() - 100)){
                  if(n[u] == n[n.length-1]){
                     k && (k = !1, f.removeClass('hidden'), $.post(paper_p.url.load+"?token="+paper_p.token, {'category_id': paper_p.category_id, 'post_ids': JSON.stringify(n)}, function(e) {
                        if(e.S == 200){
                           x.append(e.HT), n.push(e.ID), k = !0, f.addClass('hidden')
                        } else f.addClass('hidden')
                     }));
                  }
               }
               (t >= 99 && n[u+1] != undefined) && (u++, d = !0), w >= y && z && (z = !1, b.addClass('header-fixed'));
            }
            v = w <= 0 ? 0 : w, p = $('#post-'+n[u]), r = p.find('.btn_save'), h.attr('aria-checked', r.attr('aria-checked')).attr('data-id', r.attr('data-id')).find('path').attr('fill', r.find('path').attr('fill')), d && (q = p.find('.post-title'), g = q.parent(), d = !1, l.find('img').attr('src', p.find('.post-thumbnail img').attr('src')).attr('title', q.text()), l.find('.item-header-title').text(q.text()), s = g.prop("tagName") == 'A' ? g.attr('href') : a, p.find('.item-share').each(function(i){
               $(j.find('.item-share a')[i]).attr('href', $(this).find('a').attr('href'));
            })), $('.item-post').each(function() {
               var m = $(this),
                   t = m.find('.content-sticky'),
                   c = m.offset().top;
               if (w >= (c - 30)) {
                  t.addClass('sticky');
                  if (w >= (c + m.height() - (t.height() + 100))) {
                     t.removeClass('sticky').css({'bottom': 0, 'top': 'auto'});
                  } else {
                     t.addClass('sticky').removeAttr('style');
                  }
               } else {
                  t.removeClass('sticky').removeAttr('style');
               }
            });
         });
      })(), (function(){
         var p = $('#alert-preport'),
             a = p.find('.text-error'),
             j = p.find('.item_rtextarea'),
             n = $('#item-acontent'),
             m = $('#alert-spalert'),
             c = $('#item-ralert'),
             q = $('#alert-rtitle'),
             w = $('#item-abuttons'),
             t = $('#btn-rsend'),
             s = $('.item_rinput'),
             f = $('li.item_rcomment'),
             g = $('li.item_rpost'),
             h = function(){
               var x = ($(window).height() - 85),
                   y = $('.item_rinput:checked ~ .content_description'),
                   v = (x - (q.height() + w.height()));
               if(v < (n.children().height() - (y.length > 0 ? y.height() : 0))){
                  c.css('height', x), p.css('top', 'calc(50% - '+(x / 2)+'px)'), n.css('height', v)
               } else z();
             },
             z = function(){
               n.removeAttr('style'), c.removeAttr('style'), p.removeAttr('style')
             },
             l = function(){
               p.addClass('hidden'), s.prop('checked', !1), j.val('')
             },
             k = function(e){
               return $(e).addClass('color-grey').removeClass('color-blue');
             };
         paper_p.settings.nodejs == 'on' && SOCKET.on('setOutpreaction', function(e){
            if(e.S == 200){
               var x = $(e.AB ? e.AB : e.DB);
               if(e.DO){
                  $(e.DO).find('.item_likedis').text(e.CO)
               }
               x.find('.item_likedis').text(e.CR);
            }
         }), $(document).on('click', '.btn_plike, .btn_pdislike', function(){
            var x = $(this),
                q = function(e){
                  console.log(e)
                  if(e.S == 200){
                     e.DO && k($(e.DO)).find('.item_likedis').text(e.CO), x.find('.item_likedis').text(e.CR), e.DB && k($(e.DB)), e.AB && $(e.AB).removeClass('color-grey').addClass('color-blue'), r(x.removeClass('spinner-is-loading'), !1);
                  } else {
                     r(x.removeClass('spinner-is-loading'), !1)
                  }
               };
               
               i(x, !0);
               if(paper_p.settings.nodejs == 'off'){
                  $.post(paper_p.url.post_reaction+"?token="+paper_p.token, {post_id: x.attr('data-id'), type: x.attr('data-type')}, q)
               } else {
                  SOCKET.emit('setInpreaction', {post_id: x.attr('data-id'), type: x.attr('data-type')}, q)
               }
         }).on('click', '.btn_report', function(){
            var x = $(this),
                y = x.attr('data-place'),
                u = paper_p.word.report_comment;
            if(y == 'post'){
               u = paper_p.word.report_post, g.removeClass('hidden'), f.addClass('hidden')
            } else (f.removeClass('hidden'), g.addClass('hidden'));
            q.children().text(u), b.addClass('overflow-hidden'), p.removeClass('hidden'), t.attr('data-id', x.attr('data-id')).attr('data-place', y), z(), h()
         }), $('#btn-rcancel').click(function(){
            l(), z(), b.removeClass('overflow-hidden'), p.addClass('hidden'), r(t.removeAttr('data-id'), !0), a.text('')
         }), $('#btn-spclose').click(function(){
            m.addClass('hidden'), b.removeClass('overflow-hidden')
         }), t.click(function(){
            var x = $(this),
                p = x.attr('data-place'),
                y = $('.item_rinput:checked'),
                u = y.parent(),
                d = u.find('.item_rtextarea'),
                v = x.attr('data-id'),
                w = y.val(),
                o = {reported_id: v, type: w, place: p};
            d.length > 0 && (o = {reported_id: v, type: w, place: p, description: d.val()}), a.text(''), r(s, !0), i(x, !0), $.post(paper_p.url.report+"?token="+paper_p.token, o, function(e){
               if(e.S == 200){
                  l(), m.removeClass('hidden'), x.removeClass('spinner-is-loading'), r(s, !1);
               } else {
                  a.text(e.E), r(x.removeClass('spinner-is-loading'), !1), r(s, !1), h()
               }
            });
         }), s.change(function(){
            $('.item_rinput:checked') && r(t, !1), a.text(''), j.val('').next().find('.text_count').text(0), h()
         }), j.on('keyup, input', function(){
            var x = $(this),
                y = x.parent(),
                u = y.find('.text_count'),
                o = u.parent(),
                d = x.val(),
                v = d.length,
                q = paper_p.settings.max_words_report;

            (v > (q*.8)) ? (o.addClass('color-red').removeClass('color-green color-orange')) : ((v > (q*.5)) ? (o.addClass('color-orange').removeClass('color-red color-green')) : (o.addClass('color-green').removeClass('color-red color-orange'))), u.text(v), x.val(d.slice(0, (q-1)))
         }), $(window).resize(h)
      })()
   })()
})(paper_p);