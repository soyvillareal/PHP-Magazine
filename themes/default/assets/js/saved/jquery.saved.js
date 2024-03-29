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
       l = paper_us.saved_ids,
       b = !0;
   
   l.length > 0 && (function(){
      var q = $('#container-saposts'),
         c = $('#content-saposts'),
         n = $('#content-lsaload');
      $(document).scroll(function(){
         if(b && ($(document).scrollTop() + $(window).height()) >= q.height()){
            b = !1, n.removeClass('hidden'), $.post(paper_us.url.load_posts+"?token="+paper_us.token, {saved_ids: JSON.stringify(l)}, function(e){
               if(e.S == 200) {
                  c.append(e.HT), l = e.IDS, n.addClass('hidden'), b = !0
               } else n.addClass('hidden');
            })
         }
      }), $('.btn_dsave').click(function(){
         var v = $(this);
         w(v.addClass('spinner-is-loading'), !0), $.post(paper_us.url.delete_saved+"?token="+paper_us.token, {post_id: v.attr('data-id')}, function(e){
            if(e.S == 200){
               v.parents('.item_sapost').remove()
            } else {
               w(v.removeClass('spinner-is-loading'), !1)
            }
         })
      });
   })();
})(paper_us);