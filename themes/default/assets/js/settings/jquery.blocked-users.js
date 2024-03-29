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
	var d = $('#navigation-after'),
        g = $('#navigation-before'),
        h = $('#navigation-first'),
        j = $('#navigation-last'),
        n = $('#spinner-load'),
        b = $('#alert-delete'),
        w = $('#btn-block'),
        x = function(){
            b.addClass('hidden'), w.removeAttr('data-id');
        },
        y = function(e){
            u(e.removeClass('spinner-is-loading'), !1)
        },
        u = function(e, i){
            e.attr('disabled', i)
        },
        z = paper_sl.total_pages,
        t = !0,
        s = 1;
    $(document).on('click', '.btn_balert', function(){
        b.removeClass('hidden'), w.attr('data-id', $(this).blur().parents('.content-row-table').attr('data-id'))
    }), w.click(function(e){
        var r = $(this),
            v = r.attr('data-id');
        v && (u(r.addClass('spinner-is-loading'), !0), $.post(paper_sl.url.unlock_user+"?token="+paper_sl.token, {user_id: paper_sl.user_id, profile_id: v}, function(e){
            if(e.S == 200) {
                $('.content-row-table').length > 1 ? $('.content-row-table[data-id='+v+']').remove() : window.location.reload(), y(r), x()
            } else {
                y(r), x()
            }
        }))
    }), $('#btn-cancel').click(x), $('.load_table_page').click(function(){
        var q = $(this),
        	v = q.attr('data-type');
        if(v == 'after') {
            if(z > s) {
                s++, u(h, !1), u(g, !1)
            }
        } else if(v == 'before'){
            if(s > 1){
                s--, u(j, !1), u(d, !1)
            }
        } else if(v == 'last')   {
            s = z, u(j, !0), u(h, !1), u(g, !1)
        } else if(v == 'first') {
            s = 1, u(h, !0), u(d, !1), u(j, !1)
        }
        if(s == 1){
            u(h, !0), u(g, !0)
        } else if(s == z) {
            u(j, !0), u(d, !0)
        }
        n.removeClass('hidden'), t && (t = !1, $.post(paper_sl.url.table_blocked_users+"?token="+paper_sl.token, {user_id: paper_sl.user_id, page_id: s}, function(e){
            if(e.S == 200) {
                $('#container-pages').html(e.HT), n.addClass('hidden'), $('#page-now').text(s), t = !0
            } else {
                u(q, !0), n.addClass('hidden')
            }
        }))

    });
})();