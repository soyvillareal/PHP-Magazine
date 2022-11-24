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
    $.createNewsletter = function(t) {
      var w = function(e, i){
            e.attr('disabled', i);
          },
          r = function(e){
            w(e.removeClass('spinner-is-loading'), !1)
          },
          b = $('body'),
          c = $('.newsletter_category'),
          q = c.children(),
          v = $('.text_all'),
          m = $('.text_none'),
          g = $('.btn_newsletter'),
          p = $('.item_place'),
          d = !0;

      $('.close-newsletter').click(function(){
         $('.newsletter-fixed').addClass('hidden'), $('.personalize_newsletter').removeAttr('style'), b.removeClass('newsletter-personalized-active'), document.cookie = 'dismiss_newsletter=true;max-age='+((new Date().getTime()) + 31536000000)+';path=/';
      }), $('.newsletter_type').change(function(){
         var k = $(this),
             y = k.parents('.content_newsletter').find('.personalize_newsletter');
         k.val() == 'personalized' ? (y.css('height', y.children().first().height(), b.addClass('newsletter-personalized-active'))) : (y.removeAttr('style'), b.removeClass('newsletter-personalized-active'));
      }), c.change(function(e){
         $(this).children().length == $(this).val().length ? (v.addClass('hidden'), m.removeClass('hidden')) : (v.removeClass('hidden'), m.addClass('hidden'));
      }), $('.select_all').click(function(){
         q.prop('selected', d), d = !d, v.toggleClass('hidden'), m.toggleClass('hidden');
      }), g.click(function(){
         var v = $(this),
             q = v.parent(),
             k = q.find('.newsletter_email'),
             z = k.siblings('.text-error');
         w(v.addClass('spinner-is-loading'), !0), $.post(t.url+"?token="+t.token, {email: k.val(), type: q.find('.newsletter_type').val(), frequency: q.find('.newsletter_frequency').val(), popular: q.find('.newsletter_popular').is(':checked'), cats: JSON.stringify(q.find('.newsletter_category').val())}, function(e) {
            e.S == 200 ? (w(p, !0), r(v), z.addClass('color-green').removeClass('color-red').text(e.M), setTimeout(function(){
               k.removeClass('active'), z.text('').addClass('color-red').removeClass('color-green'), q.find('.personalize_newsletter').removeAttr('style'), $('.content_newsletter').trigger("reset"), w(p, !1), w(v, !1)
            }, 3000)) : (z.text(e.E), k.addClass('border-red boxshadow-red'), r(v));
         });
      }), $('.newsletter_email').on('keyup, input', function(){
         var v = $(this);
         v.val() != '' && (v.removeClass('border-red boxshadow-red').siblings('.text-error').text(''), w(g, !1))
      })
   }
}(jQuery);