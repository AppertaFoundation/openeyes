require 'spec_helper_acceptance'

describe 'selenium::hub class' do
  after(:all) do
    shell "service seleniumhub stop"
  end

  describe 'running puppet code' do
    # Using puppet_apply as a helper
    it 'should work with no errors' do
      pp = <<-EOS
        include java
        Class['java'] -> Class['selenium::hub']

        class { 'selenium::hub': }
      EOS

      # Run it twice and test for idempotency
      apply_manifest(pp, :catch_failures => true)
      apply_manifest(pp, :catch_changes => true)
    end
  end

  describe file('/etc/init.d/seleniumhub') do
    it { should be_file }
    it { should be_owned_by 'root' }
    it { should be_grouped_into 'root' }
    it { should be_mode 755 }
  end

  %w[hub_stdout.log hub_stderr.log].each do |file|
    describe file("/opt/selenium/log/#{file}") do
      it { should be_file }
      it { should be_owned_by 'selenium' }
      it { should be_grouped_into 'selenium' }
      if fact('operatingsystem') == 'Ubuntu'
        it { should be_mode 664 }
      else
        it { should be_mode 644 }
      end
    end
  end

  describe service('seleniumhub') do
    it { should be_running }
    it { should be_enabled }
  end

  describe process('java') do
    its(:args) { should match /-role hub/ }
    it { should be_running }
  end

  pending('daemon is very slow to start listening') do
    describe port(4444) do
      it { should be_listening.with('tcp') }
    end
  end
end
