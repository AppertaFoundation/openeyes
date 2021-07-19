(function (exports, Util) {

    var AdderDialog = exports;

    function MedSearch(options) {
        options = $.extend(true, {}, MedSearch._defaultOptions, options);
        options.onSelect = this.onSelect;
        AdderDialog.call(this, options);
    }

    Util.inherits(AdderDialog, MedSearch);

    MedSearch._defaultOptions = {
        searchOptions: {
            searchSource: '/medicationManagement/findRefMedications',
        },
    };
    MedSearch.prototype.open = function(){
        MedSearch._super.prototype.open.call(this);
        var $items = this.$tr.children("td:eq(1)").find("ul.add-options li");
        $items.hide();
    }
    MedSearch.prototype.onSelect = function(e) {
        let $item = $(e.target).is("span") ? $(e.target).closest("li") : $(e.target);
        let $tr = $item.closest("tr");
        if($item.attr("data-type")){
            let $all_options = $tr.children("td:eq(1)").find("ul.add-options li");
            let $relevant_options = $tr.children("td:eq(1)").find("ul.add-options li[data-category=" + $item.attr("data-type") + "]");
            $all_options.hide();
            $relevant_options.show();
        }
    },
    exports.MedSearch = MedSearch;

}(OpenEyes.UI.AdderDialog, OpenEyes.Util));
