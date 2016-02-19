# == Class: display::env
#
# Exports DISPLAY variable.
#
# === Authors
#
# Alex Rodionov <p0deje@gmail.com>
#
# === Copyright
#
# Copyright 2013 Alex Rodionov.
#
class display::env {
  $file = '/etc/profile.d/vagrant_display.sh'

  concat { $file:
    owner => root,
    group => root,
    mode  => '0644',
  }

  concat::fragment { 'DISPLAY':
    target  => $file,
    content => "export DISPLAY=:${display::display}",
  }
}
