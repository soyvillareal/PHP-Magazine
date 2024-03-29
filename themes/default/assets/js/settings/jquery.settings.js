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
    var j = $('#content-vemail'),
        l = function(e){
            e.removeClass('hidden')
        },
        z = function(e){
            e.toggleClass('hidden')
        },
        t = function(e, i){
            e.attr('disabled', i)
        },
        n = function(e){
            e.removeClass('spinner-is-loading'), t(e, !0)
        };

    (function(){
        var s = $('#username'),
            q = function(e){
                e.removeClass('border-red boxshadow-red');
            },
            b = function(e){
                e.addClass('border-red boxshadow-red')
            },
            u = function(i, e){
                var y = i.parent(),
                    j = y.next().find('.btn_save');
                y.addClass('spinner-is-loading'), $.post(paper_s.url.validate+"?token="+paper_s.token, e, function(e){
                    if(e.S == 200){
                        n(y), t(j, !0), i.siblings('.text-error').text(e.E), b(i)
                    } else {
                        n(y), t(j, !1)
                    }
                })
            },
            a = function(e, i = ''){
                e.addClass('color-red').removeClass('color-green').text(i)
            },
            m = paper_s.arr_birthday;
        
        (function(){
            var j = $('body'),
                n = $('#btn-adelete'),
                e = $('#alert-delete');

            $('.item_input').on('keyup, input', function(){
                var x = $(this),
                    k = $('#text-count'),
                    h = k.parent(),
                    y = x.parents('form'),
                    v = y.find('.text-error'),
                    w = x.attr('data-send'),
                    o = y.find('.btn_save'),
                    r = x.val(),
                    g = r.length;

                a(v), q(y.find('.item_input'));
                if(w == 'username'){
                    if(r.match(/^[a-zA-Z0-9]+$/)){
                        u(s, {'username': r});
                    } else if(g > 0){
                        v.text('*'+paper_s.word.write_only_numbers_letters), b(s)
                    }
                    if(g > 16) {
                       x.val(r.slice(0, 16))
                    }
                } else if(w == 'new_email'){
                    u(x, {'email': r});
                } else if(w == 'birthday'){
                    if(m.length == 3){
                        var p = x.attr('id');
                        m[(p == 'day' ? 0 : (p == 'month' ? 1 : 2))] = parseInt(r), t(o, !1); 
                    }
                    if(p == 'month' || p == 'year'){
                        var d = $('#day'),
                            c = d.val();
                        d.html('<option value disabled selected>'+paper_s.word.year+'</option>');
                        for (var i = 1; i <= (new Date($('#year').val(), $('#month').val(), 0)).getDate(); i++){
                            var f = $('<option>');
                            f.attr('value', i).text(i), i == c && f.attr('selected', !0), d.append(f);
                        }
                    }
                } else if(w == 'about'){
                    var z = paper_s.settings.max_words_about;
                    (g > (z*.8)) ? (h.addClass('color-red').removeClass('color-green color-orange')) : ((g > (z*.5)) ? (h.addClass('color-orange').removeClass('color-red color-green')) : (h.addClass('color-green').removeClass('color-red color-orange'))), k.text(g), x.val(r.slice(0, (z-1)))
                } else if(w == 'delete_account'){
                    t(n, !(r === paper_s.word.DELETE_COMMAND))
                }
                ['username', 'new_email', 'gender', 'birthday'].indexOf(w) != -1 && r == '' ? (v.text('*'+paper_s.word.this_field_is_empty), b(x), t(o, !0)) : t(o, !1), g > 0 ? x.addClass('active') : x.removeClass('active')
            }), n.click(function(){
                l(e), j.addClass('overflow-hidden')
            }), $('#btn-cancel').click(function(){
                z(e), j.removeClass('overflow-hidden')
            }), $('#btn-delete').click(function(){
                var x = $(this),
                    y = n.parents('form'),
                    v = y.find('.text-error'),
                    g = y.find('input');
                a(v), t(x.addClass('spinner-is-loading'), !0), $.post(paper_s.url.delete_account+"?token="+paper_s.token, {user_id: paper_s.user_id, delete_command: g.val()}, function(c) {
                    if(c.S == 200) {
                        window.location.href = c.UR
                    } else {
                        g.val('').removeClass('active'), a(v, c.E), t(n, !0), t(x.removeClass('spinner-is-loading'), !1), z(e), j.removeClass('overflow-hidden')
                    }
                })
            })
        })(), (function(){
            $('.ishow_check').change(function(){
                var x = $(this),
                    y = x.parents('form'),
                    w = x.attr('data-send'),
                    v = y.find('.text-error'),
                    g = y.find('.btn_save');
                t(g, !0), t(x, !0), $.post(paper_s.url.shows+"?token="+paper_s.token, {user_id: paper_s.user_id, 'input': w, 'show': x.is(':checked')}, function(e) {
                    if(e.S == 200) {
                        v.addClass('color-green').text(e.M), setTimeout(function(){
                            a(v), t(g, !1), t(x, !1)
                        }, 3000);
                    } else a(v, e.E);
                })
            }), $('.btn_save').click(function(e){
                e.preventDefault();
                var x = $(this),
                    y = x.parents('form'),
                    o = x.parents('.container_collapse'),
                    c = y.find('.item_input'),
                    v = y.find('.text-error'),
                    u = $('.text_email'),
                    k = $('#email-alert'),
                    w = c.attr('data-send'),
                    r = w != 'birthday' ? c.val() : JSON.stringify(m);
                a(v), x.addClass('spinner-is-loading'), t(x, !0), $.post(paper_s.url.account+"?token="+paper_s.token, {user_id: paper_s.user_id, 'input': w, 'value': r}, function(e) {
                    if(e.S == 200) {
                        o.find('.text_label').html(e.M), z(o.find('.content_cheader')), z(o.find('.content_citems')), q(c), n(x), w == 'new_email' && (e.EQ == 1 ? ($(u[0]).attr('class', 'text_email display-block'), $(u[1]).attr('class', 'text_email display-flex'), k.text(e.EM)) : ($(u[0]).attr('class', 'text_email display-flex'), $(u[1]).attr('class', 'display-inline-block ellipsis-horizontal'), k.text(e.EM)), e.EV == 1 && z($('#btn-show-verify'))), w == 'username' && (c.attr('readonly', !0), $('#username-alert').text(e.EM)), w == 'birthday' && (t(c, !0), $('#birthday-alert').text(e.EM)), w == 'newsletter' && ($('#newsletter-alert').html(e.HT));
                    } else {
                        v.text(e.E), b(c), n(x);
                    }
                });
            }), $('.btn_collapse, .btn_ccancel').click(function(){
                var x = $(this),
                    g = x.parents('.container_collapse'),
                    y = x.parents('form'),
                    c = y.find('.item_input'),
                    p = y.find('.ishow_check'),
                    v = y.find('.text-error');
                z(g.find('.content_cheader')), z(g.find('.content_citems')), v.text(''), p.length > 0 && t(p, !1), q(c), paper_s.user_type  != 'normal' && (l($('#content-disconnect')), z(y));
            }), $('#btn-show-verify, .btn_vcancel').click(function(){
                var g = $(this).parents('.container_collapse');
                z(j), z(g.find('.content_cheader'));
            }), $('.btn_cgeneral').click(function(){
                var g = $(this).parents('.container_cgeneral');
                z(g.find('.content_cgitems')), $(this).find('svg').toggleClass('rotate-180');
            })
        })();
    })(), (function(){
        var r = $('#btn-upload'),
            u = $('#upload-file'),
            d = $('#btn-ucancel'),
            h = $('#content-avatar img'),
            w = r.parent().siblings('.text-error'),
            q = function(e){
                l(h.attr('src', e)), o = !0, a = !0, r.text(paper_s.word.upload), w.text(''), l(d.text(paper_s.word.cancel)), r.blur()
            },
            f = function(e){
                e.addClass('hidden')
            },
            s = paper_s.user_avatar_s,
            m = paper_s.default_holder,
            o = !1,
            a = !1,
            p = 0;

        paper_s.user_type != 'normal' && $('#btn-disconnect').click(function(){
            var y = $(this).parent();
            y.siblings('form').removeAttr('class'), f(y);
        }), r.click(function(){
            if(o){
                p = new FormData(), p.append('user_id', paper_s.user_id), p.append('avatar', u.prop('files')[0]), t(r.text(paper_s.word.please_wait), !0), w.text(''), $.ajax({
                    url: paper_s.url.upload_avatar+"?token="+paper_s.token,
                    type: 'POST',
                    data: p,
                    contentType: false,
                    processData: false,
                    success: function(e) {
                        if(e.S == 200){
                            u.val(''), t(r.text(e.EM), !1), m = 1, s = e.AV, d.text(e.ED), o = !1, a = !1, l(d);
                        } else {
                            u.val(''), t(r.text(e.EM), !1), e.DH == 1 && (d.text(e.ED), a = !1), o = !1, h.attr('src', s), w.text(e.E);
                        }
                    }
                });
            } else {
                u.trigger('click');
            }
        }), u.change(function(){
            var y = $(this),
                v = y.prop('files');
            if (v && v[0]) {
                var g = new FileReader();
                if(g && g.readAsDataURL){
                    g.readAsDataURL(v[0]), $(g).on('load', function(){
                        q(g.result)
                    })
                } else if(window.URL.createObjectURL){
                    q(window.URL.createObjectURL(v[0]))
                }
            }
        }), d.click(function(){
            a ? (h.attr('src', s), u.val(''), o = !1, a = !1, r.text(paper_s.word.upload_a_picture), d.text(paper_s.word.delete), w.text(''), m == 0 && f(d)) : (t(d.text(paper_s.word.please_wait), !0), w.text(''), $.post(paper_s.url.reset_avatar+"?token="+paper_s.token, {user_id: paper_s.user_id}, function(e){
                e.S == 200 ? (m = 0, s = e.AV, h.attr('src', s), r.text(e.EM), f(d), d.text(e.ED), t(d, !1), r.blur()) : (w.text(e.E));
            }));
        }), $('#btn-verify-email').click(function(){
            var y = $(this),
                v = y.siblings('.text-error'),
                k = j.children();
            v.text(''), t(y.addClass('spinner-is-loading'), !0), $.post(paper_s.url.send_code+"?token="+paper_s.token, {user_id: paper_s.user_id}, function(e){
                if(e.S == 200){
                    f($(k[0]), l($(k[1])), n(y))
                } else {
                    v.text(e.E), n(y)
                }
            })
        })
    })()
})(paper_s);