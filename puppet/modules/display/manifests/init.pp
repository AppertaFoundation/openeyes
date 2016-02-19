# == Class: display
#
# Installs and configures Xvfb and x11vnc.
#
# === Parameters
#
# [*display*]
#    X display to use. Default is 0.
# [*width*]
#    Screen width to use. Default is 1280.
# [*height*]
#    Screen height to use. Default is 800.
# [*color*]
#    Screen color depth to use. Default is "24+32" (32 bit).
#
# === Examples
#
#  class {'display':
#    display => 99,
#    width   => 1024,
#    height  => 768,
#    color   => 24,
#  }
#
# === Authors
#
# Alex Rodionov <p0deje@gmail.com>
#
# === Copyright
#
# Copyright 2013 Alex Rodionov.
#
class display(
  $display = 0,
  $width   = 1280,
  $height  = 768,
  $color   = '24+32'
) inherits display::params {
  include env
  include x11vnc
  include xvfb

  Class['xvfb'] -> Class['x11vnc'] -> Class['env']
}
