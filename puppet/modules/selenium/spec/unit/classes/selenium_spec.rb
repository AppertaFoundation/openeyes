require 'spec_helper'

describe 'selenium', :type => :class do

  shared_examples 'selenium' do |params|
    # XXX need to test $url
    p = {
      :user               => 'selenium',
      :group              => 'selenium',
      :install_root       => '/opt/selenium',
      :java               => 'java',
      :version            => DEFAULT_VERSION,
      :url                => '',
      :download_timeout   => '90',
      :nocheckcertificate => false,
    }

    if params
      p.merge!(params)
    end

    # The new download URL has the major.minor version as a path component but
    # excludes the .patch. Eg.
    # https://selenium-release.storage.googleapis.com/<major>.<minor>/selenium-server-standalone-<major>.<minor>.<patch>.jar

    path_version = p[:version].match(/^(\d+\.\d+)\./)[1]

    it do
      should contain_user(p[:user]).with(
        :gid        => p[:group],
        :system     => true,
        :managehome => true,
        :home       => '/var/lib/selenium'
      )
      should contain_group(p[:group]).with(
        :system => true
      )
      should contain_class('wget')
      should contain_class('selenium').with_version(p[:version])
      should contain_file("#{p[:install_root]}").with({
        'ensure' => 'directory',
        'owner'  => p[:user],
        'group'  => p[:group],
      })
      should contain_file("#{p[:install_root]}/jars").with({
        'ensure' => 'directory',
        'owner'  => p[:user],
        'group'  => p[:group],
      })
      should contain_file("#{p[:install_root]}/log").with({
        'ensure' => 'directory',
        'owner'  => p[:user],
        'group'  => p[:group],
        'mode'   => '0755',
      })
      should contain_file('/var/log/selenium').with({
        'ensure' => 'link',
        'owner'  => 'root',
        'group'  => 'root',
        'target' => "#{p[:install_root]}/log",
      })
      should contain_wget__fetch('selenium-server-standalone').with({
        'source'             => "https://selenium-release.storage.googleapis.com/#{path_version}/selenium-server-standalone-#{p[:version]}.jar",
        'destination'        => "#{p[:install_root]}/jars/selenium-server-standalone-#{p[:version]}.jar",
        'timeout'            => p[:download_timeout],
        'nocheckcertificate' => p[:nocheckcertificate],
        'execuser'           => p[:user],
      })
      should contain_logrotate__rule('selenium').with({
        :path          => "#{p[:install_root]}/log",
        :rotate_every  => 'weekly',
        :missingok     => true,
        :rotate        => 4,
        :compress      => true,
        :delaycompress => true,
        :copytruncate  => true,
        :minsize       => '100k',
      })
    end
  end

  context 'for osfamily RedHat' do
    let(:facts) {{ :osfamily => 'RedHat' }}

    context 'no params' do
      it_behaves_like 'selenium', {}
    end

    context 'user => foo' do
      p = { :user => 'foo' }
      let(:params) { p }

      it_behaves_like 'selenium', p
    end

    context 'user => []' do
      p = { :user => [] }
      let(:params) { p }

      it 'should fail' do
        expect {
          should contain_class('selenium')
        }.to raise_error
      end
    end

    context 'group => foo' do
      p = { :group => 'foo' }
      let(:params) { p }

      it_behaves_like 'selenium', p
    end

    context 'group => []' do
      p = { :group => [] }
      let(:params) { p }

      it 'should fail' do
        expect {
          should contain_class('selenium')
        }.to raise_error
      end
    end

    context 'install_root => /foo/selenium' do
      p = { :install_root => '/foo/selenium' }
      let(:params) { p }

      it_behaves_like 'selenium', p
    end

    context 'install_root => []' do
      p = { :install_root => [] }
      let(:params) { p }

      it 'should fail' do
        expect {
          should contain_class('selenium')
        }.to raise_error
      end
    end

    context 'java => /opt/java' do
      p = { :java => '/opt/java' }
      let(:params) { p }

      it_behaves_like 'selenium', p
    end

    context 'java => []' do
      p = { :java => [] }
      let(:params) { p }

      it 'should fail' do
        expect {
          should contain_class('selenium')
        }.to raise_error
      end
    end

    context 'download_timeout => 42' do
      p = { :download_timeout => '42' }
      let(:params) { p }

      it_behaves_like 'selenium', p
    end

    context 'download_timeout => []' do
      p = { :download_timeout => [] }
      let(:params) { p }

      it 'should fail' do
        expect {
          should contain_class('selenium')
        }.to raise_error
      end
    end

    context 'nocheckcertificate => true' do
      p = { :nocheckcertificate => true }
      let(:params) { p }

      it_behaves_like 'selenium', p
    end

    context 'nocheckcertificate => []' do
      p = { :nocheckcertificate => [] }
      let(:params) { p }

      it 'should fail' do
        expect {
          should contain_class('selenium')
        }.to raise_error
      end
    end
  end

end
