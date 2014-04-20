require "spec_helper"
=begin
describe package("nginx") do
  it { should be_installed }
end

describe service("nginx") do
  it { should be_enabled }
  it { should be_running }
  it { should be_monitored_by("monit") }
end

describe port(80) do
  it { should be_listening }
end

describe port(443) do
  it { should be_listening }
end

describe file("/etc/httpd/conf/httpd.conf") do
  it { should be_file }
  its(:content) { should match /ServerName default/ }
end
=end
