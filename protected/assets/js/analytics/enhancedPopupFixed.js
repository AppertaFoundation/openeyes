/*
Enhance Popup Fixed.
1) Provide click (touch) mechanism.
2) Enhance for mouse / trackpad
3) Open popup and position (as it's Fixed)
IDG demo, it assumes a DOM structure of:
<wrap>
	<btn />
	<popup /> // Fixed position
</wrap>
... and that there is an 'active' class on button ;)
*/
var enhancedPopupFixed = function($wrap,$btn,$popup){
    var popupShow = false;
    var css;

    // handles touch
    $btn.click( changePopup );

    // enchance with mouseevents through DOM wrapper
    $wrap
        .mouseenter( showPopup )
        .mouseleave( hidePopup );

    // controller
    function changePopup(){
        if(!popupShow){
            showPopup();
        } else {
            hidePopup();
        }
    }

    function showPopup(){
        setClasses();
        setCSSposition();
        $popup.show();
        $btn.addClass('active');
        popupShow = true;
    }

    function hidePopup(){
        $popup.hide();
        $btn.removeClass('active');
        popupShow = false;
        resetCSS();
    }

    // each time it opens
    // work out where it is and apply
    // CSS and positioning.

    function setClasses(){
        // position popup based on screen location
        // options: top-left, top-right, bottom-left, bottom-right
        // updates the look of the popup
        var offset = $wrap.offset();

        var w = window.innerWidth;
        var h = window.innerHeight;

        if( offset.top < ( h / 2 ) ){
            css = "top-";
        } else {
            css = "bottom-";
        }

        if(offset.left < ( w / 2 ) ){
            css += "left";
        } else {
            css += "right";
        }

        $popup.addClass(css);
    }

    function resetCSS(){
        $popup.removeClass(css);
        $popup.css("top", "");
        $popup.css("bottom", "");
        $popup.css("left", "");
        $popup.css("right", "");
    }


    function setCSSposition(){
        /*
      Popup is FIXED positioned
      work out offset position
      setup events to close it on resize or scroll.
      */
        // js vanilla:
        var wrapPos = $wrap[ 0 ].getBoundingClientRect();
        var w = document.documentElement.clientWidth;
        var h = document.documentElement.clientHeight;

        switch(css){
            case "top-left":
                // set CSS Fixed position
                $popup.css({
                    "top": wrapPos.y,
                    "left": wrapPos.x
                });
                break;
            case "top-right":
                // set CSS Fixed position
                $popup.css({
                    "top": wrapPos.y,
                    "right": (w - wrapPos.right) });
                break;
            case "bottom-left":
                // set CSS Fixed position
                $popup.css({
                    "bottom": (h - wrapPos.bottom),
                    "left": wrapPos.x
                });
                break;
            case "bottom-right":
                // set CSS Fixed position
                $popup.css({
                    "bottom": (h - wrapPos.bottom),
                    "right": (w - wrapPos.right)
                });
                break;
        }

    }



    // should be a close icon button in the popup
    var $closeBtn = $popup.find('.close-icon-btn');
    $closeBtn.click( hidePopup );
}