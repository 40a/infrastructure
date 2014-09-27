require "spec_helper"

describe user("jenkins") do
  it {
    pending "To be implemented"
    should exist
    should have_home_directory '/var/lib/jenkins'
    should have_login_shell '/bin/bash'
    should have_authorized_key /ssh-rsa/
  }
end

describe package("jenkins") do
  it {
    pending "To be implemented"
    should be_installed
  }
end

describe service("jenkins") do
  it {
    pending "To be implemented"
    should be_enabled
    should be_running
  }
end

describe port(8080) do
  it {
    pending "To be implemented"
    should be_listening
  }
end

describe file("/var/lib/jenkins/config.xml") do
  it {
    pending "To be implemented"
    should be_file
  }
end

# TODO Verify if travis allows us that
# describe command("java -jar /var/lib/jenkins/jenkins-cli.jar -i /var/lib/jenkins/.ssh/id_rsa -s http://localhost:8080 help") do
#   it {
#     pending "To be implemented"
#     should return_stdout "PONG"
#   }
# end
