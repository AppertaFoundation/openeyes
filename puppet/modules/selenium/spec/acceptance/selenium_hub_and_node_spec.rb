require 'spec_helper_acceptance'

describe 'selenium::hub & selenium::node on the same host' do
  after(:all) do
    shell "service seleniumhub stop"
    shell "service seleniumnode stop"
  end

  describe 'running puppet code' do
    # Using puppet_apply as a helper
    it 'should work with no errors' do
      pp = <<-EOS
        include java
        Class['java'] -> Class['selenium::hub']
        Class['java'] -> Class['selenium::node']

        class { 'selenium::hub': }
        class { 'selenium::node': }
      EOS

      # Run it twice and test for idempotency
      apply_manifest(pp, :catch_failures => true)
      apply_manifest(pp, :catch_changes => true)
    end
  end

  ['seleniumhub', 'seleniumnode'].each do |service|
    describe service(service) do
      it { should be_running }
      it { should be_enabled }
    end
  end
end
