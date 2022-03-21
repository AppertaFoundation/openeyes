(function (exports) {

    'use strict';

    /**'
     *
     * @param {object} options
     * @constructor
     */
    function PathStep(options) {
        this.options = $.extend(true, {}, PathStep._defaultOptions, options);
        this.client_width = document.documentElement.clientWidth;
        this.slightGap = 2;
        this.cssWidth = 380;
        this.winH = window.innerHeight;
        this.pathStepLocked = false;
        this.currentPopup = null;
        this.delayRequest = null;
        this.pathstepId = null;
        this.patientID = null;
        this.request = null;
        this.pathstepIcon = null;
        this.init();
    }

    /**
     *
     * @param {pathStepIconClassSelector} 'the icon class'
     * @param {presetPopuUpClassText} 'the icon popup class text (not selector)'
     * @param {spinnerHTML} 'spinner HTML'
     * @param {domRequestURL} 'popup detailed content request URL'
     * @param {popupCloseBtnClassSelector} 'popup close button selector'
     * @param {popupContentCtnSelector} 'popup detailed content selector'
     * @param {elementDataKey} 'this will be used as $(element).data(elementDataKey) to grab the id for requesting detailed content'
     * @param {extraActions} 'take event and callback to achieve generic use'
     * @config
     */
    PathStep._defaultOptions = {
        interactive: 1,
        pathStepIconClassText: 'oe-pathstep-btn',
        pathStepIconClassSelector: '.oe-pathstep-btn',
        presetPopuUpClassText: 'oe-pathstep-popup',
        presetPopuUpClassSelector: '.oe-pathstep-popup',
        spinnerHTML: `<i class="spinner as-icon small" title="Loading..."></i>`,
        domRequestURL: '/OphDrPGDPSD/PSD/getPathStep',
        requestType: 'GET',
        popupCloseBtnClassSelector: '.close-icon-btn',
        popupContentCtnSelector: 'div.slide-open',
        elementDataKey: 'pathstep-id',
        patientIDKey: 'patient-id',
        extraActions: [],
    };
    // binding events
    PathStep.prototype.init = function(){
        this.bindMouseEnter();
        this.bindMouseClick();
        this.bindMouseLeave();
        this.bindClosePopupClick();
        this.bindExtraEvents();
        const ps = this;
        $('body').off('click').on('click', function(e){
            if($(e.target).parents(ps.options.presetPopuUpClassSelector).length || $(e.target).hasClass(ps.options.pathStepIconClassText)){
                return;
            }
            if(ps.checkPopupExistence() && !ps.administer_ready && ps.pathStepLocked){
                ps.closePopup();
            }
        });
    }

    // mouse enter event
    PathStep.prototype.bindMouseEnter = function(){
        const ps = this;
        $(document).off('mouseenter', ps.options.pathStepIconClassSelector).on('mouseenter', ps.options.pathStepIconClassSelector, function(e) {
            if(ps.pathStepLocked){
                return;
            }
            if(ps.checkPopupExistence()){
                ps.closePopup();
            }
            ps.initPopup(this);

            // minimise the amount of unintended requests
            if(ps.delayRequest){
                clearTimeout(ps.delayRequest);
            }
            ps.delayRequest = setTimeout(
                ps.requestDetails.bind(
                    ps,
                    {partial: 1, pathstep_id: ps.pathstepId, patient_id: ps.patientID}
                ),
                300
            );
        });
    }

    // mouse leave event
    PathStep.prototype.bindMouseLeave = function(){
        const ps = this;
        $(document).off('mouseleave', ps.options.pathStepIconClassSelector).on('mouseleave', ps.options.pathStepIconClassSelector, function(){
            if(ps.request && !ps.pathStepLocked){
                ps.request.abort();
            }
            if(ps.delayRequest){
                clearTimeout(ps.delayRequest);
            }
            if(ps.pathStepLocked){
                return;
            }
            if(ps.checkPopupExistence()){
                ps.closePopup();
            }
        });
    }

    // click event
    PathStep.prototype.bindMouseClick = function(){
        const ps = this;
        $(document).off('click', ps.options.pathStepIconClassSelector).on('click', ps.options.pathStepIconClassSelector, function(){
            // if there is no existed popup, do nothing
            if(!ps.checkPopupExistence() || ps.administer_ready){
                return;
            }

            // if the icon is clicked in a locked state, close the popup
            if(ps.pathStepLocked && ps.pathstepId === $(this).data(ps.options.elementDataKey)){
                ps.closePopup();
                return;
            }else{
                ps.closePopup();
                ps.initPopup(this);
            }

            // clear the popup and fill with requested content
            ps.pathStepLocked = true;
            ps.resetPopup();
            if(ps.delayRequest){
                clearTimeout(ps.delayRequest);
            }
            ps.delayRequest = setTimeout(
                ps.requestDetails.bind(
                    ps,
                    {partial: 0, pathstep_id: ps.pathstepId, patient_id: ps.patientID, interactive: ps.options.interactive}
                ),
                350
            );
        });
    }

    // close btn click event
    PathStep.prototype.bindClosePopupClick = function(){
        const ps = this;
        let close_btn_selector = `.${ps.options.presetPopuUpClassText} ${ps.options.popupCloseBtnClassSelector}`;
        $(document).off('click', close_btn_selector).on('click', close_btn_selector, function(){
            if(!ps.checkPopupExistence()){
                return;
            }
            ps.closePopup();
        });
    }

    // initialize popup
    PathStep.prototype.initPopup = function(ele){
        let $pathstep_ctn = $(`<div class="${this.options.presetPopuUpClassText}"></div>`);
        let $pathstep_ctn_spinner = $(this.options.spinnerHTML);
        this.pathstepIcon = ele;
        this.pathstepId = $(ele).data(this.options.elementDataKey);
        this.patientID = $(ele).data(this.options.patientIDKey);
        this.administer_ready = false;

        const popup_pos = this.getPopupPosition(ele, $pathstep_ctn);
        $pathstep_ctn.css(popup_pos);
        $pathstep_ctn_spinner.appendTo($pathstep_ctn);
        $pathstep_ctn.appendTo('body');
        this.popupOriginalHeight = $pathstep_ctn.get(0).scrollHeight;
        this.currentPopup = $pathstep_ctn;
    }

    PathStep.prototype.getPopupPosition = function(ele, $popup){
        let btnPos = ele.getBoundingClientRect();
        let top;
        let bottom;
        const right = this.client_width - btnPos.right;
        const left = (btnPos.right - this.cssWidth);

        if( btnPos.bottom < (this.winH * 0.7)){
            top = btnPos.bottom + this.slightGap + 'px';
            bottom = 'auto';
            $popup.removeClass('arrow-b').addClass('arrow-t');
        } else {
            top = 'auto';
            bottom = (this.winH - btnPos.top) + this.slightGap + 'px';
            $popup.removeClass('arrow-t').addClass('arrow-b');
        }
        return {
            top : top, 
            right: right + 'px', 
            left: left + 'px', 
            bottom: bottom
        };
    }
    PathStep.prototype.bindExtraEvents = function(){
        const ps = this;
        ps.options.extraActions.forEach(function(item, i){
            if($(item.target).length){
                $(item.target).off(item.event).on(item.event, function(e){
                    item.callback(e, this, ps);
                });
            } else {
                $(document).off(item.event, item.target).on(item.event, item.target, function(e){
                    item.callback(e, this, ps);
                });
            }
        });
    }

    // send request for detailed content
    PathStep.prototype.requestDetails = function(data = null, url = null, type = null, callback = null, complete = null){
        const ps = this;
        if(ps.request){
            ps.request.abort();
        }
        ps.request = $.ajax({
            type: type || "GET",
            url: url || ps.options.domRequestURL,
            data: data,
            success: function (resp) {
                if(callback){
                    callback(ps, resp);
                } else {
                    ps.renderPopupContent(resp);
                }
                $('.user-pin-entry').trigger('focus');
            },
            complete
        });
    }

    PathStep.prototype.closePopup = function(terminate = true){
        this.currentPopup.remove();
        if(terminate){
            this.pathStepLocked = false;
            this.administer_ready = false;
        }
    }
    PathStep.prototype.renderPopupContent = function(content){
        this.currentPopup.html(content);
        this.configPopupHeight(this.currentPopup, this.options.popupContentCtnSelector);
    }
    PathStep.prototype.resetPopup = function(){
        this.currentPopup.html('');
        $(this.options.spinnerHTML).appendTo(this.currentPopup);
        this.currentPopup.get(0).style.height = this.popupOriginalHeight + 'px';
    }
    // setup the popup height after getting detailed content
    PathStep.prototype.configPopupHeight = function($popup, popup_content_wrapper_class){
        let popup_ctn = $popup.get(0);
        let scrollHeight = $popup.find(popup_content_wrapper_class).get(0).scrollHeight;
        popup_ctn.style.height = (scrollHeight + 20) + 'px';
    }

    // check if there is any existed popup
    PathStep.prototype.checkPopupExistence = function(){
        return this.currentPopup && this.currentPopup.closest('body').length > 0;
    }

    exports.PathStep = PathStep;

}(OpenEyes.UI));
