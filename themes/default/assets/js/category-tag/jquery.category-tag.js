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
       l = paper_c.catag_ids,
       b = !0;

   l.length > 0 && (function(){
      var q = $('#container-sposts'),
          c = $('#content-sresults'),
          n = $('#content-load-sc');
          
      $(document).scroll(function(){
         if(b && ($(document).scrollTop() + $(window).height()) >= q.height()){
            b = !1, n.removeClass('hidden'), $.post(paper_c.url.load_posts+"?token="+paper_c.token, {typet: paper_c.type, catag_id: paper_c.catag_id, catag_ids: JSON.stringify(l)}, function(e){
               if(e.S == 200) {
                  c.append(e.HT), l = e.IDS, n.addClass('hidden'), b = !0
               } else n.addClass('hidden');
            })
         }
      });
   })();
})(paper_c);