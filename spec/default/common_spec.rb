require "spec_helper"

describe command('git --version') do
  it {
    should return_stdout /^git version/
  }
end

describe command('java -version') do
  it {
    should return_stdout /OpenJDK 64-Bit Server/
  }
end
