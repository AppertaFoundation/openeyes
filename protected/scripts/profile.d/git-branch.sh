
# If not running interactively, don't do anything
[ -z "$PS1" ] && return

HISTSIZE=1000
HISTFILESIZE=2000
shopt -s checkwinsize

function gitbranch {
  if branch=$(git rev-parse --abbrev-ref HEAD 2>/dev/null); then
    if [ "$branch" = "HEAD" ]; then
      branch=$(git describe --all 2>/dev/null);
    fi
  else
    branch="";
  fi

  if [ ! "$branch" = "" ]; then
    git diff --quiet
    if [ $? = 1 ]; then branch="$branch (*)"; fi
  fi
  if [ ! "$branch" = "" ]; then echo " $branch "; fi
}
PS1="\e[0m\n\e[44m\e[97m \u@\h \e[41m\$(gitbranch)\e[0m\n\w>"
