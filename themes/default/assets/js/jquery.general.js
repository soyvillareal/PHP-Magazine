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
    var h = function(e, i){
            e.attr('disabled', i)
        };
    (function(){
        var t = $(window),
            x = $('#drawer'),
            j = $('#content-shfilter'),
            r = $('#content-shinfo'),
            s = $('#content-load-shi'),
            n = $('#content-load-shc'),
            d = $('#key-hreset'),
            u = $('#key-hsearch'),
            c = $('#content-shresults'),
            g = function(e){
                e.attr('aria-expanded', !JSON.parse(e.attr('aria-expanded'))).blur()
            },
            i = function(e){
                return $('.btn_hddown[data-type='+e+']').siblings().find('.btn_hdfilter.selected').attr('data-type')
            },
            y = function(e){
                var v = $('#content-smessage'),
                    z = $('.btn_hdfilter'),
                    k = $('#btn-hsearch'),
                    p = function(e){
                        $('#type-hdate').val(e[0]), $('#type-hcategory').val(e[1]), $('#type-hauthor').val(e[2]), $('#type-hsort').val(e[3])
                    };

                h(z, !0), $('#content-nav').addClass('hidden'), j.removeClass('hidden'), u.val().length > 0 ? (h(k, !1), d.removeClass('hidden')) : (h(k, !0), d.addClass('hidden')), r.addClass('hidden'), c.addClass('hidden'), s.removeClass('hidden'), n.removeClass('hidden'), $.post(paper_g.url.normal_search+"?token="+paper_g.token, {keyword: u.val(), date: i('date'), category: i('category'), author: i('author'), sort: i('sort')}, function(e){
                    if(e.S == 200){
                        p([e.DT, e.CT, e.AT, e.ST]), v.removeClass('hidden'), s.addClass('hidden'), e.KW && (r.removeClass('hidden').html(e.IO)), n.addClass('hidden'), c.html(e.HT).removeClass('hidden'), l = e.IDS, b = w = !0, h(z, !1);
                    } else {
                        p([e.DT, e.CT, e.AT, e.ST]), w = !1, v.addClass('hidden'), c.removeClass('hidden').html(e.HT), r.addClass('hidden'), s.addClass('hidden'), n.addClass('hidden'), h(z, !1)
                    }
                })
            },
            l = [],
            w = !1,
            b = !0;

        (function(){
            var m = $('.btn_hddown'),
                q = function(){
                    var v = $('.btn_hddown[aria-expanded=true]'),
                        o = v.siblings(),
                        z = v.parent();
                    v.length > 0 && o.css({'left': ((z.width() - o.width()) / 2) + z.position().left})
                },
                a = !0,
                l = x.children();
            m.click(function(){
                var v = $(this),
                    o = v.siblings(),
                    p = $('.btn_hddown[aria-expanded=true]'),
                    f = m.index(v),
                    z = v.parent(),
                    k = 0;
                m.parent().each(function(i){
                    if(i < f) k += $(this).width();
                }), a = !1, p.length > 0 && (p.siblings().addClass('hidden'), g(p)), x.scrollTop(0), l.addClass('overflow-hidden'), g(v), o.removeClass('hidden').css({'left': ((z.width() - o.width()) / 2) + z.position().left}), j.width() < t.width() && j.animate({scrollLeft: (f == 0 ? 0 : (f == m.length - 1 ? j.children().width() : k))}, 750, function(){
                    a = !0
                });
            }), j.scroll(function(){
                var v = $('.btn_hddown[aria-expanded=true]'),
                    o = v.siblings(),
                    z = j.width(),
                    p = (j.scrollLeft() + (t.width() - 40));
                ((m.index(v) == 0 && p > z || m.index(v) == 2 && p < j.children().width()) && a) && (o.addClass('hidden'), l.removeClass('overflow-hidden')), q()
            }), $('.btn_hdfilter').click(function(){
                var p = $(this),
                    z = p.parents('.item_hdfilter'),
                    o = z.prev(),
                    v = o.attr('data-type');
                z.find('.btn_hdfilter.selected').removeClass('selected'), p.addClass('selected'), y(), o.text((v == 'date' ? paper_g.word.date : (v == 'category' ? paper_g.word.category : paper_g.word.sort_by))+' ('+$.trim(p.text())+')');
            }), t.resize(q), $(document).click(function(e){
                var v = $('.btn_hddown[aria-expanded=true]');
                $('.item_hdfilter, .item_hdfilter *, .btn_hddown, .btn_hddown *').index(e.target) == -1 && v.length > 0 && (v.siblings().addClass('hidden'), j.removeClass('pointer-events-none'), g(v), l.removeClass('overflow-hidden'))
            })
        })(), (function(){
            var a = $('body'),
                q = x.children(),
                o = $('#content-nav'),
                f = $('.btn_drawer'),
                k = function(){
                    w = !1, $('#content-smessage').removeClass('hidden'), j.addClass('hidden'), n.addClass('hidden'), o.removeClass('hidden'), c.addClass('hidden').html(''), s.addClass('hidden'), r.addClass('hidden').html('')
                };
            d.click(k), f.click(function(){
                k(), u.val(''), a.toggleClass('overflow-hidden').toggleClass('backdrop-active-as'), g($(f[0])), paper_g.loggedin ? a.removeClass('modal-login-active') : $('.btn_account').removeClass('active').siblings().addClass('hidden')
            }), u.keyup(y), q.scroll(function(){
                if(w && b && (q.scrollTop() + q.height()) >= (q.children().height() - 100)){
                    b = !1, n.removeClass('hidden'), $.post(paper_g.url.table_search+"?token="+paper_g.token, {keyword: u.val(), search_ids: JSON.stringify(l), date: i('date'), category: i('category'), author: i('author'), sort: i('sort')}, function(e){
                        e.S == 200 ? (c.append(e.HT), l = e.IDS, n.addClass('hidden'), b = !0) : n.addClass('hidden')
                    })
                }
            })
        })();
    })(), paper_g.loggedin && (function(){
        var c = $(window),
            b = $('#container-notifications'),
            g = $('#content-nsettings'),
            v = $('#btn-bnotifications'),
            q = $('#btn-bnmenu'),
            m = $('#btn-nsettings'),
            y = $('#btn-nssave'),
            j = $('#content-notifications'),
            s = $('#content-ntspinner'),
            w = $('#container-menu'),
            n = $('.item_caccount'),
            p = $('#content-nbspinner'),
            i = $('#content-nreload'),
            d = $('#content-ntno'),
            a = function(){
                var x = (c.height() - 65),
                    e = (c.width() - 20);
                b.css({'width': (e <= o ? e : ''), 'height': (x <= r ? x : '')})
            },
            t = function(){
                m.removeClass('hidden'), b.attr('data-content') == 'hide' && d.removeClass('hidden'), y.addClass('hidden'), g.addClass('hidden'), j.removeClass('hidden'), q.removeClass('hidden'), v.addClass('hidden')
            },
            f = function(e){
                e.removeClass('spinner-is-loading').addClass('hidden')
            },
            k = function(e){
                e.addClass('spinner-is-loading').removeClass('hidden')
            },
            r = b.height(),
            o = b.width(),
            z = !0,
            u = !0;

        c.resize(a), q.click(function(){
            b.addClass('hidden'), w.removeAttr('style')
        }), v.click(t), m.click(function(){
            g.removeClass('hidden'), d.addClass('hidden'), j.addClass('hidden'), v.removeClass('hidden'), q.addClass('hidden'), y.removeClass('hidden'), m.addClass('hidden')
        }), y.click(function(){
            var x = $(this);
            h(x.addClass('spinner-is-loading'), !0), $.post(paper_g.url.nsettings+"?token="+paper_g.token, {values: JSON.stringify(g.children().serializeArray())}, function(e){
                if(e.S == 200){
                    h(x.removeClass('spinner-is-loading'), !1), t()
                } else {
                    h(x.removeClass('spinner-is-loading'), !1), t()
                }
            })
        }), b.children().last().scroll(function(){
            var x = $(this),
                e = [];
            if((x.scrollTop() + x.height()) >= (j.height() * .9)){
                z && (z = !1, j.children().each(function(){
                    e.push($(this).attr('data-id'))
                }), k(p), $.post(paper_g.url.get_ncontent+"?token="+paper_g.token, {notify_ids: JSON.stringify(e)}, function(e){
                    if(e.S == 200){
                        f(p), j.append(e.HT), z = !0
                    } else f(p);
                }))
            }
        }), (function(){
            var p = $('#container-languages'),
                q = $('#btn-notifications'),
                t = $('#btn-messages'),
                r = function(e = 0, x){
                    x > 0 ? e.removeClass('color-grey').addClass('font-bold color-red') : e.addClass('color-grey').removeClass('font-bold color-red')
                },
                l = function(){
                    k(s), b.removeClass('hidden'), h(m, !0), $.post(paper_g.url.get_ncontent+"?token="+paper_g.token, function(e){
                        if(e.S == 200){
                            n.text((o == 0 && c == 0 ? '' : (c > 0 ? c : ''))), f(s), h(m, !1), a(), r(q), d.addClass('hidden'), b.removeAttr('data-content'), w.css('opacity', 0), j.html(e.HT), i.removeAttr('style'), z = u = !0
                        } else {
                            f(s), a(), b.attr('data-content', 'hide'), d.removeClass('hidden'), h(m, !1);
                        }
                    })
                },
                y = function(e){
                    if(e.S == 200){
                        o = e.CN, c = e.CM, n.text(e.CT), u && e.CN > 0 && i.css('top', 50), r(q, e.CN), r(t, e.CM)
                    } else {
                        n.text(''), r(q), r(t)
                    }
                },
                o = 0,
                c = 0;

            q.click(l), $('#btn-nreload').click(l), $('#btn-cnreload').click(function(){
                i.removeAttr('style'), u = !1;
            }), $('#btn-languages, #btn-blmenu').click(function(){
                p.toggleClass('hidden'), w.toggleClass('hidden')
            }), paper_g.settings.nodejs == 'off' ? setInterval(function(){
                $.post(paper_g.url.get_count+"?token="+paper_g.token, y)
            }, 5000) : SOCKET.on('setOutnotifies', y)

        })();
    })(), paper_g.settings.switch_mode == 'on' && (function(){
        var w = $('.btn_darkmode');
        w.click(function(){
            var x = $(this),
                p = $('html'),
                y = x.attr('data-type'),
                z = 'night',
                u = '<title>{$word->activate_night_mode}</title><path fill="#ccc" d="M13.276 24c3.701 0 7.082 -1.684 9.321 -4.443 0.331 -0.408 -0.03 -1.005 -0.542 -0.907 -5.822 1.109 -11.169 -3.355 -11.169 -9.232 0 -3.385 1.812 -6.499 4.758 -8.175 0.454 -0.258 0.34 -0.947 -0.176 -1.042A12.101 12.101 0 0 0 13.276 0c-6.624 0 -12 5.368 -12 12 0 6.624 5.368 12 12 12z"/>',
                o = 0;
            if(y == 'night'){
               z = 'light', u = '<title>{$word->disable_night_mode}</title><path fill="#fff" d="M12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12zM11 1h2v3h-2V1zm0 19h2v3h-2v-3zM3.515 4.929l1.414-1.414L7.05 5.636 5.636 7.05 3.515 4.93zM16.95 18.364l1.414-1.414 2.121 2.121-1.414 1.414-2.121-2.121zm2.121-14.85l1.414 1.415-2.121 2.121-1.414-1.414 2.121-2.121zM5.636 16.95l1.414 1.414-2.121 2.121-1.414-1.414 2.121-2.121zM23 11v2h-3v-2h3zM4 11v2H1v-2h3z"/>', o = 1
            };
            if(paper_g.loggedin){
                h(x.addClass('spinner-is-loading'), !0), $.post(paper_g.url.set_darkmode+'?token='+paper_g.token, {darkmode: o}, function(e){
                    if(e.S == 200){
                        h(x.removeClass('spinner-is-loading'), !1), y == 'night' ? p.attr('dark', !0) : p.removeAttr('dark'), w.attr('data-type', z), w.find('svg').html(u), $('#item-tdarkmode').text(e.TX);
                    } else h(x.removeClass('spinner-is-loading'), !1)
                });
            } else {
                y == 'night' ? p.attr('dark', !0) : p.removeAttr('dark'), w.attr('data-type', z), w.find('svg').html(u), document.cookie = 'darkmode='+o+';max-age='+((new Date().getTime()) + 31536000000)+';path=/';
            }
         })
    })();
})(paper_g);