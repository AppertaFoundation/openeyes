require 'spec_helper'

describe 'selenium::config', :type => :define do
  shared_examples 'config' do |params|
    let :pre_condition do
      "include selenium"
    end

    p = {
      :display      => ':0',
      :user         => 'selenium',
      :install_root => '/opt/selenium',
      :jar_name     => "selenium-server-standalone-#{DEFAULT_VERSION}.jar",
      :options      => '-Dwebdriver.enable.native.events=1',
      :java         => 'java',
    }

    p.merge!(params) if params

    it do
      should contain_file("selenium#{title}").with({
        'ensure' => 'file',
        'path'   => "/etc/init.d/selenium#{title}",
        'owner'  => 'root',
        'group'  => 'root',
        'mode'   => '0755',
      }).
        with_content(/# selenium#{title} Selenium server init script/).
        with_content(/SLNM_DISPLAY='#{p[:display]}'/).
        with_content(/SLNM_USER='#{p[:user]}'/).
        with_content(/SLNM_INSTALL_ROOT='#{p[:install_root]}'/).
        with_content(/SLNM_JAR_NAME='#{p[:jar_name]}'/).
        with_content(/SLNM_OPTIONS='#{p[:options]}'/).
        with_content(/SLNM_JAVA='#{p[:java]}'/).
        with_content(/SLNM_LOG_NAME='#{title}'/).
        with_content(/prog='selenium#{title}'/)
      should contain_service("selenium#{title}").with({
        :ensure     => 'running',
        :hasstatus  => 'true',
        :hasrestart => 'true',
        :enable     => 'true',
      })
    end
  end

  context 'for osfamily RedHat' do
    let(:facts) {{ :osfamily => 'RedHat' }}

    context "server" do
      let(:title) { 'server' }

      context 'no params' do
        it_behaves_like 'config', {}
      end

      context 'all params' do
        params = {
          :display      => 'X:0',
          :user         => 'Xselenium',
          :install_root => 'X/opt/selenium',
          :jar_name     => 'Xselenium-server-standalone-x.xx.x.jar',
          :options      => 'X-Dwebdriver.enable.native.events=1',
          :java         => 'Xjava',
        }

        let(:params) { params }

        it_behaves_like 'config', params
      end
    end

    context "hub" do
      let(:title) { 'hub' }

      context 'no params' do
        it_behaves_like 'config', {}
      end

      context 'all params' do
        params = {
          :display      => 'X:0',
          :user         => 'Xselenium',
          :install_root => 'X/opt/selenium',
          :jar_name     => 'Xselenium-server-standalone-x.xx.x.jar',
          :options      => 'X-Dwebdriver.enable.native.events=1',
          :java         => 'Xjava',
        }

        let(:params) { params }

        it_behaves_like 'config', params
      end
    end
  end

  context 'for osfamily Debian' do
    let(:title) { 'server' }
    let(:facts) {{ :osfamily => 'Debian' }}
    let :pre_condition do
      "include selenium"
    end

    it { should contain_package('daemon').with_ensure('present') }
  end

end
