require 'spec_helper'

describe 'selenium::server', :type => :class do

  shared_examples 'server' do |params|
    p = {
      :display => ':0',
      :options => '-Dwebdriver.enable.native.events=1',
    }

    p.merge!(params) if params

    it do
      should contain_class('selenium')
      should contain_selenium__config('server').with({
        'display' => p[:display],
        'options' => p[:options],
      })
      should contain_class('selenium::server')
    end
  end

  context 'for osfamily RedHat' do
    let(:facts) {{ :osfamily => 'RedHat' }}

    context 'no params' do
      it_behaves_like 'server', {}
    end

    context 'display => :42' do
      p = { :display => ':42' }
      let(:params) { p }

      it_behaves_like 'server', p
    end

    context 'display => :42' do
      let(:params) {{ :display => [] }}

      it 'should fail' do
        expect {
          should contain_class('selenium::server')
        }.to raise_error
      end
    end

    context 'options => -foo' do
      p = { :options => '-foo' }
      let(:params) { p }

      it_behaves_like 'server', p
    end

    context 'options => []' do
      let(:params) {{ :options => [] }}

      it 'should fail' do
        expect {
          should contain_class('selenium::server')
        }.to raise_error
      end
    end
  end

end
