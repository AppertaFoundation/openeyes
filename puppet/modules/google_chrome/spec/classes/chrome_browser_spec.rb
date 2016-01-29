require 'spec_helper'

describe 'google_chrome' do

  context 'with Debian osfamily' do
    let :facts do
      {
          :osfamily  => 'Debian',
          :lsbdistid => 'Debian',
      }
    end

    it do
      should contain_class('google_chrome::config')
      should contain_class('google_chrome::install')
      should contain_apt__source('google-chrome')
      should contain_package('google-chrome-stable').with(
        :ensure => 'installed',
      )
     end
  end

  context 'with Fedora operatingsystem' do
    let :facts do
      {
          :osfamily        => 'RedHat',
          :operatingsystem => 'Fedora',
          :lsbdistid       => 'Fedora',
      }
    end

    it do
      should contain_class('google_chrome::config')
      should contain_class('google_chrome::install')
      should contain_yumrepo('google-chrome')
      should contain_package('google-chrome-stable').with(
        :ensure => 'installed',
      )
    end
  end

  context 'with Suse osfamily' do
    let :facts do
      {
          :osfamily        => 'Suse',
          :operatingsystem => 'OpenSuse',
          :lsbdistid       => 'OpenSuse',
      }
    end

    it do
      should contain_class('google_chrome::config')
      should contain_class('google_chrome::install')
      should contain_zypprepo('google-chrome')
      should contain_package('google-chrome-stable').with(
        :ensure => 'installed',
      )
    end
  end

  context 'with invalid osfamily' do
    let :facts do
      {
          :osfamily => 'Darwin',
      }
    end

    it 'should fail' do
      expect { should compile }.to raise_error(/Unsupported operating system family/)
    end
  end

  context 'with invalid chrome version' do
    let :facts do
      {
          :osfamily => 'Debian',
      }
    end

    let :params do
      {
          :version => 'test-version',
      }
    end

    it 'should fail' do
      expect { should compile }.to raise_error(/does not match/)
    end
  end

  context 'with version => unstable' do
    let :facts do
      {
          :osfamily  => 'Debian',
          :lsbdistid => 'Debian',
      }
    end

    let :params do
      {
          :version => 'unstable',
      }
    end

    it do
      should contain_class('google_chrome::config')
      should contain_class('google_chrome::install')
      should contain_package('google-chrome-unstable').with(
        :ensure => 'installed',
      )
    end
  end
end
