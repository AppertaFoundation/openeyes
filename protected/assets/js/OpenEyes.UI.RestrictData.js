var OpenEyes = OpenEyes || {};

OpenEyes.UI = OpenEyes.UI || {};

(function (exports) {

    function RestrictDataController(options) {
        this.options = $.extend(true, {}, RestrictDataController._defaultOptions, options);
        this.selector = this.options.selector;

        this.collapseData();
    }

    RestrictDataController._defaultOptions = {
    };

    /*
    Tile Element - watch for data overflow
    */
    RestrictDataController.prototype.restrictDataHeight = function( wasHiddenElem = false ){
        function setupRestrict( $elem ){
            /*
            Restrict data can have several different
            heights, e.g. 'rows-10','rows-5'
            */

            let wrapHeight 		= $elem.height();
            let $content 		= $elem.find('.restrict-data-content');
            let scrollH 		= $content.prop('scrollHeight');

            /*
            if set up, don't do bother again, probably coming in from a
            hide show wrapper.
            */
            if( $elem.data('build') ){
                // but fade in the flag UI..
                $elem.find('.restrict-data-shown-flag').fadeIn();
            } else {
                if(scrollH > wrapHeight){
                    // it's scrolling, so flag it
                    let flag = $('<div/>',{ class:"restrict-data-shown-flag"});
                    $elem.prepend(flag);

                    flag.click(function(){
                        $content.animate({
                            scrollTop: scrollH
                        }, 1000);
                    });

                    $content.on('scroll',function(){
                        flag.fadeOut();
                    });

                    $elem.data('build',true);
                } else {
                    /*
                    In case there are fewer than 5 or 10 rows,
                    remove class to prevent adding unnecessary white space
                     */
                    $content.removeClass('restrict-data-content');
                }
            }
        }

        if( wasHiddenElem !== false){
            /*
            A restricted height element could be wrapped in hideshow
            wrapper DOM. Therefore when it's open IT calls this function
            with an Elem and then sets it up.
            */
            setupRestrict( $(wasHiddenElem) );
            return;
        }


        if( $('.restrict-data-shown').length === 0 ) return;

        /*
        Quick demo of the UI / UX behaviour
        */
        $('.restrict-data-shown').each(function(){
            setupRestrict( $(this) );
        });
    };

    /*
    HideShow content
    */
    RestrictDataController.prototype.collapseData = function(){
        "use strict";

        let controller = this;
        let collapseData = document.querySelectorAll('.collapse-data');
        if(collapseData.length < 1) return;

        function hideShowBtn( elem ){
            let btn = elem.querySelector('.collapse-data-header-icon');
            let content = elem.querySelector('.collapse-data-content');
            let hidden = true;

            let changeState = (e) => {
                e.stopPropagation();
                if(hidden){
                    content.style.display = "block";
                    btn.className = "collapse-data-header-icon collapse";

                    let restrictedContentChild = content.querySelector('.restrict-data-shown');
                    if(restrictedContentChild != null){
                        controller.restrictDataHeight( restrictedContentChild );
                    }


                } else {
                    content.style.display = "none";
                    btn.className = "collapse-data-header-icon expand";
                }

                hidden = !hidden;
            };

            btn?.addEventListener("click", changeState, false);
        }

        /*
        Switch to array for better support
        */
        let arr = Array.prototype.slice.call(collapseData);
        arr.forEach( ( elem ) => { hideShowBtn( elem );$(elem).trigger('loaded'); });
    };

    exports.RestrictDataController = RestrictDataController;
}(OpenEyes.UI));

$(document).ready(function(){
    new OpenEyes.UI.RestrictDataController();
});