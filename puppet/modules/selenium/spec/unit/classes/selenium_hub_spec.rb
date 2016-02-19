require 'spec_helper'

describe 'selenium::hub', :type => :class do

  shared_examples 'hub' do |params|
    p = {
      :options => '-role hub'
    }

    p.merge!(params) if params

    it do
      should contain_class('selenium')
      should contain_selenium__config('hub').with({
        'options' => p[:options],
      })
      should contain_class('selenium::hub')
    end
  end

  context 'for osfamily RedHat' do
    let(:facts) {{ :osfamily => 'RedHat' }}

    context 'no params' do
      it_behaves_like 'hub', {}
    end

    context 'options => -foo' do
      p = { :options => '-foo' }
      let(:params) { p }

      it_behaves_like 'hub', p
    end

    context 'options => []' do
      let(:params) {{ :options => [] }}

      it 'should fail' do
        expect {
          should contain_class('selenium::hub')
        }.to raise_error
      end
    end
  end

end
