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
  var f = function(e, u){
        return e.attr('disabled', u);
      },
      s = function(e, u, y){
        e ? e.removeClass('border-red boxshadow-red') : y.removeClass('background-red'), u && u.text('')
      },
      k = function(e, u, y, r, w = !0){
        e ? e.addClass('border-red boxshadow-red') : w && r.addClass('background-red'), u && u.text(y), r && g(r)
      },
      g = function(e){
        f(e, !1).removeClass('spinner-is-loading')
      },
      b = function(e, u){
        var r = e.parents('.entry-carousel'),
            w = r.length > 0 ? r : e.parents('.content-field');
        if(w.find('.thumbnail_file').length > 0){
          w.find('.content-placeholder').addClass('hidden'), w.find('.content-image').removeClass('hidden'), w.find('.item_image').html('<img src="'+u+'" alt="'+paper_ep.word.preview+'"/>')
        } else {
          var g = w.find('.content-carrusel'),
              y = g.find('.content-pbcarousel'),
              p = g.find('.content_captions'),
              x = w.find('.carousel_file'),
              v = x.attr('disabled') ? 'text' : 'file',
              c = !e.hasClass('btn_gimage') ? w.parent() : e.parents('.content-alert-popup'),
              z = c.find('.carousel_'+v),
              j = g.find('.content_files'),
              o = (j.children().length+1);
          s(g, g.siblings('.text-error')), g.find('div[role=progressbar]').attr('aria-valuemax', o), g.find('div[role=group]').attr('aria-label', '1 '+paper_ep.word.of+' '+o), g.find('div[role=progressbar]').attr('aria-valuenow', 1), o > 1 && g.find('.content-ecbuttons').removeClass('hidden'), w.find('.content-thumbnail').addClass('hidden'), f(z.clone(), !0).removeAttr('class').addClass('item_c'+v).prependTo(j), f(x, !1), z.val(''), g.removeClass('hidden').find('.entry_cimage').attr('src', u), y.children().removeClass('selected'), y.prepend('<button class="btn_crepos item-pbcarousel btn-noway padding-5 border-grely inhover-button animation-ease3s selected" data-image="'+u+'" type="button"><div class="item-pbcimage w-45px h-45" style="background-image: url('+u+');"></div></button>'), p.children().addClass('hidden'), p.prepend('<input class="item-input item_caption position-absolute z-index-1 no_validate w-100 h-30 color-wwhite padding-b5 padding-l10 padding-r10 text-center border-bottom border-grely border-focus-blue animation-ease3s sizing-box" type="text" placeholder="'+paper_ep.word.description+'">')
        }
      },
      q = function(){
        var w = $('#btn-post');
        $('.item_key:not(.no_validate, :disabled), .item_change:not(.no_validate, :disabled)').each(function(){
          if($(this).val() == '') return f(w, !0), !1; else f(w, !1);
        })
      },
      l = !0;

    (function(){
      var v = $('.post_sources'),
          r = $('.btn_asource'),
          c = (v.first().children().length+1),
          z = (v.last().children().length+1);
      r.click(function(){
        var w = $(this),
            h = w.attr('data-type'),
            o = v.first().children().length,
            z = v.last().children().length,
            j = h == 'post' ? (o+1) : (z+1);
        l && (j < paper_ep.settings.number_of_fonts ? (w.parent().next().find('.post_sources').append('<div class="display-flex direction-row align-center w-100 h-30 margin-t10"><span class="item_count font-bigly icon-z color-blackly margin-l5">'+j+'</span><div class="display-flex w-100"><input class="item-input item_key disable_before no_validate w-100 background-white color-black margin-r10 padding-r10 border-bottom border-black border-focus-blue animation-ease3s sizing-box" type="text" placeholder="'+paper_ep.word.name+'"><input class="item-input item_key disable_before no_validate w-100 background-white color-black padding-r10 border-bottom border-black border-focus-blue animation-ease3s sizing-box" type="text" placeholder="'+paper_ep.word.link+'"></div><button class="btn_sdelete area_validate display-flex btn-noway background-red margin-l5 border-rlow boxshadow-grey hover-button animation-ease3s" type="button" data-type="'+h+'"><svg class="icon-z color-wwhite" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M 10.091 16.688 L 10.091 9.813 C 10.091 9.721 10.061 9.646 10.001 9.588 C 9.942 9.529 9.866 9.5 9.773 9.5 L 9.136 9.5 C 9.044 9.5 8.967 9.529 8.908 9.588 C 8.848 9.646 8.818 9.721 8.818 9.813 L 8.818 16.688 C 8.818 16.779 8.848 16.854 8.908 16.912 C 8.967 16.971 9.044 17 9.136 17 L 9.773 17 C 9.866 17 9.942 16.971 10.001 16.912 C 10.061 16.854 10.091 16.779 10.091 16.688 Z M 12.636 16.688 L 12.636 9.813 C 12.636 9.721 12.607 9.646 12.547 9.588 C 12.487 9.529 12.411 9.5 12.318 9.5 L 11.682 9.5 C 11.589 9.5 11.513 9.529 11.453 9.588 C 11.393 9.646 11.364 9.721 11.364 9.813 L 11.364 16.688 C 11.364 16.779 11.393 16.854 11.453 16.912 C 11.513 16.971 11.589 17 11.682 17 L 12.318 17 C 12.411 17 12.487 16.971 12.547 16.912 C 12.607 16.854 12.636 16.779 12.636 16.688 Z M 15.182 16.688 L 15.182 9.813 C 15.182 9.721 15.152 9.646 15.092 9.588 C 15.033 9.529 14.956 9.5 14.864 9.5 L 14.227 9.5 C 14.134 9.5 14.058 9.529 13.999 9.588 C 13.939 9.646 13.909 9.721 13.909 9.813 L 13.909 16.688 C 13.909 16.779 13.939 16.854 13.999 16.912 C 14.058 16.971 14.134 17 14.227 17 L 14.864 17 C 14.956 17 15.033 16.971 15.092 16.912 C 15.152 16.854 15.182 16.779 15.182 16.688 Z M 9.773 7 L 14.227 7 L 13.75 5.857 C 13.704 5.799 13.647 5.763 13.581 5.75 L 10.429 5.75 C 10.363 5.763 10.306 5.799 10.26 5.857 Z M 19 7.313 L 19 7.938 C 19 8.029 18.97 8.104 18.911 8.162 C 18.851 8.221 18.775 8.25 18.682 8.25 L 17.727 8.25 L 17.727 17.508 C 17.727 18.048 17.572 18.515 17.26 18.909 C 16.948 19.303 16.574 19.5 16.136 19.5 L 7.864 19.5 C 7.426 19.5 7.052 19.31 6.74 18.929 C 6.429 18.548 6.273 18.087 6.273 17.547 L 6.273 8.25 L 5.318 8.25 C 5.225 8.25 5.149 8.221 5.089 8.162 C 5.03 8.104 5 8.029 5 7.938 L 5 7.313 C 5 7.221 5.03 7.146 5.089 7.088 C 5.149 7.029 5.225 7 5.318 7 L 8.391 7 L 9.087 5.369 C 9.186 5.128 9.365 4.923 9.624 4.754 C 9.882 4.585 10.144 4.5 10.409 4.5 L 13.591 4.5 C 13.856 4.5 14.118 4.585 14.376 4.754 C 14.635 4.923 14.814 5.128 14.913 5.369 L 15.609 7 L 18.682 7 C 18.775 7 18.851 7.029 18.911 7.088 C 18.97 7.146 19 7.221 19 7.313 Z"></path></svg></button></div>'), (h == 'post' ? c++ : z++)) : f(w, !0))
      }), $(document).on('click', '.btn_sdelete', function(e){
        var w = $(this),
            h = w.attr('data-type'),
            d = h == 'post',
            u = w.parents('.post_sources'),
            x = u.children(),
            p = u.find('.btn_sdelete').index(w)+1;
        l && (x[p].remove(), x.length == (p+1) ? (d ? c-- : z--) : ((d ? c = 2 : z = 2), u.find('.item_count').each(function(e, i){
          $(i).text(e+2), (d ? c++ : z++)
        })), f(r, !1))
      })
    })(), (function(){
      var o = $("#content-tags"),
          a = $('#tags'),
          r = $('#new-tag'),
          m = $('#search-tags'),
          c = $('#tag-label'),
          j = 'margin-t5 margin-b5 margin-r5 padding-5',
          i = [],
          n = !1,
          d = function(e){
            e.removeClass("item-tag-active")
          },
          x = function(){
            $('.item_tag').length == 0 && (!r.is(':focus') && c.hasClass('active') && c.removeClass('active'), r.parent().removeClass(j)), !r.is(':focus') && c.removeClass('focus')
          },
          h = function(e) {
            var p = $('.item_tag'),
                w = r.val(),
                u = e ? e : w;
            if(u === '' || i.length >= paper_ep.settings.number_labels) return !1;
            d(p); 
            if(i.indexOf(u) != -1) return $(p[i.indexOf(u)]).addClass("item-tag-active"), !1;
            $('<li class="item_tag display-block position-relative background-blue '+j+' border-rlow border-all"><label class="tag_label color-black"></label><button class="tag_close btn-noway color-black hover-button animation-ease3s animate-tab-button"><svg class="icon-y vertical-sub" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill="none" stroke="currentColor" stroke-width="2" d="M7,7 L17,17 M7,17 L17,7"/></svg></button></li>').insertBefore(o.find(".tag-new")).find('.tag_label').text(u), r.val('').parent().addClass(j), i.push(u), a.val(i.toString()), q(), z(), s(o, o.next());
          },
          z = function(){
            m.addClass('hidden').html('')
          };
      $('.tag_label').each(function(){
        i.push($(this).text())
      }), a.val(i.toString()), r.on('cut', z).keydown(function(e) {
        var u = $('.item_tag'),
            p = u.last(),
            y = e.which || e.keyCode,
            v = r.val();

        if(!l) return !1;
        d(u) 
        if (y == 8 && v === ''){
          n ? (i.pop(), a.val(i.toString()), p.remove(), x(), q()) : (n = !0, p.addClass("item-tag-active"))
        } else {
          n = !1, m.html('');
        }
        if(y === 188 && e.shiftKey === !1 || y === 13 || y == 9 && v !== '') e.preventDefault(), h()
      }).keyup(function(){
        l && (m.addClass('hidden'), r.val() != '' && $.post(paper_ep.url.get_tags+"?token="+paper_ep.token, {'search': r.val(), 'tags': JSON.stringify(i)}, function(e){
                  e.S == 200 && e.TG === r.val() && m.removeClass('hidden').html(e.HT)
                }))
      }).blur(function(e){
        var w = $(e.relatedTarget),
            v = r.val();
        if(!l) return !1;
        if(w.is('.btn_tag')){
          h($.trim(w.text()))
        } else {
          if(v !== ''){
            if(i.indexOf(v) != -1) return d($('.item_tag')), x(), m.addClass('hidden'), !1;
            h(), x()
          } else x();
        }
      }).focus(function(){
        l && (c.addClass('active focus'));
      }), o.click(function(e){
        l && ($(e.target).is('UL') && r.focus())
      }), $(document).click(function(e){
        if(!l) return !1;
        if($(e.target).is('#content-tags, #content-tags *') && m.html() != '') m.removeClass('hidden');
      }).on('click', '.tag_close', function(){
        var w = $(this).parents('.item_tag');
        l && (i.splice($('.item_tag').index(w), 1), a.val(i.toString()), w.remove(), x(), q())
      })
    })(), (function(){
      $(document).on('keyup, input', '.item_key', function(){
        var w = $(this),
            o = w.val();
        if(w.siblings('.btn_gvideo').length > 0){
          var z = w.parent().next(),
              a = z.find('.youtube'),
              c = z.find('.dailymotion'),
              d = z.find('.vimeo'),
              t = z.find('.twitch');
            a.removeClass('active'), c.removeClass('active'), d.removeClass('active'), t.removeClass('active'), o.match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/) ? a.addClass('active') : (o.match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/) ? c.addClass('active') : o.match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/) ? d.addClass('active') : (o.match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?twitch\.tv\/videos\/([0-9]+)$/) && t.addClass('active')))
        }
        o != '' ? (w.addClass('active'), s(w, w.next('.text-error'))) : w.removeClass('active'), q()
      }).on('change', '.item_change', function(){
        var w = $(this),
            z = w.prop('type');

        if(z == 'file'){
          var x = w.prop('files');
          if (x && x[0]) {
            var p = new FileReader(),
                u = w.parents('.content-thumbnail'),
                r = u.find('.item-placeholder'),
                h = u.find('.text-error');
            if(p && p.readAsDataURL){
              p.readAsDataURL(x[0]), $(p).on('load', function(){
                b(u, p.result), s(r, h)
              })
            } else if(window.URL.createObjectURL){
              b(u, window.URL.createObjectURL(x[0])), s(r, h)
            }
          }
        }
        q()
      })
    })(), (function(){
      var d = $('.btn_aentry'),
          j = function(){
            $('.text_simditor').each(function(e = !1){
              var p = $(this).parents(".entry");
              if(p.find(".simditor").length == 0){
                Simditor.locale = paper_ep.lang;
                var r = new Simditor({
                  textarea: $(this),
                  toolbar: ['title', 'bold', 'italic', 'underline', 'strikethrough', 'fontScale', '|', 'ol', 'ul', 'blockquote', '|', 'link', 'indent', 'outdent', 'alignment'],
                  pasteImage: !0,
                  toolbarFloat: !1
                });
                r.on('valuechanged', function(){
                  this.sync(), q(), s(this.el, this.el.next())
                  var n = this.getValue().split('<p>');
                  if(n.length === 6){
                    var m = $('<div>').html(n[5]);
                    if(m.text().length == 1) c(p)
                  }
                }), e && r.focus(e);
              }
            })
          },
          a = function(e, u, y){
            u.addClass("hidden").next().addClass('hidden'), u.prev().html(y)
          },
          o = function(e = '', i = !1){
            clearTimeout(I), $('#content-lnotify').css((paper_ep.dir == 'rtl' ? 'right' : 'left'), (i ? 20 : '')).find('p').text(e), I = setTimeout(function(){
              o()
            }, 5000);
          },
          c = function(e){
            var u = $('#content-pposts .content_rbody');
            $(u[$('.entry[data-type=text]').index(e)]).length > 0 && (h.pop(), u.last().remove(), h.length == 0 && $('#btn-addrb').removeClass('hidden'));
          },
          I = 0,
          h = paper_ep.recobo_ids;

      (function(){
        var a = $('#search-collaborators'),
            x = $('#btn-addcp'),
            t = $('#btn-cpsearch'),
            d = $('#content-susers'),
            l = $('#content-ausers'),
            h = paper_ep.collaborators_ids;

        x.click(function(){
          a.focus()
        }), a.keyup(function(){
          var p = a.val();
          p.length > 0 ? f(t, !1) : f(t, !0), $.post(paper_ep.url.ecu_search+'?token='+paper_ep.token, {keyword: p, user_ids: JSON.stringify(h)}, function(e){
            if(e.S == 200){
              d.html(e.HT)
            } else {
              d.html(e.HT ? e.HT : '')
            }
          })
        }), $(document).on('click', '.btn_putcollab', function(){
          var n = $(this);
          f(n, !0).addClass('spinner-is-loading'), $.post(paper_ep.url.push_user+'?token='+paper_ep.token, {push_id: n.attr('data-id'), user_ids: JSON.stringify(h)}, function(e){
            if(e.S == 200){
              l.prepend(e.HT), h.push(e.ID), n.remove(), a.val(''), x.addClass('hidden'), g(n), l.sortable("refresh");
            } else {
              o(e.E, !0), g(f(n, !1));
            }
          })
        }).on('click', '.btn_dcollab', function(){
          var n = $(this).parents('.content-cpost');
          h.splice(h.indexOf(parseInt(n.attr('data-id'))), 1), n.remove(), h.length == 0 && x.removeClass('hidden');
        }), l.sortable({
          revert: !0,
          nested: !1,
          vertical: !1,
          forcePlaceholderSize: !0,
          placeholder: 'user-placeholder w-100px h-100px background-grely margin-l10 border-rall border-grely boxshadow-grey'
        })
      })(), (function(){
        var t = $('#alert-delete'),
            x = function(e){
              var n = $('.btn_popup[aria-expanded=true] ~ .content-alert-popup'),
                  w = n.parents('.content-thumbnail'),
                  r = (w.length > 0 ? w : n.parents('.entry-carousel').find('.content-thumbnail')).find('.item-file'),
                  y = n.find('.thumbnail_text');
              f(n.prev(), !1).attr('aria-expanded', !1), r.val() != '' || e && f(r, !1), r.removeClass('hidden'), n.addClass('hidden').siblings('.item-placeholder').removeClass('active'), f(y, !0);
            },
            m = function(){
              t.addClass('hidden'), $('body').removeClass('overflow-hidden')
            },
            z;
        $('#btn-acancel').click(m), $('#btn-delete').click(function(){
          l && (c(z), z.remove(), m(), q(), $('.entries .entry[data-type=video]').length == 0 && $('#type').val('normal'))
        }), $(document).on('click', '.btn_adelete', function(){
          l && (z = $(this).parents('.entry'), t.removeClass('hidden'), $('body').addClass('overflow-hidden'))
        }).on('click', '.btn_popup', function(){
          var n = $(this),
              w = n.siblings('.item-file'),
              r = w.length > 0 ? w : n.parents('.entry-carousel').find('.content-thumbnail .item-file');
          l && (x(), n.attr('aria-expanded', !0).blur().next().removeClass('hidden').next().addClass('active'), f(r, !0).addClass('hidden'), n.next().find('.text-error').text(''), f(f(n, !0).next().find('input'), !1).focus())
        }).on('click', '.btn_gicancel', function(){
          var n = $(this).parents('.content-placeholder');
          x(!0)
        }).click(function(e){
          var n = $(e.target);
          !n.is('.btn_popup[aria-expanded=true], .btn_popup[aria-expanded=true] *, .btn_popup[aria-expanded=true] ~ .content-alert-popup, .btn_popup[aria-expanded=true] ~ .content-alert-popup *') && x(!0), n.is(t) && m()
        }).on('click', '.btn_gimage', function(){
          var n = $(this),
              w = n.parents('.content-alert-popup'),
              y = w.find('input'),
              u = w.find('.text-error'),
              r = w.parents('.content-thumbnail');
          l && (s(f(y, !0), u), f(n, !0).addClass('spinner-is-loading'), $.post(paper_ep.url.get_image+"?token="+paper_ep.token, {'url': y.val()}, function(e){
                      if(e.S == 200){
                        w.addClass('hidden'), x(), b(n, e.IM), g(n), q(), s(r.find('.item-placeholder'), r.find('.text-error'))
                      } else {
                        k(f(y, !1), u, e.E, n)
                      }
                  }))
        }).on('click', '.btn_cadd', function(){
          $(this).parents('.content-carrusel').next().find('.carousel_file').trigger('click');
        })
      })(), (function(){
        $('.btn_post').click(function(e){
          e.preventDefault();
          var y = $(this),
              w = [],
              p = $('.area_validate'),
              j = $('.post_sources'),
              m = $('#post-right'),
              u = m.find('.thumbnail_file')[0].files,
              n = $('.simditor-body'),
              i = $('.disable_before, .extra_shadow, .simditor'),
              a = i.parents('.content-field'),
              t = new FormData(), 
              c = function(y){
                var m = [],
                    d = 1;
                $(j[y]).children().each(function(e, i){
                  var i = $(i),
                      z = i.find('input'),
                      x = $(z[0]).val(),
                      h = $(z[1]).val(),
                      a = i.parent();
                  m.push({'name': x, 'source': h}), s(z, a.next()), e != 0 && x == '' && h == '' ? i.remove() : (i.find('.item_count').text(d), d++)
                })
                return m;
              },
              q = function(e){
                var m = [];
                e.each(function(){
                  m.push(parseInt($(this).attr('data-id')))
                })
                return m;
              };

          if(!l) return !1;
          t.append('thumbnail', (u.length == 0 ? (a.find('.item_image img').last().length > 0 ? m.find('.thumbnail_text').val() : '') : u[0])), l = !1, f(p, !0), $('.entry').each(function(e, i){
            var h = $(i),
                v = h.find('.thumbnail_text').val(),
                j = h.find('.content_files').children(),
                x = h.attr('data-type'),
                r = [x];
                
            if(x == 'image'){
              var m = h.find('.thumbnail_file'),
                  z = m[0].files,
                  a = (z.length == 0 ? (h.find('.item_image img').length > 0 ? v : '') : z[0]);
              t.append('thumbnail_'+e, a)
            } else if(x == 'carousel'){
              var u = [];
              h.find('.item_caption').each(function(){
                u.push($(this).val())
              }), t.append('carousel_captions_'+e, JSON.stringify(u))

              j.each(function(c){
                t.append('carousel_'+e+'_'+c, ($(this).prop('type') == 'text' ? $(this).val() : $(this)[0].files[0]))
              });
            } else if(x == 'embed'){
              var u = [];
              h.find('.item_dattr, .item_dval').each(function(){
                u.push({name: $(this).attr('name'), value: $(this).val()});
              }), t.append('embed_'+e, JSON.stringify(u))
            }

            h.find('.item_key, .item_change').each(function(a){
              r.push($(this).val()), a == 2 && (x == 'image' ? m[0].files.length == 0 && (r[2] = v) : x == 'carousel' && (r[2] = j.length));
            }), r.push(h.attr('data-id')), w.push(r);
          }), paper_ep.settings.nodejs == 'on' && t.append('socket_id', SOCKET.id), t.append('entries', JSON.stringify(w)), w = q($('.content_rbody')), t.append('recobo', JSON.stringify(w)), w = q($('.content-cpost')), t.append('collaborators', JSON.stringify(w)), t.append('post_sources', JSON.stringify(c(0))), t.append('thumb_sources', JSON.stringify(c(1))), t.append('post_id', paper_ep.post_id), t.append('title', $('#title').val()), t.append('category', $('#category').val()), t.append('type', $('#type').val()), t.append('description', $('#description').val()), t.append('tags', $('#tags').val()), t.append('action', y.attr('data-action')), n.attr('contenteditable', !1), s(f(i, !0), a.find('.text-error')), f(y, !0).addClass('spinner-is-loading'), $.ajax({
            url: paper_ep.url.edit_post+"?token="+paper_ep.token,
            type: 'POST',
            data: t,
            contentType: !1,
            processData: !1,
            success: function(e) {
              console.log(e);
              if(e.S == 200){
                window.location = e.LK
              } else {
                f(i, !1), n.attr('contenteditable', !0), $.isArray(e.E) ? $.each(e.E, function(e, u){
                  var i = isNaN(u['EL']),
                      z = i ? $(u['EL']) : $($('.entry')[u['EL']]),
                      x = i ? z.parents('.content-field') : z.find('.content-field'),
                      h = (!isNaN(u['CT']) ? $(j[u['CT']]).parent() : x).find('.text-error'),
                      a = $(j[u['CT']]).children();
                  k((i ? (u['SW'] == 0 ? !1 : z) : x.find(u['CS'])), h, u['TX'], y, !1), u['EL'] == '.post_sources' && u['PS'] && a.each(function(e, i){
                    if(u['PS'].indexOf(e) != -1) $(a[e]).find('input:nth-child('+u['FD']+')').addClass('border-red')
                  })
                }) : o(e.E, !0), g(y), f(p, !1), l = !0
              }
            }
          })
        })
      })(), (function(){
        var a = $('#search-posts'),
            t = $('#btn-psearch'),
            d = $('#content-sposts'),
            x = $('#btn-addrb');

        $('#btn-dnotify').click(o), x.click(function(){
          a.focus()
        }), a.keyup(function(){
          var p = a.val();
          p.length > 0 ?  f(t, !1) : f(t, !0), $.post(paper_ep.url.ecp_search+'?token='+paper_ep.token, {keyword: p, post_id: paper_ep.post_id, post_ids: JSON.stringify(h)}, function(e){
            if(e.S == 200){
              d.html(e.HT)
            } else {
              d.html(e.HT ? e.HT : '')
            }
          })
        }), $(document).on('click', '.btn_putrbody', function(){
          var n = $(this),
              l = $('.simditor'),
              p = l.siblings('.text-error'),
              m = [];
          if(l.length == 0) return !1;
          s(l, p), f(n, !0).addClass('spinner-is-loading'), $(".text_simditor").each(function(i, e){
            m.push($(e).val());
          }), $.post(paper_ep.url.push_post+'?token='+paper_ep.token, {push_id: n.attr('data-id'), entry_text: JSON.stringify(m), post_id: paper_ep.post_id, post_ids: JSON.stringify(h)}, function(e){
            if(e.S == 200){
              x.before(e.HT).addClass('hidden'), h.push(e.ID), n.remove(), g(n);
            } else {
              e.PS == undefined ? (o(e.E, !0), g(f(n, !1))) : (l = $(l[e.PS]), $('html').animate({scrollTop: l.parents('.entry').position().top}, 750), p = l.siblings('.text-error'), o(paper_ep.word.oops_error_has_occurred, !0), k(l, p, e.E, f(n, !1)));
            }
          })
        }).on('click', '.btn_drecomm', function(){
          var n = $(this).parents('.content_rbody');
          h.splice(h.indexOf(parseInt(n.attr('data-id'))), 1), n.remove(), h.length == 0 && x.removeClass('hidden');
        })
      })(), (function(){
        var z = function(e, u){
              u.length > 0 ? f(e, !1) : f(e, !0)
            },
            x = function(e, u){
              clearTimeout(m), s(u), s(e.find('.item_dattr')), e.find('.item_attrna').removeClass('color-red')
            },
            y = function(e){
              e.siblings('.item_embed').html('')
            },
            t = function(e){
              var a = $(this === window ? e : this),
                  p = a.parents('.content_get'),
                  d = p.find('.content_edattrs'),
                  r = p.find('.item_attr'),
                  v = p.find('.item_val'),
                  c = v.val(),
                  g = r.val(),
                  n = !0,
                  h = '',
                  q = '';

              if(!l) return !1;
              if(g.length > 0){
                h = '<span class="item_attrna color-purple">'+g+'</span>';
                q = '<li class="content_edinput display-flex text-center margin-t15"><div class="content_eainputs display-flex w-100 position-relative"><input class="item_dattr item-input item_key no_validate h-36 background-white color-purple w-100 margin-r15 padding-t5 padding-b5 padding-l10 padding-r10 border-all border-grely border-rlow border-focus-blue sizing-box" type="text" name="attribute" readonly value="'+g+'"></div><button class="btn_ifdel btn-noway h-30 background-red color-wwhite margin-auto padding-t5 padding-b5 padding-l10 padding-r10 border-rlow boxshadow-grey hover-button animate-tab-button animation-ease3s" type="button"><svg class="icon-y vertical-sub" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path fill="currentColor" d="M0 12a1.5 1.5 0 0 1 1.5 -1.5h21a1.5 1.5 0 1 1 0 3H1.5a1.5 1.5 0 0 1 -1.5 -1.5z"></path></svg></button></li>';
              }
              if(c.length > 0){
                h = '<span class="item_attrna color-purple">'+g+'</span><span class="color-greenly">="</span><span class="item_attrva color-green">'+c+'</span><span class="color-greenly">"</span>';
                q = '<li class="content_edinput display-flex text-center margin-t15"><div class="content_eainputs display-flex w-100 position-relative"><input class="item_dattr item-input item_key no_validate h-36 background-white color-purple w-100 margin-r15 padding-t5 padding-b5 padding-l10 padding-r10 border-all border-grely border-rlow border-focus-blue sizing-box" type="text" name="attribute" readonly value="'+g+'"><input class="item_dval item-input item_key no_validate h-36 background-white color-green w-100 margin-r15 padding-t5 padding-b5 padding-l10 padding-r10 border-all border-grely border-rlow border-focus-blue sizing-box" type="text" name="value" readonly value="'+c+'"></div><button class="btn_ifdel btn-noway h-30 background-red color-wwhite margin-auto padding-t5 padding-b5 padding-l10 padding-r10 border-rlow boxshadow-grey hover-button animate-tab-button animation-ease3s" type="button"><svg class="icon-y vertical-sub" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path fill="currentColor" d="M0 12a1.5 1.5 0 0 1 1.5 -1.5h21a1.5 1.5 0 1 1 0 3H1.5a1.5 1.5 0 0 1 -1.5 -1.5z"></path></svg></button></li>';
              }
              x(p, r), d.children().each(function(i){
                var w = $(this),
                    j = p.find('.item_attrs[data-pos='+i+'] .item_attrna'),
                    b = w.find('.item_dattr');
                if(g === b.val()){
                  return k(b), j.addClass('color-red'), m = setTimeout(function(){
                    s(b), j.removeClass('color-red')
                  }, 3000), n = !1, !1;
                }
              }), n ? (h.length > 0 && (y(p), p.find('.before').before('<span class="item_attrs margin-l5" data-pos="'+o+'">'+h+'</span>'), o++, p.find('.item_vattr').text(paper_ep.word.attribute).siblings('.item_vval').text(paper_ep.word.value), r.val('').removeClass('active').focus(), v.val('').removeClass('active'), d.append(q).removeClass('hidden'))) : k(r)
            },
            m = 0,
            o = 0;

        $(document).on('keyup, input', '.item_url', function(){
          var a = $(this),
              r = a.val(),
              p = a.parents('.content_get');

          y(p), p.find('.item_srcva').text(r), z(p.find('.btn_giframe'), r);
        }).on('keyup, input', '.item_attr, .item_val', function(){
          var a = $(this),
              p = a.parents('.content_get'),
              c = p.find('.item_vattr'),
              g = p.find('.btn_ifadd'),
              r = a.val(),
              q = paper_ep.word.attribute,
              h = paper_ep.word.value,
              v = r.length > 0 ? r : q;

          if(a.hasClass('item_val')){
            c = p.find('.item_vval');
            if(r.length == 0){
              v = h
            }
            if(r.match(/["]+/)) {
              return a.val(''), c.text(h), !1
            }
          } else {
            if(r.match(/[^A-Za-z0-9\-\_]+/)) {
              return a.val(''), f(g, !0), c.text(q), !1
            }
            z(g, r), x(p, a)
          }
          c.text(v)
        }).on('keydown', '.item_attr, .item_val', function(e){
          (e.which || e.keyCode) == 13 && t(this)
        }).on('click', '.btn_ifadd', t).on('click', '.btn_ifdel', function(){
          var a = $(this),
              p = a.parents('.content_get'),
              d = a.parents('.content_edinput'),
              v = p.find('.content_edinput');

          if(!l) return !1;
          y(p), v.length <= 1 && v.parents('.content_edattrs').addClass('hidden'), p.find('.item_attrs[data-pos='+p.find('.content_edinput').index(d)+']').remove(), d.remove(), o = 0, p.find('.item_attrs[data-pos]').each(function(i){
            $(this).attr('data-pos', i), o++
          });
        })
      })(), (function(){
        $(document).on('click', '.btn_esource', function(){
          var w = $(this),
              u = w.children(),
              p = $(u[2]);
          l && (w.prev().toggleClass('hidden'), $(u[0]).toggleClass('hidden'), $(u[1]).toggleClass('hidden'), p.toggleClass('rotate-180'))
        }).on('click', '.btn_idelete', function(){
          var w = $(this).parents('.content-thumbnail'),
              u = w.find('.content-alert-popup input'),
              v = w.find('.item-file');
          l && (f(v, !1).val(''), w.find('.content-placeholder').removeClass('hidden'), w.find('.content-image').addClass('hidden'), w.find('.item_image img').remove(), v.val() != '' && f(u, !1), u.val(''), f(w.find('.btn_popup'), !1), q())
        }).on('click', '.btn_repos', function() {
          if(!l) return !1;
          var z = $(this).attr('data-type'),
              w = $(this).parents(".entry"),
              x = w.length > 0 ? w : $(this).parents(".content_rbody");
          z == 'up' ? x.insertBefore(x.prev()) : x.insertAfter(x.next())
        }).on("click", '.btn_gvideo', function(){
          var x = $(this),
              w = x.parents('.entry'),
              u = w.find('.content_get'),
              p = u.find("input"),
              v = w.find('.text-error');
          l && (s(f(p, !0), v), f(x, !0).addClass('spinner-is-loading'), $.post(paper_ep.url.get_frame+"?token="+paper_ep.token, {'url': p.val(), 'type': w.attr('data-type')}, function(e){
                      if(e.S == 200){
                        a(w, u, e.HT), g(x)
                      } else {
                        k(f(p, !1), v, e.E, x)
                      }
                  }))
        }).on("click", '.btn_giframe', function(){
          var x = $(this),
              z = x.attr('data-type') == 'iframe',
              w = x.parents('.entry'),
              u = w.find('.content_get'),
              p = u.find(".item_url"),
              m = function(e, i){
                var y = [];
                return z ? {'url': e, 'type': i} : (u.find('.item_dattr, .item_dval').each(function(){
                  y.push({name: $(this).attr('name'), value: $(this).val()});
                }), {'url': e, 'type': i, 'attrs': JSON.stringify(y)})
              },
              v = w.find('.text-error');
            l && (s(f(p, !0), v), f(x, !0).addClass('spinner-is-loading'), $.post(paper_ep.url.get_frame+"?token="+paper_ep.token, m(p.val(), w.attr('data-type')), function(e){
                        if(e.S == 200){
                          z ? (e.TT && w.find('.item_title input').addClass('active').val(e.TT), a(w, u, e.HT), e.FB == 1 && setTimeout((function(){FB.XFBML.parse()}), 1e3)) : (u.siblings('.item_embed').removeClass('hidden').html(e.HT), f(p, !1)), g(x)
                        } else {
                          k(f(p, !1), v, e.E, x)
                        }
                      }))
        }), d.click(function(){
          var w = $(this),
              u = w.siblings('.text-error');
          l && (s(!1, u, w), f(w, !0).addClass('spinner-is-loading'), $.post(paper_ep.url.entry+"?token="+paper_ep.token, {type: d.index(w)}, function(e){
                    if(e.S == 200) {
                      g(w), $('#entries').append(e.HT).find('.item_key:not(.no_focus)').focus(), e.TP == 'text' && j(!0), e.TP == 'video' && $('#type').val('video'), f($('#btn-post'), !0)
                    } else k(!1, u, e.E, w);
                  }))
        }), j()
      })();
    })()
})(paper_ep);