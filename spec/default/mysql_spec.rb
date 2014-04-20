require "spec_helper"

describe service("mysql") do
  it { should be_enabled }
  it { should be_running }
end

describe port("3306") do
  it { should be_listening }
end

describe command('mysql -u root -p test < echo "SELECT 1 FROM test"') do
  it {
    pending "Get password and SQL command"
    should return_stdout 'TRUE'
  }
end
