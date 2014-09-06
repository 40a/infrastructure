require "spec_helper"
=begin
# TODO Test PHP CLI here ONLY!
describe file("/usr/local/etc/php.ini") do
  it { should be_file }
end

describe "PHP config parameters" do
  context  php_config("max_execution_time") do
    its(:value) { should eq "30m" }
  end

  context  php_config("memory_limit") do
    its(:value) { should eq "10G" }
  end
end
=end
