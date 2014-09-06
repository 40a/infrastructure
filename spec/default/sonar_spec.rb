require "spec_helper"

describe service("sonar") do
  it { should be_enabled }
  it { should be_running }
end

describe port(9001) do
  it { should be_listening }
end

describe command('mysql -u root < echo "SELECT 1 FROM sonar"') do
  it {
    pending "Get password and SQL command"
    should return_stdout 'TRUE'
  }
end
