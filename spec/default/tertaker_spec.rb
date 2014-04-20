require "spec_helper"
=begin
# TODO ct 2014-04-21 should have logrotate on logs

%w[
  .tertaker/logs/
  .tertaker/tmp/
  .tertaker/repositories/
  .tertaker/sandbox/
  .tertaker/ter/
  .tertaker/jobs/
].each do |dir|
  describe file("/var/lib/jenkins/#{dir}") do
    it { should be_directory }
    it { should be_readable.by_user("jenkis") }
  end
end

describe package("tertaker") do
  it { should be_installed.by("gem").with_version("0.0.6") }
end

describe command("tertaker version") do
  it { should return_stdout "0.0.6" }
end

describe cron do
  it { should have_entry '* */6 * * * tertaker run' }
end
=end
