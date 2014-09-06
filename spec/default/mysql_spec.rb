require "spec_helper"

describe service("mysql") do
  it {
    should be_enabled
    should be_running
  }
end

describe port("3306") do
  it {
    should be_listening
  }
end
