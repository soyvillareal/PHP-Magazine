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
	var t = $('.content_pnuser[data-id='+paper_m.profile_id+']'),
		p = $('#btn-pmsend'),
		s = $('#content-pnnew'),
		w = function(e, i){
	        return e.attr('disabled', i);
	    };

	(function(){
		var c = $('#content-messages'),
			u = $('#content-pmtext'),
			d = $('#content-mform'),
			e = function(e = !1){
				(r || e) && u.animate({scrollTop: c.height()}, 750)
			},
		    z = function(e){
		    	e.TX && $(e.EL).find('.item_pntuser').text(e.TX).siblings('.item_pncuser').text(e.CA)
		    },
			f = function(u, i){
				var v = c.find('.container_pmdot')
			    v.length > 0 ? v.before(u.HTM) : c.append(u.HTM), u.HTM && e(i)
			},
			h = function(){
				t.removeClass('is_new').find('.content_pninfo').removeClass('hidden')
			},
	    	q = !1,
	    	j = !0,
			n = !0,
			m = !1,
			r = !0;

		u.scrollTop(c.height()).removeClass('pointer-events-none'), q = !0, (function(){
			var r = $('#content-mfreply'),
				a = $('#content-mffiles'),
				b = $('#text-message'),
				i = $('#content-norefor');

			(function(){
				var i = $('#content-mfrefi'),
					g = $('#item-mfrouser'),
					n = $('#item-mfrmessage'),
					o = $('#content-mfinputs'),
					s = $('.disable_before'),
					q = function(e){
						var t = new FormData(),
							v = $('.content_mffile'),
							k = $('.content_mfimage'),
							g = o.children(),
							n = b.val();

						if(g.length > 0){
							g.each(function(i, e){
								var x = $(e).prop('files');
								t.append('file_'+i, x[0]);
							})
						} else if($.trim(n) == ''){
							return b.val(''), e && e.preventDefault(), !1;
						}

						paper_m.settings.nodejs == 'on' && t.append('socket_id', SOCKET.id), t.append('count_files', g.length), t.append('profile_id', paper_m.profile_id), t.append('text', n), t.append('answered_id', y), t.append('type', l), j = !1, w(s, !0), p.addClass('spinner-is-loading'), $.ajax({
							url: paper_m.url.send+"?token="+paper_m.token,
					        type: 'POST',
					        data: t,
					        contentType: !1,
					        processData: !1,
					        success: function(e) {
					            if(e.S == 200) {
						            m = !1, e.CO && ($(e.EL+' .btn_pcdelete').attr('data-id', e.CID).removeClass('unseen hidden'), c.html(''), h()), paper_m.messages_ids.push(e.MID), o.html(''), a.addClass('hidden'), i.addClass('hidden'), r.addClass('hidden'), $(k[0]).addClass('hidden').find('img').attr('src', '').attr('alt', ''), $(v[0]).addClass('hidden').find('span').text(''), a.children(':not(.hidden)').remove(), d.trigger('reset'), b.attr('rows', 1), u.removeAttr('style'), f(e, !0), z(e), w(s, !1), p.removeClass('spinner-is-loading'), j = !0
						        } else {
						            w(s, !1), p.removeClass('spinner-is-loading')
						        }
					        }
						})
					},
					t = !0,
					y = 0,
					l = 'text';

				(function(){
					var h = 0,
						j = '';
					paper_m.enable_msg ? (p.click(q), b.focus(function(){
			         	var v = $(this);
			         	v.val().length == 0 && v.prop('baseScrollHeight', v.prop('scrollHeight'));
			      	}), b.on('keyup, input', function(){
			      		if(!t) return t = !0, !1;
			      		var v = $(this);
			      			v.attr('rows', 1)

			      		var x = v.prop('scrollHeight'),
			      			k = (1 + Math.ceil((x - v.prop('baseScrollHeight')) / 24)),
			      			d = i.height(),
			      			s = d > 0 ? d+21 : d,
			      			o = 162 + s;

			      		k <= 6 && (o = (b.height() + s + (x >= 54 ? x - 2 : x))), u.css('height', 'calc(70vh - '+o+'px)'), v.attr('rows', k > 6 ? 6 : k), e()
					}), b.keyup(function(e){
						var v = $(this).val();
						clearTimeout(h), v.length > 0 ? (v != j && (m = !0), j = v, h = setTimeout(function(){
				      			m = !1;
				      		}, 5000)) : (m = !1), (e.keyCode || e.which) == 13 && !e.shiftKey && (v.length > 0 || o.children().length > 0) && q()
					}), b.keydown(function(e){
						var v = $(this),
							x = e.keyCode || e.which,
							k = e.shiftKey,
							y = v.val().length,
							u = x == 13;

						u && k && (t = !1)
			      		if((u && y == 0) || (u && !k && y > 0)){
			      			e.preventDefault();
			      			return !1;
			      		}
					}), b.focus()) : $('#item-pnsearch').focus(), $('#btn-mfrclose').click(function(){
						y = 0, a.hasClass('hidden') && i.addClass('hidden'), r.addClass('hidden'), n.text(''), g.text(''), u.css('height', 'calc(70vh - '+d.height()+'px)')
					}), $('#btn-close').click(function(){
						$(this).parents('.content-alert').addClass('hidden');
					})
				})(), (function(){
					var m = $('#item-mfrtuser'),
						f = function(e, u = !0){
			            	var e = this == window ? e : $(this),
			                	x = $('.btn_pnmoptions[aria-expanded=true]'),
			                	v = function(i){
			                  		return i.attr('aria-expanded', !JSON.parse(i.attr('aria-expanded')))
			                	};
			            	u && x.length > 0 && v(x), v(e).blur()
			          	},
			          	j = function(){
			            	return $('.btn_pnmoptions[aria-expanded=true]')
			          	};

					$(document).on('click', '.btn_pmtreply', function(){
						var v = $(this),
							h = v.attr('data-type'),
							x = v.parents('.content_pmtmessage'),
							k = g.parent(),
							s = v.parents('.content_pmt'+(h == 'text' ? 'message' : h)).find('.item_pmtutext'),
							c = x.find('.item_pmtext').text();

						if(['file', 'image'].indexOf(h) != -1){
							c = h == 'file' ? paper_m.word.attached_file : paper_m.word.image
						}

						if(s.length > 0){
							k.removeClass('hidden'), g.text(s.text()), m.addClass('hidden')
						} else {
							k.addClass('hidden'), m.removeClass('hidden')
						}

						n.text(c), e(), b.focus(), a.hasClass('hidden') && i.removeClass('hidden'), r.removeClass('hidden'), y = v.attr('data-id'), u.css('height', 'calc(70vh - '+d.height()+'px)'), l = h
					}).on('click', '.btn_pnmoptions:not(#btn-pnnclose)', f).click(function(e){
			         	var x = j();
			         	!$(e.target).is('.content_pnmoptions, .content_pnmoptions *') && x.length > 0 && f(x, !1)
			      	}), (function(){
			      		var q = $('body'),
							u = $('#alert-cdelete'),
			      			a = $('#btn-cdelete'),
			      			d = $('#alert-cblock'),
							h = $('#btn-cblock'),
			      			z = $('#alert-mdelete'),
							t = $('#btn-mdelete'),
			      			b = $('#alert-mmdelete'),
							g = $('#btn-mmdelete'),
							p = function(e){
								e.addClass('hidden'), q.removeClass('overflow-hidden')
							},
				          	r = function(e = !1, x, y, z){
				          		var o = j()
			      				y.attr('data-id', x.attr('data-id')), e && y.attr('data-type', x.attr('data-type')), z.removeClass('hidden'), q.addClass('overflow-hidden'), o.length > 0 && f(o, !1)
			      			},
			      			k = function(){
			      				$(z.find('.item_dmtype')[0]).prop('checked', !0)
			      			},
			      			n = function(e){
			      				w(e.removeClass('spinner-is-loading'), !1)
			      			},
			      			l = function(e){
			      				w(e.addClass('spinner-is-loading'), !0)
			      			};
			      		$(document).on('click', '.btn_pmtdelete', function(){
							r(!0, $(this), t, z)
						}).on('click', '.btn_pmtmdelete', function(){
							r(!0, $(this), g, b)
						}).on('click', '.btn_pcdelete', function(){
							r(!0, $(this), a, u)
				      	}).on('click', '.btn_pcblock', function(){
							r(!0, $(this), h, d)
				      	}), $('#btn-cdcancel').click(function(){
				      		p(u)
				      	}), $('#btn-cbcancel').click(function(){
				      		p(d)
				      	}), $('#btn-mcancel').click(function(){
				      		p(z), k()
				      	}), $('#btn-mmcancel').click(function(){
				      		p(b)
				      	}), h.click(function(){
				      		var v = $(this),
								s = v.attr('data-id');
							l(v), $.post(paper_m.url.block+"?token="+paper_m.token, {profile_id: s}, function(e){
								if(e.S == 200){
									window.location.reload();
								} else n(v)
							})
				      	}), a.click(function(){
				      		var v = $(this),
								s = v.attr('data-id');
							l(v), $.post(paper_m.url.delete_chat+"?token="+paper_m.token, {chat_id: s}, function(e){
								if(e.S == 200){
									window.location.reload()
								} else n(v)
							})
				      	}), t.click(function(){
							var v = $(this),
								x = v.attr('data-type'),
								s = v.attr('data-id'),
								u = z.find('.item_dmtype:checked').val(),
								o = 'message',
								y;
							l(v), $.post(paper_m.url.delete_both_message+"?token="+paper_m.token, {id: s, for: u, type: x}, function(e){
								if(e.S == 200){
									x != 'text' && (o = x), y = $('.content_pmt'+o+'[data-id='+s+']'), k(), p(z), n(v);
									if(u == 'all'){
										c.find('.item_pmtmmessage[data-id='+s+'][data-type='+x+']').replaceWith(e.HT), y.replaceWith(e.HT)
									} else {
										if(e.DA == !0 && o != 'message'){
											var m = y.parents('.content_pmtmessage');
											m.next('.item_pmttime').remove();
											m.remove();
										} else {
											var d = y.next(),
												q = d.is('.content_pmtimages'),
												m = q ? d : y;
											m.next('.item_pmttime').remove(), q && x != 'text' && d.remove(), y.remove();
										}
										c.find('.item_pmtmessage[data-id='+s+'][data-type='+x+']').remove()
									}
								} else n(v)
							})
						}), g.click(function(){
							var v = $(this),
								x = v.attr('data-type'),
								s = v.attr('data-id'),
								o = 'message';
							l(v), $.post(paper_m.url.delete_me_message+"?token="+paper_m.token, {id: s, type: x}, function(e){
								if(e.S == 200){
									var o = x != 'text' ? x : o,
										u = '[data-id='+s+']',
			            				a = $('.content_pmt'+o+u+', .content_pmtmessage'+u+', .item_pmtmessage'+u+'[data-type='+x+']');

									if(e.DA == !0 && o != 'message'){
										var m = a.parents('.content_pmtmessage');
										m.next('.item_pmttime').remove();
										m.remove();
									} else {
										var d = a.next(),
											k = d.is('.content_pmtimages'),
											m = k ? d : a;
										m.next('.item_pmttime').remove(), k && x != 'text' && d.remove(), a.remove();
									}
									p(b), n(v);;
								} else n(v)
							})
						})
			      	})()
				})();
			})(), (function(){

				var j = $('#btn-mffile'),
					g = $('#content-mfrefi'),
					c = $('#content-mfinputs'),
					b = $('#btn-pmsend'),
					f = function(z, i = !1){
						var u = z.prop('files');
				        if (u && u[0]){
					        var p = new FileReader(),
					        	v = function(u){
					        		var l = $($('.content_mfimage')[0]),
										y = $('.item_mffile'),
										x = l;
					        		i && (x = l.clone(), l.val('')), x.removeClass('hidden').find('img').attr('src', u).attr('alt', u[0].name), y.length > 0 ? x.insertBefore(a.children().first()) : i && a.prepend(x), w(j, !1).removeClass('spinner-is-loading'), s(z, 'image'), e(), b.focus()
					        	};
					        if(p && p.readAsDataURL){
					            p.readAsDataURL(u[0]), $(p).on('load', function(){
					                v(p.result)
					            })
					        } else if(window.URL.createObjectURL){
					            v(window.URL.createObjectURL(u[0]))
					        }
				        }
				        return !1;
					},
					q = function(u, i = !1){
						var h = $($('.content_mffile')[0]),
							y = $('.item_mffile'),
							x = h;
						i && (x = h.clone(), h.val()), x.removeClass('hidden').find('span').text(u[0].name), y.length > 0 ? x.insertBefore(a.children().first()) : i && a.prepend(x), e()
					},
					s = function(e, x){
						e.addClass('is_'+x), a.removeClass('hidden'), g.removeClass('hidden'), u.css('height', 'calc(70vh - '+d.height()+'px)'), b.focus()
					};

				j.click(function(){
					var x = c.children(),
						u = function(){
							c.prepend($('<input class="item_mffile hidden" type="file"/>').trigger('click'))
						};
					if(x.length > 0){
						var o = x.first();
						if(o.prop('files').length > 0){
							u();
						} else {
							o.trigger('click');
						}
					} else u();
				});

				$(document).on('change', '.item_mffile', function(e){
					var v = $(this),
						k = v.prop('files');

					if(k[0].size > paper_m.file_size_limit){
						$('#alert-limit').removeClass('hidden');
						return e.preventDefault(), !1;
					}

					c.find('.item_mffile').length == 0 && v.next().children().replaceWith('<svg class="icon-z vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title>'+paper_m.word.more_files+'</title><path fill="currentColor" d="M13,13 L13,20 C13,20.5522847 12.5522847,21 12,21 C11.4477153,21 11,20.5522847 11,20 L11,13 L4,13 C3.44771525,13 3,12.5522847 3,12 C3,11.4477153 3.44771525,11 4,11 L11,11 L11,4 C11,3.44771525 11.4477153,3 12,3 C12.5522847,3 13,3.44771525 13,4 L13,11 L20,11 C20.5522847,11 21,11.4477153 21,12 C21,12.5522847 20.5522847,13 20,13 L13,13 Z"></path></svg>');

					if(['image/png', 'image/jpeg', 'image/gif'].indexOf(k[0].type) != -1){
						w(j, !0).addClass('spinner-is-loading');
						if($('.item_mffile.is_image').length > 0){
							f(v, !0)
						} else {
							f(v)
						}
					} else {
						if($('.item_mffile.is_file').length > 0){
							q(k, !0)
						} else {
							q(k)
						}
						s(v, 'file')
					}
				}).on('click', '.btn_mffile', function(){
					var p = c.children().first();
					if(p.prop('files').length == 0){
						p.remove();
					}
					var v = $(this),
						y = a.children(':not(.hidden)'),
						x = v.parents('.content_mfimage, .content_mffile'),
						k = y.index(x),
						s = c.children().length,
						q = $($('.item_mffile')[k]),
						t = c.find('.item_mffile.is_image'),
						o = c.find('.item_mffile.is_file');

					if(t.length == 1 || o.length == 1){
						x.addClass('hidden')
						if(t.length == 1){
							x.find('img').attr('src', '').attr('alt', '')
						}
						if(o.length == 1){
							x.find('span').text('')
						}
					}
					if((t.length > 1 && q.hasClass('is_image')) || (o.length > 1 && q.hasClass('is_file'))){
						x.remove()
					}
					if(s == 1){
						r.hasClass('hidden') && g.addClass('hidden'), a.addClass('hidden')
					}
					q.remove(), u.css('height', 'calc(70vh - '+d.height()+'px)')
				})
			})();
		})(), (function(){
			var p = $('#content-pnchats'),
				o = $('#content-pmntspinner'),
				k = $('#content-pnuspinner'),
				g = $('#container-pnchats'),
				b = $('#content-norefor'),
				d = $('#item-pnsearch'),
				w = $('#btn-mdown'),
				a = d.prev(),
				i = function(){
					r = !0, w.removeAttr('style')
				},
				y = '';

			u.scroll(function(){
				var l = c.height();

		    	if((u.scrollTop() + $(window).height()) < l){
		    		r = !1, w.css('bottom', 15)
		    	} else i()

		     	if(q && u.scrollTop() <= 60){
		     		o.removeClass('hidden'), $.post(paper_m.url.fetch+"?token="+paper_m.token, {profile_id: paper_m.profile_id, messages_ids: JSON.stringify(paper_m.messages_ids), type: 'first'}, function(e){
				        if(e.S == 200) {
				        	var h = c.height();
				            u.addClass('pointer-events-none'), o.addClass('hidden'), c.prepend(e.HTM), e.MIDS && (paper_m.messages_ids = e.MIDS), u.animate({scrollTop: (c.height() - h)}, 100, function(){
				            	q = !0, u.removeClass('pointer-events-none')
				            })
				        } else o.addClass('hidden');
				    }), q = !1
		     	}
		    }), g.scroll(function(){
		    	var l = p.height();
		    	if(n && (g.scrollTop() + g.height()) >= (l - ((l/100)*10))){
		     		n = !1, k.removeClass('hidden'), $.post(paper_m.url.fetch+"?token="+paper_m.token, {profiles_ids: JSON.stringify(paper_m.profiles_ids), type: 'users', keyword: y}, function(e){
			            if(e.S == 200) {
			               n = !0, k.addClass('hidden'), k.before(e.HTU), e.UIDS && (paper_m.profiles_ids = e.UIDS)
			            } else k.addClass('hidden');
			        })
		     	}
		    }), w.click(function(){
		    	u.scrollTop(c.height()), i();
		    }), d.on('keyup, keydown, input', function(){
		      	var v = d.val();
		      	paper_m.profiles_ids = [], j = n = !1, a.addClass('spinner-is-loading'), $.post(paper_m.url.search_chats+"?token="+paper_m.token, {profile_id: paper_m.profile_id, profiles_ids: JSON.stringify(paper_m.profiles_ids), keyword: v}, function(e){
				    if(e.S == 200) {
				        $('li').remove('.content_pnuser'), b.addClass('hidden'), p.removeClass('hidden'), a.removeClass('spinner-is-loading'), s.after(e.HTU), e.UIDS && (paper_m.profiles_ids = e.UIDS), y = e.KW, j = n = !0
				    } else if(e.S == 204){
				        y = '', p.addClass('hidden'), b.html(e.HT).removeClass('hidden'), y = '', a.removeClass('spinner-is-loading')
				    } else {
				        y = '', b.addClass('hidden'), p.removeClass('hidden'), a.removeClass('spinner-is-loading')
				    }
				})
		    });
			
			(function(){
				var q = function(e){
						if(e.S == 200){
							if(e.S == 200){
								for (var i = 0; i < e.TPS.length; i++) {
									var el = e.TPS[i];
									$(el['EL']).find('.item_pntuser').text(el['TX']);
								}
							}
							if(e.DL){
								r();
							}
						}
					},
					w = function(e, x){
						var d = c.find('.container_pmdot');
						x ? (d.length == 0 && c.append(e.HTT), u.scrollTop(c.height())) : (d.remove(), m = !1)
					},
					r = function(){
						c.find('.container_pmdot').remove();
					};

				if(paper_m.settings.nodejs == 'off'){
					setInterval(function(){
						var g = p.find('.content_nomgs'),
							x = paper_m.messages_ids,
							k = paper_m.profiles_ids,
							l = {profile_id: paper_m.profile_id, type: 'last', keyword: y, typing: m};
						j && (x.length > 0 && (l = {profile_id: paper_m.profile_id, messages_ids: JSON.stringify(x), type: 'last', keyword: y, typing: m}), k.length > 0 && (l = {profile_id: paper_m.profile_id, last_cupdate: paper_m.last_cupdate, profiles_ids: JSON.stringify(k), messages_ids: JSON.stringify(x), type: 'last', keyword: y, typing: m}), $.post(paper_m.url.fetch+"?token="+paper_m.token, l, function(e){
							if(e.S == 200) {
								w(e, !e.HTM && e.HTT), e.UIDS && (paper_m.profiles_ids = e.UIDS, paper_m.last_cupdate = e.LCU), e.HTM && c.find('.content_nomgs').length > 0 && c.html(''), e.HTU && g.length > 0 && g.remove(), !t.hasClass('is_new') && s.after(e.HTU), e.MIDS && (paper_m.messages_ids = e.MIDS, h()), f(e, !1), e.US && $.each(e.US, function(i, u){
									   z(u), u.LU && $(u.EL).find('a').addClass('unseen');
								}), e.MS && $.each(e.MS, function(i, u){
									var x = '[data-id='+u['ID']+']',
										o = $('.content_pmtmessage'+x+', .item_pmtmmessage'+x);
									   !o.hasClass('content-pmtwdeleted') && o.replaceWith(u['HTG'])
								}), e.FS && $.each(e.FS, function(i, u){
									var x = $('.content_pmt'+u['TP']+'[data-id='+u['ID']+']');
									!x.hasClass('content-pmtwdeleted') && x.replaceWith(u['HTF'])
								});
							}
						}))
					}, 2000), setInterval(function(){
						$.post(paper_m.url.delete_my_typings+"?token="+paper_m.token, {profile_id: paper_m.profile_id}, q)
					}, 20000)
				} else {
					var l = function(e){
							if(e.S == 200){
								e.UID && (paper_m.profiles_ids.push(e.UID), paper_m.last_cupdate = e.LCU), !t.hasClass('is_new') && s.after(e.HTU), z(e), h();
							}
						},
						x = function(e){
							if(e.S == 200){
								if(e.PID == paper_m.profile_id){
									e.MID && paper_m.messages_ids.push(e.MID), c.find('.content_nomgs').length > 0 && c.html(''), f(e, !0);
									if(e.DLD != undefined){
										r(), SOCKET.emit('setInmseen', {message_id: e.MID})
									}
								}
							}
						};
	
					SOCKET.on('setOutichat', l), SOCKET.on('setOutochat', l), SOCKET.on('setOutomessage', x), SOCKET.on('setOutimessage', x), SOCKET.on('setOutupchat', function(e){
						z(e), e.LU && $(e.EL).find('a').addClass('unseen')
					}), setInterval(function(){
						SOCKET.emit('setIntyping', {profile_id: paper_m.profile_id, typing: m});
					}, 2000), SOCKET.on('setOutdMytyping', q), SOCKET.on('setOuttyping', function(e){
						if(e.S == 200){
							if(e.PID == paper_m.profile_id){
								w(e, !e.DL)
							}
						}
					}), SOCKET.on('setOutttyping', function(e){
						if(e.S == 200){
							$(e.EL).find('.item_pntuser').text(e.TX)
						}
					});

				}
			})();
		})();
	})(), (function(){
		var m = $('#container-messages'),
			j = $('#container-pmhead-one'),
			a = $('#container-pmhead-two'),
			q = $('#container-pnmessage'),
			o = $('#content-pmsspinner'),
			k = $('#content-pmsearch'),
			c = $('#btn-pmnew svg'),
			u = $('#item-pmsearch'),
			g = function(){
				var n = '<title>'+paper_m.word.search+'</title><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>';
				a.hasClass('hidden') ? (n = '<title>'+paper_m.word.close+'</title><path fill="none" stroke="currentColor" stroke-width="2" d="M3,3 L21,21 M3,21 L21,3"></path>', u.focus()) : (u.val('').blur(), k.html('')), c.html(n), m.toggleClass('hidden'), j.toggleClass('hidden'), a.toggleClass('hidden'), s.toggleClass('hidden'), t.find('a').toggleClass('active'), f();
			},
			z = function(){
				o.addClass('hidden').next().removeClass('hidden')
			},
			f = function(){
				q.toggleClass('hide-mobile').next().toggleClass('hide-mobile'), window.history.pushState({state:'new'}, '', paper_m.url.messages)
			},
			y = function(e){
				u.attr('aria-expanded', e)
			},
			r = paper_m.enable_msg;

		u.keyup(function(){
			o.removeClass('hidden'), k.addClass('hidden'), $.post(paper_m.url.search_users+"?token="+paper_m.token, {keyword: u.val()}, function(e){
		        if(e.S == 200) {
		            z(), y(!0), k.html(e.HT)
		        } else if(e.S == 204){
		            z(), y(!1), k.html(e.HT)
		        } else {
		            z(), y(!1), k.html('')
		        }
		    })
		}), $('.btn_mback').click(function(){
			r ? f() : g(), r = !1;
		}), $('#btn-pmnew, #btn-pnnclose').click(g)
	})();
})(paper_m);