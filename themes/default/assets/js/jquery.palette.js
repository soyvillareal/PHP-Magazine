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
    var w = function(e){
        e.addClass('spinner-is-loading').attr('disabled', !0)
      },
      r = function(e){
        e.removeClass('spinner-is-loading').removeAttr('disabled'), u = !0
      },
      u = !0,
      t;
      
    $(window).on('beforeunload', function(){
        if(t != undefined && u){
           return paper_gp.word.leave_page_changes_will_saved;
        }
    }), $('.btn_cpalette').click(function(){
        var x = $(this),
            a = x.attr('data-type'),
            b = x.next(),
            c = 'dark';
   
        $('.btn_presave').attr('data-type', a);
        if(a == 'dark'){
           c = 'light', $('html').attr('dark', !0)
        } else $('html').removeAttr('dark');
        if(b.hasClass('hidden')){
           var d = $('.btn_cpalette[data-type='+c+']'),
               f = function(e){
                 e.find('svg').toggleClass('rotate-180')
               };
           f(d), d.next().addClass('hidden'), b.removeClass('hidden'), f(x)
        }
    }), $('#btn-apalette').click(function(){
        var x = $(this),
            a = x.find('svg'),
            b = x.parent(),
            c = function(e){
              a.toggleClass('rotate-180').find('title').text(e)
            };
        if(!a.hasClass('rotate-180')){
           c(paper_gp.word.show), b.css({'min-width': 0, 'width': 0});
        } else {
           c(paper_gp.word.hide), b.removeAttr('style')
        }
    }), $('#btn-psave').click(function(){
        var x = $(this);
        w(x), u = !1, $.post(paper_gp.url.change_palette+'?token='+paper_gp.token, {light_palette: $('#light-fpalette').serialize(), dark_palette: $('#dark-fpalette').serialize()}, function(e){
           console.log(e);
           if(e.S == 200){
              window.location.reload();
           } else r(x);
        });
    }), $('#btn-preset').click(function(){
        var x = $(this);
        w(x), u = !1, $.post(paper_gp.url.reset_palette+'?token='+paper_gp.token, {type: x.attr('data-type')}, function(e){
           console.log(e);
           if(e.S == 200){
              window.location.reload();
           } else r(x);
        });
    }), $(".item_palette").each(function(){
        $(this).next().text($('.'+$(this).attr('name')).length)
    }).spectrum({
        flat: false,
        showInput: true,
        showInitial: true,
        allowEmpty: false,
        showAlpha: true,
        clickoutFiresChange: true,
        cancelText: paper_gp.word.cancel,
        chooseText: paper_gp.word.choose,
        preferredFormat: "hex",
        move: function(e){
           var d = $(this),
               a = $('#'+d.attr('data-type')+'-palette'),
               b = a.text(),
               c = window.tinycolor(d.val())._format;
           t = e.toHexString();
           if(c == 'rgb'){
              t = e.toRgbString();
           }
           var g = b.match(new RegExp('(?!\s)([^}])*(?![\.\#\s\,\>])'+d.attr('name')+'[^}]*}', 'is')),
               h = d.attr('data-prop'),
               k = g[0].replace(new RegExp(h+'\:([\\\s|]*)(['+d.val()+']+)'), h+':'+t);
       
           d.val(t), a.html(b.replace(g[0], k));
        },
        change: function(){
           $(this).val(t);
        }
    });
})(paper_gp);