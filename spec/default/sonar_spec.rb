require "spec_helper"
=begin

run_list(
  "recipe[java]",
  "recipe[maven]",
  "recipe[sonar]",
  "recipe[sonar::proxy_nginx]"
)


  "sonar" => {
    "web_host" => "127.0.0.1",
    "web_port" => "9001",
    "web_domain" => "sonar.typo3.dev",
    "jdbc_username" => "root",
    "jdbc_password" => "8CUSouuHj8AQ0zNAx4fn"
  }

describe package("sonar") do
  it { should be_installed }
end

describe service("sonar") do
  it { should be_enabled }
  it { should be_running }
end

describe port(9001) do
  it { should be_listening }
end

describe file("/etc/httpd/conf/httpd.conf") do
  it { should be_file }
  its(:content) { should match /ServerName default/ }
end
=end
