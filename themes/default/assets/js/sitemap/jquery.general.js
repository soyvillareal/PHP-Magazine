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
   	var w = $('#footer'),
   		q = $('#content-sposts'),
       	n = $('#spinner-load'),
       	l = paper_sm.sitemap_ids,
       	b = !0;

   $(document).scroll(function(){
      if(b && ($(document).scrollTop() + $(window).height()) >= ($(document).height() - w.height())){
         b = !1, n.removeClass('hidden'), $.post(paper_sm.url.load_sitemap+"?token="+paper_sm.token, {date: paper_sm.date, day: paper_sm.day, month: paper_sm.month, year: paper_sm.year, sitemap_ids: JSON.stringify(l)}, function(e){
            if(e.S == 200) {
               q.append(e.HT), l = e.IDS, n.addClass('hidden'), b = !0
            } else n.addClass('hidden');
         })
      }
   });
})(paper_sm);