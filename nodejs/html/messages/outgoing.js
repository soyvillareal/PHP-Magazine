

module.exports = function(socket, temp){
    return (temp.deleted_fuser == 0 ? 
        (temp.type == 'answered' ? `
            <div class="content_pmtmessage display-flex direction-row align-center margin-left-auto" data-id="{!id}">
                <div class="display-flex direction-colum w-100 margin-r10">
                    <div class="item_pmtmessage position-relative" data-id="{!answered_id}" data-type="{!ans_type}">
                        <h4 class="font-low font-normal text-right color-grey">
                            <svg class="icon-s vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M0.389 8.899L8.64 1.774C9.362 1.151 10.5 1.657 10.5 2.626v3.752c7.529 0.086 13.5 1.595 13.5 8.731 0 2.88 -1.855 5.733 -3.906 7.225 -0.64 0.466 -1.552 -0.119 -1.316 -0.873 2.126 -6.797 -1.008 -8.602 -8.278 -8.707V16.875c0 0.97 -1.139 1.474 -1.86 0.851l-8.25 -7.125c-0.519 -0.448 -0.52 -1.254 0 -1.703z"/>
                            </svg>
                            <span>{!ans_title}</span>
                        </h4>
                        <div class="container-pmtans display-flex direction-row align-center">`+(
                            temp.ans_deleted == !1 ? 
                                (temp.ans_type == 'text' ?
                                    `<p class="item_pmtmmessage break-all background-grely margin-left-auto padding-t5 padding-b15 padding-l15 padding-r15" data-id="{!ans_id}" data-type="text">{!ans_text}</p>`
                                :   
                                    (temp.ans_type == 'file' ?
                                        `<div class="content-pmtfile content_pmtfile display-flex direction-row align-center background-grely position-relative margin-t5 margin-left-auto border-r25px" data-id="{!fi_aid}" data-type="file">
                                            <div class="item-pmtfile display-flex background-white margin-l10 margin-r10 border-rall">
                                                <svg class="icon-y vertical-middle color-black margin-auto" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="95.52 95.52 24 24" enable-background="new 796 796 200 200" xml:space="preserve">
                                                    <title>{$word->file}</title>
                                                    <path fill="currentColor" d="M117.227 103.058l-7.118 -7.118c-0.268 -0.268 -0.633 -0.419 -1.012 -0.419h-10.271c-0.791 0 -1.431 0.641 -1.431 1.431v21.138c0 0.79 0.641 1.431 1.431 1.431h17.389c0.79 0 1.431 -0.641 1.431 -1.431V104.07C117.646 103.69 117.495 103.326 117.227 103.058zM112.26 115.69h-9.48c-0.398 0 -0.72 -0.322 -0.72 -0.72c0 -0.398 0.322 -0.72 0.72 -0.72h9.48c0.398 0 0.72 0.322 0.72 0.72C112.98 115.368 112.658 115.69 112.26 115.69zM112.26 112.11h-9.48c-0.398 0 -0.72 -0.322 -0.72 -0.72s0.322 -0.72 0.72 -0.72h9.48c0.398 0 0.72 0.322 0.72 0.72S112.658 112.11 112.26 112.11zM112.26 108.53h-9.48c-0.398 0 -0.72 -0.322 -0.72 -0.72c0 -0.398 0.322 -0.72 0.72 -0.72h9.48c0.398 0 0.72 0.322 0.72 0.72C112.98 108.208 112.658 108.53 112.26 108.53zM110.528 104.07c-0.79 0 -1.431 -0.641 -1.431 -1.431v-5.423l6.854 6.854L110.528 104.07L110.528 104.07z"></path>
                                                </svg>
                                            </div>
                                            <div class="display-flex direction-colum padding-t10 padding-b10 padding-l10 padding-r10">
                                                <span class="color-black break-all ellipsis-vertical ellipsis-v3 no-select">{!fi_aname}</span>
                                                <span class="color-black font-low margin-t5 no-select">{!fi_asize}</span>
                                            </div>
                                        </div>`
                                    :
                                        `<div class="content-pmtfile content_pmtimage margin-left-auto" data-id="{!fi_aid}" data-type="image">
                                            <img class="content-preloader-image position-relative blur-up item-pmtimage is-reply border-rlow lazyload" src="{!fi_aurl}" alt="{!fi_aname}">
                                        </div>`))
                            :
                                `<span class="item-pmtdeleted break-all background-white color-grey margin-left-auto padding-t5 padding-b5 padding-l15 padding-r15 border-r25px border-all border-grely">{!ans_deleted_word}</span>`)+`
                        </div>
                    </div>
                    <div class="item-pmtreply display-flex direction-row align-center position-relative margin-t15 margin-l5">`+(
                        temp.text != null ?
                            `<button class="btn_pmtdelete w-30px h-30 btn-noway margin-left-auto margin-r5 hover-background border-rall opacity-0 hover-button animation-ease3s animate-tab-button" data-id="{!id}" data-type="text">
                                <span class="color-grey">
                                    <svg class="icon-z vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="currentColor" d="M 10.091 16.688 L 10.091 9.813 C 10.091 9.721 10.061 9.646 10.001 9.588 C 9.942 9.529 9.866 9.5 9.773 9.5 L 9.136 9.5 C 9.044 9.5 8.967 9.529 8.908 9.588 C 8.848 9.646 8.818 9.721 8.818 9.813 L 8.818 16.688 C 8.818 16.779 8.848 16.854 8.908 16.912 C 8.967 16.971 9.044 17 9.136 17 L 9.773 17 C 9.866 17 9.942 16.971 10.001 16.912 C 10.061 16.854 10.091 16.779 10.091 16.688 Z M 12.636 16.688 L 12.636 9.813 C 12.636 9.721 12.607 9.646 12.547 9.588 C 12.487 9.529 12.411 9.5 12.318 9.5 L 11.682 9.5 C 11.589 9.5 11.513 9.529 11.453 9.588 C 11.393 9.646 11.364 9.721 11.364 9.813 L 11.364 16.688 C 11.364 16.779 11.393 16.854 11.453 16.912 C 11.513 16.971 11.589 17 11.682 17 L 12.318 17 C 12.411 17 12.487 16.971 12.547 16.912 C 12.607 16.854 12.636 16.779 12.636 16.688 Z M 15.182 16.688 L 15.182 9.813 C 15.182 9.721 15.152 9.646 15.092 9.588 C 15.033 9.529 14.956 9.5 14.864 9.5 L 14.227 9.5 C 14.134 9.5 14.058 9.529 13.999 9.588 C 13.939 9.646 13.909 9.721 13.909 9.813 L 13.909 16.688 C 13.909 16.779 13.939 16.854 13.999 16.912 C 14.058 16.971 14.134 17 14.227 17 L 14.864 17 C 14.956 17 15.033 16.971 15.092 16.912 C 15.152 16.854 15.182 16.779 15.182 16.688 Z M 9.773 7 L 14.227 7 L 13.75 5.857 C 13.704 5.799 13.647 5.763 13.581 5.75 L 10.429 5.75 C 10.363 5.763 10.306 5.799 10.26 5.857 Z M 19 7.313 L 19 7.938 C 19 8.029 18.97 8.104 18.911 8.162 C 18.851 8.221 18.775 8.25 18.682 8.25 L 17.727 8.25 L 17.727 17.508 C 17.727 18.048 17.572 18.515 17.26 18.909 C 16.948 19.303 16.574 19.5 16.136 19.5 L 7.864 19.5 C 7.426 19.5 7.052 19.31 6.74 18.929 C 6.429 18.548 6.273 18.087 6.273 17.547 L 6.273 8.25 L 5.318 8.25 C 5.225 8.25 5.149 8.221 5.089 8.162 C 5.03 8.104 5 8.029 5 7.938 L 5 7.313 C 5 7.221 5.03 7.146 5.089 7.088 C 5.149 7.029 5.225 7 5.318 7 L 8.391 7 L 9.087 5.369 C 9.186 5.128 9.365 4.923 9.624 4.754 C 9.882 4.585 10.144 4.5 10.409 4.5 L 13.591 4.5 C 13.856 4.5 14.118 4.585 14.376 4.754 C 14.635 4.923 14.814 5.128 14.913 5.369 L 15.609 7 L 18.682 7 C 18.775 7 18.851 7.029 18.911 7.088 C 18.97 7.146 19 7.221 19 7.313 Z"/>
                                    </svg>
                                </span>
                                <div class="content-spinner-circle position-absolute">
                                    <div class="spinner-circle"></div>
                                </div>
                            </button>
                            <button class="btn_pmtreply w-30px h-30 btn-noway margin-r5 hover-background color-grey border-rall opacity-0 hover-button animation-ease3s animate-tab-button" data-id="{!id}" data-type="text">
                                <svg class="icon-x vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="currentColor" d="M0.389 8.899L8.64 1.774C9.362 1.151 10.5 1.657 10.5 2.626v3.752c7.529 0.086 13.5 1.595 13.5 8.731 0 2.88 -1.855 5.733 -3.906 7.225 -0.64 0.466 -1.552 -0.119 -1.316 -0.873 2.126 -6.797 -1.008 -8.602 -8.278 -8.707V16.875c0 0.97 -1.139 1.474 -1.86 0.851l-8.25 -7.125c-0.519 -0.448 -0.52 -1.254 0 -1.703z"/>
                                </svg>
                            </button>
                            <p class="item_pmtext break-all background-blue color-wwhite padding-t5 padding-b5 padding-l15 padding-r15 border-r25px">{!text}</p>`
                        :
                            `<div class="display-flex direction-colum">`+(
                                temp.messafi == !0 ?
                                    ((temp.has_file == !0 ?
                                        `<div class="content_pmtfiles display-grid`+(temp.has_image == !0 ? ' margin-b10' : '')+`">
                                            {!outfiles}
                                        </div>`
                                    :
                                        '')+
                                    (temp.has_image == !0 ?
                                        `<div class="content_pmtimages display-grid">
                                            {!outimages}
                                        </div>`
                                    :
                                        ''))
                                :
                                    ''

                            )+
                            `</div>`)+
                    `</div>
                </div>
            </div>`
        :
            `<div class="content_pmtmessage`+(temp.text != null ? ' display-flex direction-row align-center ' : '')+`margin-left-auto margin-r10 padding-t15" data-id="{!id}">`+(
                temp.text != null ?
                    `<button class="btn_pmtdelete w-30px h-30 btn-noway margin-left-auto margin-r5 hover-background border-rall opacity-0 hover-button animation-ease3s animate-tab-button" data-id="{!id}" data-type="text">
                        <span class="color-grey">
                            <svg class="icon-z vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor" d="M 10.091 16.688 L 10.091 9.813 C 10.091 9.721 10.061 9.646 10.001 9.588 C 9.942 9.529 9.866 9.5 9.773 9.5 L 9.136 9.5 C 9.044 9.5 8.967 9.529 8.908 9.588 C 8.848 9.646 8.818 9.721 8.818 9.813 L 8.818 16.688 C 8.818 16.779 8.848 16.854 8.908 16.912 C 8.967 16.971 9.044 17 9.136 17 L 9.773 17 C 9.866 17 9.942 16.971 10.001 16.912 C 10.061 16.854 10.091 16.779 10.091 16.688 Z M 12.636 16.688 L 12.636 9.813 C 12.636 9.721 12.607 9.646 12.547 9.588 C 12.487 9.529 12.411 9.5 12.318 9.5 L 11.682 9.5 C 11.589 9.5 11.513 9.529 11.453 9.588 C 11.393 9.646 11.364 9.721 11.364 9.813 L 11.364 16.688 C 11.364 16.779 11.393 16.854 11.453 16.912 C 11.513 16.971 11.589 17 11.682 17 L 12.318 17 C 12.411 17 12.487 16.971 12.547 16.912 C 12.607 16.854 12.636 16.779 12.636 16.688 Z M 15.182 16.688 L 15.182 9.813 C 15.182 9.721 15.152 9.646 15.092 9.588 C 15.033 9.529 14.956 9.5 14.864 9.5 L 14.227 9.5 C 14.134 9.5 14.058 9.529 13.999 9.588 C 13.939 9.646 13.909 9.721 13.909 9.813 L 13.909 16.688 C 13.909 16.779 13.939 16.854 13.999 16.912 C 14.058 16.971 14.134 17 14.227 17 L 14.864 17 C 14.956 17 15.033 16.971 15.092 16.912 C 15.152 16.854 15.182 16.779 15.182 16.688 Z M 9.773 7 L 14.227 7 L 13.75 5.857 C 13.704 5.799 13.647 5.763 13.581 5.75 L 10.429 5.75 C 10.363 5.763 10.306 5.799 10.26 5.857 Z M 19 7.313 L 19 7.938 C 19 8.029 18.97 8.104 18.911 8.162 C 18.851 8.221 18.775 8.25 18.682 8.25 L 17.727 8.25 L 17.727 17.508 C 17.727 18.048 17.572 18.515 17.26 18.909 C 16.948 19.303 16.574 19.5 16.136 19.5 L 7.864 19.5 C 7.426 19.5 7.052 19.31 6.74 18.929 C 6.429 18.548 6.273 18.087 6.273 17.547 L 6.273 8.25 L 5.318 8.25 C 5.225 8.25 5.149 8.221 5.089 8.162 C 5.03 8.104 5 8.029 5 7.938 L 5 7.313 C 5 7.221 5.03 7.146 5.089 7.088 C 5.149 7.029 5.225 7 5.318 7 L 8.391 7 L 9.087 5.369 C 9.186 5.128 9.365 4.923 9.624 4.754 C 9.882 4.585 10.144 4.5 10.409 4.5 L 13.591 4.5 C 13.856 4.5 14.118 4.585 14.376 4.754 C 14.635 4.923 14.814 5.128 14.913 5.369 L 15.609 7 L 18.682 7 C 18.775 7 18.851 7.029 18.911 7.088 C 18.97 7.146 19 7.221 19 7.313 Z"/>
                            </svg>
                        </span>
                        <div class="content-spinner-circle position-absolute">
                            <div class="spinner-circle"></div>
                        </div>
                    </button>
                    <button class="btn_pmtreply w-30px h-30 btn-noway margin-l5 margin-r5 padding-5 hover-background color-grey border-rall opacity-0 hover-button animation-ease3s animate-tab-button" data-id="{!id}" data-type="text">
                        <svg class="icon-x vertical-middle" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path fill="currentColor" d="M0.389 8.899L8.64 1.774C9.362 1.151 10.5 1.657 10.5 2.626v3.752c7.529 0.086 13.5 1.595 13.5 8.731 0 2.88 -1.855 5.733 -3.906 7.225 -0.64 0.466 -1.552 -0.119 -1.316 -0.873 2.126 -6.797 -1.008 -8.602 -8.278 -8.707V16.875c0 0.97 -1.139 1.474 -1.86 0.851l-8.25 -7.125c-0.519 -0.448 -0.52 -1.254 0 -1.703z"/>
                        </svg>
                    </button>
                    <p class="item_pmtext break-all background-blue color-wwhite padding-t5 padding-b5 padding-l15 padding-r15 border-r25px">{!text}</p>`
                :
                    (temp.messafi == !0 ?
                        ((temp.has_file == !0 ?
                            `<div class="content_pmtfiles display-grid">
                                {!outfiles}
                            </div>`
                        :
                            '')+
                        (temp.has_image == !0 ?
                            `<div class="content_pmtimages display-grid">
                                {!outimages}
                            </div>`
                        :
                            ''))
                    :
                        '')
            )+
            `</div>`)
    :
        '')+
        (temp.text != null ? 
            (temp.messafi == !0 ?
                ((temp.has_file == !0 ? 
                    `<div class="content_pmtfiles display-grid margin-r10">
                        {!outfiles}
                    </div>`
                :
                    '')+
                (temp.has_image == !0 ?
                    `<div class="content_pmtimages display-grid margin-left-auto margin-r10">
                        {!outimages}
                    </div>`
                :
                    ''))
            :
                '')
        :
            '')+
        `<div class="item_pmttime margin-t5 margin-left-auto margin-r15 font-lowly color-grey">{!created_at}</div>`;
};