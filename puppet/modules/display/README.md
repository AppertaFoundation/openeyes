## puppet-display

Module allows to connect to remote machine display using x11vnc + Xvfb.

Created for easy workflow of Selenium tests in Vagrant.

### Example

Add to your manifest:

    class {'display':}

Now, forward default VNC port to your host:

    # Vagrantfile
    Vagrant.configure('2') do |config|
      config.vm.network :forwarded_port, guest: 5900, host: 5900
    end

Reload your VM box.

You can now see your tests running on VM by connecting with any VNC viewer to `localhost:5900`.

You can also use SSH tunnel instead of port forwarding as some clients forbid connecting to localhost.

### Configuration

You can configure display, resolution and color depth:

    class {'display':
      display => 99,   # default is 0
      width   => 1024, # default is 1280
      height  => 768,  # default is 800
      color   => 24,   # default is "24+32" (i.e. 32-bit)
    }

### Support

Supports RedHat and Debian families.
