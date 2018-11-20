var OpenEyes = OpenEyes || {};

OpenEyes.OphCoCorrespondence = OpenEyes.OphCoCorrespondence || {};

(function(exports){
    function DocumentViewerController(){
        this.init();
    }

    DocumentViewerController.prototype.init = function(){

        let controller = this;
        this.$container = $('.js-correspondence-image-overlay');
        this.pageCount = controller.$container.children().length;
        this.currentPageNumber = 0;

        controller.$container.on('mousemove', function (e) {
            var parentOffset = $(this).parent().offset();
            var relX = e.pageX - parentOffset.left;
            var relY = e.pageY - parentOffset.top;
            var xRatio = relX / $(this).width();
            var yRatio = relY / $(this).height();
            controller.changePreviewCoords(xRatio, yRatio);
        });
    };

    DocumentViewerController.prototype.changePreviewCoords = function(xRatio, yRatio) {
        let controller = this;
        var page = Math.ceil(controller.pageCount * yRatio);
        if(page !== 0){
            page = page - 1;
        }
        controller.changePreviewPage(page);
    };


    DocumentViewerController.prototype.changePreviewPage = function(page) {
        let controller = this;
        if (controller.currentPageNumber === page) {
            return;
        }

        if (page !== null && page !== controller.pageCount) {
            $('#correspondence_image_' + controller.currentPageNumber).hide();
            controller.currentPageNumber = page;
            $('#correspondence_image_' + page).show();
        }
    };

    exports.DocumentViewerController = DocumentViewerController;
})(OpenEyes.OphCoCorrespondence);