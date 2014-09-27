require "spec_helper"

describe package("nginx") do
  it {
    should be_installed
  }
end

describe service("nginx") do
  it {
    pending "To be implemented"
    should be_enabled
    should be_running
    # should be_monitored_by("monit")
  }
end

describe port(80) do
  it {
    should be_listening
  }
end

describe port(443) do
  it {
    pending "To be implemented"
    should be_listening
  }
end
