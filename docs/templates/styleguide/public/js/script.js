/* global prettyPrint: false */

(function() {

  var Docs = (function() {

    function init() {
      initSyntaxHighlight();
      initNavBar();
    }

    /** Add syntax highlighting to code blocks */
    function initSyntaxHighlight() {

      // Ensure all <pre> blocks will have syntax highlighting
      $('pre').addClass('prettyprint');

      // Due to markdown's innability to correctly handle code blocks within list items,
      // we have to remove the excess whitespace manually. Gah!
      $('code').each(function() {
        this.innerHTML = this.innerHTML.replace(/^\s{0,4}/mg, '');
      });

      // Add syntax highlighting to <pre> blocks.
      prettyPrint();
    }

    /** Control the positioning of the navbar */
    function initNavBar() {

      var win = $(window);
      var nav = $('.side-nav');
      var navOffset = nav.offset();

      function onWinScroll() {

        var marginTop = (win.scrollTop() >= navOffset.top) ? win.scrollTop() - navOffset.top + 20 : 0;

        nav.css({
          marginTop: marginTop
        });
      }

      function onNavbarResize() {

        win.off('scroll.navbar');

        if (win.height() <= nav.height() || win.width() <= 1024) {
          return nav.css({
            marginTop: 0
          });
        }

        win.on('scroll.navbar', onWinScroll);
        win.trigger('scroll.navbar');
      }

      win.on('resize.navbar', onNavbarResize);
      win.trigger('resize.navbar');
    }

    /** Pubic API */
    return {
      init: init
    };
  }());

  Docs.init();
}());