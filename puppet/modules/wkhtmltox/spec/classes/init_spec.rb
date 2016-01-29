require 'spec_helper'
describe 'wkhtmltox' do

  context 'with defaults for all parameters' do
    it { should contain_class('wkhtmltox') }
  end
end
