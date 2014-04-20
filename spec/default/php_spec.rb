require "spec_helper"
=begin

run_list(
  "recipe[java]",
#  "recipe[php::pear]",
  "recipe[php::xdebug]",
  "recipe[varnish]",
  "recipe[nginx]",
  "recipe[jenkins]",
#  "recipe[typo3]",
  "recipe[pear_config]",
  "recipe[pirum]",
  "recipe[pirum::nginx]",
  "recipe[sonar]",
  "recipe[sonar::nginx]"
)


# tree -L 1 /var/lib/jenkins/plugins/ | grep -v -e "\(\.bak\|\.hpi\)$"
override_attributes(
  "pear_channel" => {
    "port" => "80",
    "hostname" => "pear.typo3.local",
    "name" => "TYPO3 PEAR channel",
    "repository" => "typo3",
    "webroot" => "/var/www/"
  },

describe file("/usr/local/etc/php.ini") do
  it { should be_file }
end

# TODO ct 2014-04-21 Can we limit PHP settings to jenkins
# in order to have pirum unaffected?
describe "PHP config parameters" do
  context  php_config("safe_mode") do
    its(:value) { should eq "off" }
  end

  context  php_config("max_execution_time") do
    its(:value) { should eq "30m" }
  end

  context  php_config("memory_limit") do
    its(:value) { should eq "10G" }
  end
end

=end
