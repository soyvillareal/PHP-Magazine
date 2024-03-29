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
	var g = function(e = '', i = !1, u = ''){
        var x = $('#content-lnotify');
        clearTimeout(t), u == 'blue' ? x.addClass('background-blue').removeClass('background-red') : (u == 'red' ? x.addClass('background-red').removeClass('background-blue') : x.removeClass('background-blue background-red')), x.css((paper_p.dir == 'rtl' ? 'right' : 'left'), (i ? 20 : '')).find('p').text(e), t = setTimeout(function(){
            g()
        }, 5000);
    },
    t = 0;

   	(function(){
      	var r = function(e, i){
            	e.attr('disabled', i);
          	},
          	i = function(e, i){
            	r(e.addClass('spinner-is-loading'), i)
          	},
          	c = function(e){
            	return e.parents('.content_comment')
          	},
          	h = function(e){
            	return e.parents('.content_preply').addClass('hidden').find('.text_carea')
          	},
          	k = function(e, u = !0){
            	var e = this == window ? e : $(this),
                	x = $('.btn_coptions[aria-expanded=true]'),
                	v = function(i){
                  	return i.attr('aria-expanded', !JSON.parse(i.attr('aria-expanded')))
                	};
            	u && x.length > 0 && v(x), v(e).blur()
          	},
          	l = function(){
            	u.addClass('hidden'), o.removeAttr('data-id')
          	},
          	b = function(){
            	return d.find('.btn_coptions[aria-expanded=true]')
          	},
          	f = function(e, i){
            	var s = e.find('svg'),
                	v = e.siblings('.content_replies, .btn_rload');
            	e.attr('data-type', 'hide'), i.text(paper_p.word.hide), s.addClass('rotate-180'), v.removeClass('hidden')
          	},
          	j = function(e, i = ''){
            	var x = c(e),
                	v = x.find('.content_cprogress'),
                	s = v.next(),
                	y = x.find('.btn_comment, .btn_reply');
            	i ? (v.addClass('hidden'), s.removeClass('hidden'), r(y, !0)) : (s.addClass('hidden'), v.removeClass('hidden'), r(y, !1)), s.children().text(i)
          	},
          	z = function(e, i, u){
            	var e = e.hasClass('text_carea') ? e : e.parents('.content_cbuttons').prev(),
                	x = e.next().find('.container_cprogress'),
                	s = x.find('svg');
            	x.attr('aria-valuenow', i), i > 70 ? (s.addClass('color-red').removeClass('color-blue color-orange')) : (i > 40 ? (s.addClass('color-orange').removeClass('color-red color-blue')) : (s.addClass('color-blue').removeClass('color-red color-orange'))), x.find('circle').last().attr('stroke-dashoffset', u), j(e)
          	},
          	w = function(e){
            	return e.parents('.container_comments')
          	},
          	m = function(e){
            	return w(e).find('.btn_csortby').val()
          	},
          	d = $('.content_comments'),
          	u = $('#alert-cdelete'),
          	o = $('#btn-cdelete'),
          	q = !0,
          	a = !1,
          	p = !0;
       
		paper_p.settings.nodejs == 'on' && SOCKET.on('setOutcreaction', function(e){
			if(e.S == 200){
				var x = $(e.AB ? e.AB : e.DB);
				if(e.DO){
					$(e.DO).find('.item_likedis').text(e.CO)
			   	}
				x.find('.item_likedis').text(e.CR);
			}
		}), $(document).on('focus', '.text_carea', function(){
         	var s = $(this);
         	s.val().length == 0 && s.prop('baseScrollHeight', s.prop('scrollHeight')), c(s).find('.content_cbuttons').removeClass('hidden');
      	}).on('keyup, input', '.text_carea', function(){
         	var x = $(this),
             	v = x.val(),
             	s = v.length,
             	y = paper_p.settings.max_words_comments,
             	e = (s/y);
         	s <= y ? z(x, (e*100), (100-(e*55))) : j(x, y-s), x.val().length > 0 && x.removeClass('border-red'), x.attr('rows', 1).attr('rows', (1 + Math.ceil((x.prop('scrollHeight') - x.prop('baseScrollHeight')) / 24)));
      	}).on('click', '.btn_ccancel', function(){
         	z(c($(this)).find('.content_cbuttons').addClass('hidden').prev().val('').attr('rows', 1), 0, 100)
      	}).on('click', '.btn_rcancel', function(){
         	var x = h($(this));
         	x.val('').attr('rows', 1), z(x, 0, 100)
      	}).on('click', '.btn_comment', function(e){
         	var v = $(this),
             	x = v.attr('data-id'),
             	s = v.parent(),
             	y = s.prev(),
             	o = w(v),
             	a = o.find('.content_comments'),
             	p = o.find('.content_comment[data-pinned=true]'),
				f = function(e){
					console.log(e);
					if(e.S == 200){
					s.addClass('hidden'), y.val('').attr('rows', 1), p.length == 0 ? a.prepend(e.HT) : p.after(e.HT), v.parents('.post-center').find('.item_comments').text(e.CC), r(v.removeClass('spinner-is-loading'), !1), z(v, 0, 100)
					} else {
					r(v.removeClass('spinner-is-loading'), !1), g(e.E, !0, 'red')
					}
				},
				u = {post_id: x, text: y.val()};

				
			if(q){
				if($.trim(y.val()) == ''){
					return y.val(''), e.preventDefault(), !1;
				}
				i(v, !0);
				if(paper_p.settings.nodejs == 'off'){
					$.post(paper_p.url.comment+"?token="+paper_p.token, u, f)
				} else {
					SOCKET.emit('setIncomment', u, f)
				}
			}
      	}).on('click', '.btn_reply', function(){
         	var v = $(this),
             	s = h(v),
				u = function(e){
					if(e.S == 200){
					   	var x = c(v).find('.content_replies').append(e.HT).removeClass('hidden').siblings('.btn_sreplies');
					   	s.val('').attr('rows', 1), x.find('.item_screplies').text(e.HC), x.attr('data-type') == 'show' && f(x, x.find('.item_streplies')), r(v.removeClass('spinner-is-loading'), !1), z(v, 0, 100)
					} else {
					   	r(v.removeClass('spinner-is-loading'), !1), g(e.E, !0, 'red')
					}
				},
				w = {comment_id: v.attr('data-id'), text: s.val()};
			
			if(q){
				i(v, !0);
				if(paper_p.settings.nodejs == 'off'){
					$.post(paper_p.url.reply+"?token="+paper_p.token, w, u);
				} else {
					SOCKET.emit('setInreply', w, u);
				}
			}
      	}).on('click', '.btn_sreply', function(){
         	var x = $(this),
             	v = x.attr('data-username'),
             	y = x.parents('.item_cr').next(),
             	s = y.find('.text_carea');
         	q && (y.removeClass('hidden'), setTimeout(function(){
            	s.focus(), paper_p.loggedin && v && s.val('@'+v+' ')
         	}));
      	}).on('click', '.btn_clike, .btn_cdislike', function(){
         	var x = $(this),
             	s = function(e){
               		return $(e).addClass('color-grey').removeClass('color-blue');
             	},
				z = function(e){
					console.log(e);
					if(e.S == 200){
					   e.DO && s($(e.DO)).find('.item_likedis').text(e.CO), x.find('.item_likedis').text(e.CR), e.DB && s($(e.DB)), e.AB && $(e.AB).removeClass('color-grey').addClass('color-blue'), r(x.removeClass('spinner-is-loading'), !1);
					} else {
					   r(x.removeClass('spinner-is-loading'), !1), g(e.E, !0, 'red')
					}
				},
				w = {comment_id: x.attr('data-id'), type: x.attr('data-type'), treact: x.attr('data-treact')};
			if(q){
				i(x, !0);
				if(paper_p.settings.nodejs == 'off'){
					$.post(paper_p.url.comment_reaction+"?token="+paper_p.token, w, z);
				} else {
					SOCKET.emit('setIncrection', w, z)
				}
			}
      	}).on('click', '.btn_coptions', k).click(function(e){
         	var x = b();
         	!$(e.target).is('.content_cdown, .content_cdown *') && x.length > 0 && k(x, !1)
      	}).on('click', '.btn_scdelete', function(){
         	var x = $(this);
         	q && (o.attr('data-id', x.attr('data-id')).attr('data-type', x.attr('data-type')), u.removeClass('hidden'), k(b(), !1));
      	}).on('click', '.btn_cpin', function(){
         	var v = $(this),
             	s = c(v);

			q && (i(v, !0), $.post(paper_p.url.pin+"?token="+paper_p.token, {comment_id: v.attr('data-id'), sort_by: m(v)}, function(e){
				if(e.S == 200){
					e.HI && $(e.HI).replaceWith(e.HO), s.remove(), $('html').animate({scrollTop: (d.prepend(e.HT).offset().top - s.find('.item-comment').height())}, 750)
				} else {
					r(v.removeClass('spinner-is-loading'), !1), g(e.E, !0, 'red')
				}
			}))
      	}).on('click', '.btn_cunpin', function(){
         	var v = $(this);
         	q && (i(v, !0), $.post(paper_p.url.unpin+"?token="+paper_p.token, {comment_id: v.attr('data-id')}, function(e){
	            if(e.S == 200){
	               $(e.HE).replaceWith(e.HT)
	            } else {
	               r(v.removeClass('spinner-is-loading'), !1), g(e.E, !0, 'red')
	            }
         	}))
      	}).on('click', '.btn_rload', function(){
         	var v = $(this),
             	s = c(v),
             	x = [];

         	q && (s.find('.content_reply').each(function(){
            	x.push($(this).attr('data-id'))
         	}), i(v, !0), $.post(paper_p.url.load_replies+"?token="+paper_p.token, {comment_id: v.attr('data-id'), sort_by: m(v), reply_ids: JSON.stringify(x)}, function(e){
	            if(e.S == 200){
	               var y = s.find('.content_replies'),
	                   n = y.find('.content_reply[data-type=new]');
	               n.length > 0 ? n.before(e.HT) : y.append(e.HT), r(v.removeClass('spinner-is-loading'), !1)
	            } else v.remove();
         	}))
      	}).on('click', '.btn_cload', function(){
         	var v = $(this),
             	s = v.prev(),
             	x = [];

         	p && q && (p = !1, s.children().each(function(){
            	x.push($(this).attr('data-id'))
         	}), i(v, !0), $.post(paper_p.url.load_comments+"?token="+paper_p.token, {post_id: v.attr('data-id'), sort_by: m(v), comment_ids: JSON.stringify(x)}, function(e){
	            if(e.S == 200){
	               s.append(e.HT), r(v.removeClass('spinner-is-loading'), !1), p = !0
	            } else (v.addClass('hidden'), a = p = !0);
         	}))
      	}).on('click', '.btn_sreplies', function(){
         	var x = $(this),
             	s = x.find('svg'),
             	v = x.siblings('.content_replies, .btn_rload'),
             	y = x.find('.item_streplies');

         	if(x.attr('data-type') == 'hide'){
            	x.attr('data-type', 'show'), y.text(paper_p.word.show), s.removeClass('rotate-180'), v.addClass('hidden');
         	} else f(x, y);
      	}).on('change', '.btn_csortby', function(){
         	var x = $(this),
             	s = function(e, u, x = !0){
               		var s = w(e),
                   		y = s.find('.btn_comment'),
                   		v = s.find('.content_cspinner').toggleClass('hidden').toggleClass('spinner-is-loading').siblings('.content_comments, .btn_cload').toggleClass('hidden');
               	r(e, u), r(y, u), r(y.parent().prev(), u), a && x && (r($(v[1]).removeClass('spinner-is-loading hidden'), !1), a = !1);
               	return s;
             	};
         	q && (q = !1, s(x, !0, !1), $.post(paper_p.url.sort_comments+"?token="+paper_p.token, {post_id: paper_p.post_id, sort_by: x.val()}, function(e){
	            if(e.S == 200){
	               q = !0, s(x, !1).find('.content_comments').html(e.HT)
	            } else {
	               q = !0, s(x, !1), g(e.E, !0, 'red')
	            }
         	}))
      	}).on('click', '.btn_spcomment', function(){
         	$('html').animate({scrollTop: ($('.container_comments[data-id='+$(this).attr('data-id')+']').offset().top - 50)})
      	}).on('click', '.btn_ctchange', function(){
         	var x = $(this),
             	v = w(x),
             	y = x.attr('data-type'),
             	s = v.find('.item_ctsort');
         	y == 'facebook' ? s.addClass('hidden') : s.removeClass('hidden'), x.parent().find('.btn_ctchange.active').removeClass('active').siblings(':not(.item_ctbackground)').addClass('active'), v.find('.items_comments[data-type='+y+']').removeClass('hidden').siblings(':not(.item_notab)').addClass('hidden');
      	}), $('#btn-dnotify').click(g), o.click(function(){
         	var v = $(this),
             	s = v.attr('data-type');
			q && (i(v, !0), $.post(paper_p.url.delete_comment+"?token="+paper_p.token, {comment_id: v.attr('data-id'), type: s}, function(e){
				if(e.S == 200){
				   $(e.DL).remove(), l(), r(v.removeClass('spinner-is-loading'), !1)
				} else {
				   r(v.removeClass('spinner-is-loading'), !1), g(e.E, !0, 'red')
				}
			}))
      	}), $('#btn-ccancel').click(l);
   })(), (function(){
      	var p = $("body"),
          	s = document.body.createTextRange;
      	$(document).on('click', '.btn_cucopy', function(){
         	var x = $(this).attr('data-link'),
             	y,
             	k,
             	o;
         	if(s || window.getSelection){
            	y = $('<div>'), p.append(y.text(x));
	            if(s) {
	               k = document.body.createTextRange(), k.moveToElementText(y[0]), k.select();
	            } else {
	               o = window.getSelection(), k = document.createRange(), k.selectNodeContents(y[0]), o.removeAllRanges(), o.addRange(k);
	            }
         	} else {
            	y = $('<input>'), p.append(y.val(x)), y.select();
         	}
         	document.execCommand("copy"), y.remove(), g(paper_p.word.link_copied_clipboard, !0, 'blue')
      	})
   	})();
})(paper_p);