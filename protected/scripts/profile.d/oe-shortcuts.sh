alias oec="$SCRIPTDIR/oe-checkout.sh"
__git_complete oec _git_checkout 2>/dev/null
alias oemig="$SCRIPTDIR/oe-migrate.sh"
alias oereset="$SCRIPTDIR/oe-reset.sh"
__git_complete oereset _git_checkout 2>/dev/null
alias oeu="$SCRIPTDIR/oe-update.sh"
alias oefix="$SCRIPTDIR/oe-fix.sh"
alias oewhich="$SCRIPTDIR/oe-which.sh"
alias oe-behat-tests="$SCRIPTDIR/oe-behat-tests.sh"
alias oebehattests="oe-behat-tests"
alias oeunittests="$SCRIPTDIR/oe-unit-tests.sh"
alias oe-checkout="oec"
alias oe-migrate="oemig"
alias oe-reset="oereset"
alias oe-update="oeu"
alias oe-fix="oefix"
alias oe-which="oewhich"
alias oe-unit-tests="oeunittests"
alias oetests="oebehattests && oeunittests"
alias oet="oetests"

alias cdoe="cd $WROOT"
alias cded="cd $WROOT/protected/modules/eyedraw"
alias cdnb="cd $WROOT/protected/assets/newblue"
alias cdsample="cd $WROOT/protected/modules/sample/sql"
alias cdscripts="cd $SCRIPTDIR"
alias cdiolm="cd $WROOT/protected/javamodules/IOLMasterImport"
alias cdprotected="cd $WROOT/protected"
alias cdconfig="cd $WROOT/protected/config/local"
alias cdtests="cd $WROOT/protected/tests"

alias oelogson="tail $WROOT/protected/runtime/application.log -f &"
alias oelogsoff="kill -9 $(pgrep -f protected/runtime/application.log)"

alias oeviewlogs="tail -n 150 $WROOT/protected/runtime/application.log"
alias oeviewmigratelogs="more $WROOT/protected/runtime/migrate.log"

alias oe-reloadaliases="$SCRIPTDIR/set-profile.sh --no-envs && source /etc/profile.d/oe-shortcuts.sh"

alias behat="$WROOT/vendor/behat/behat/bin/behat"

alias clearapc="curl http://localhost/apc_clear.php"
alias flushapc=clearapc
