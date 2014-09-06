require "spec_helper"

describe user("jenkins") do
  it { should exist }
  it { should have_home_directory '/var/lib/jenkins' }
  it { should have_login_shell '/bin/bash' }
  it { should have_authorized_key /ssh-rsa/ }
end

describe package("jenkins") do
  it { should be_installed }
end

describe service("jenkins") do
  pending "To be implemented"
  it { should be_enabled }
  it { should be_running }
end

describe port(8080) do
  it { should be_listening }
end

describe file("/var/lib/jenkins/config.xml") do
  it { should be_file }
#  its(:content) { should match /TYPO3/ }
end

describe command("java -jar /var/lib/jenkins/jenkins-cli.jar -i /var/lib/jenkins/.ssh/id_rsa -s http://localhost:8080 help") do
  it { should return_stdout "PONG" }
end
