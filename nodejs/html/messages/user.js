// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

var functions = require('../../includes/functions');
const SETTINGS = functions.Settings();

module.exports = function(socket, temp){
    return `<li class="content_pnuser position-relative margin-l5 margin-r5 padding-l5 padding-r5`+(temp.last_text == '' ? ' is_new' : '')+`" data-id="{!user_id}">
        <a class="content-pnmessage hover-background display-flex direction-row align-center color-black padding-t10 padding-b10 padding-l10 padding-r10`+(temp.profile_id == temp.user_id || temp.last_text == '' ? ' active' : '')+(temp.last_unseen == !0 ? ' unseen' : '')+`" href="{$url->{{#r_messages}/{!user}}}">
            <div class="item-pnmessage overflow-hidden border-rall margin-r10">
                <img class="content-preloader-image position-relative blur-up lazyload" src="{!avatar_s}" alt="{!username}">
            </div>
            <div class="display-flex direction-colum">
                <span class="margin-b5">{!username}</span>
                <div class="content_pninfo display-flex direction-row font-franklin font-low`+(temp.last_text == '' ? ' hidden' : '')+`">
                    <span class="item_pntuser ellipsis-horizontal">{!last_text}</span>
                    <span class="margin-l5 margin-r5 font-normal" aria-hidden="true">·</span>
                    <span class="item_pncuser font-normal">{!last_created_at}</span>
                </div>
            </div>
        </a>
        <div class="content_pnmoptions position-absolute margin-left-auto animation-ease3s">
            <button class="btn_pnmoptions btn-noway background-white color-blue w-36px h-36 btn-noway padding-5 hover-background opacity-0 boxshadow-grey border-rall animation-ease3s animate-tab-button" aria-haspopup="true" aria-expanded="false" aria-label="{$word->options}">
                <svg class="icon-z vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <title>{$word->options}</title>
                    <path fill="currentColor" d="M7 12c0 1.104-.896 2-2 2s-2-.896-2-2 .896-2 2-2 2 .896 2 2zm12-2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm-7 0c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"/>
                </svg>
            </button>
            <div class="content_pmoptions position-absolute background-white z-index-1 boxshadow-grey hidden" role="dialog" aria-modal="true" aria-label="{$word->options}">`+(
                SETTINGS.blocked_users == 'on' && ['publisher', 'viewer'].indexOf(temp.role) !== -1 && temp.user_deleted == !0 ?
                    `<button class="btn_pcblock w-100 text-left btn-noway padding-t10 padding-b10 padding-l10 padding-r10 hover-button animate-tab-button animation-ease3s" data-id="{!user_id}">{$word->block}</button>`
                :
                    '' 
            )+`<button class="btn_pcdelete w-100 text-left btn-noway padding-t10 padding-b10 padding-l10 padding-r10 hover-button animate-tab-button animation-ease3s`+(temp.last_text == '' ? ' hidden': '')+`" data-id="{!id}">{$word->delete}</button>
            </div>
        </div>
        <div class="item-pnmpoint position-absolute background-blue border-rall"></div>
    </li>`;
};