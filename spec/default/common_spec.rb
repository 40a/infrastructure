require "spec_helper"
=begin

# TYPO3 Server Team / chef takes care of that?
run_list(
  "recipe[ntp]",
  "recipe[sudo]",
  "recipe[git]",
  "recipe[openssl]",
  "recipe[subversion]",
  "recipe[build-essential]",
  "recipe[ruby]"
)

%w[
  git-core
  java
  ruby
].each do |pkg|
  describe package(pkg) do
    pending "To be implemented"
    it { should be_installed }
  end
end

describe command('git --version') do
  it { should return_stdout /^git version/ }
end

describe command('java -version') do
  it { should return_stdout /^openjdk/ }
  it { should_not return_stdout /^sun/ }
end
=end
