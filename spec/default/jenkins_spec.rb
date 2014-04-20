require "spec_helper"

=begin
run_list(
  "recipe[java]",
  "recipe[php::xdebug]",
  "recipe[varnish]",
  "recipe[nginx]",
  "recipe[jenkins]",
  "recipe[pear_config]",
  "recipe[pirum]",
  "recipe[pirum::nginx]",
  "recipe[sonar]",
  "recipe[sonar::nginx]"
)


# tree -L 1 /var/lib/jenkins/plugins/ | grep -v -e "\(\.bak\|\.hpi\)$"

"jenkins" => {
  "server" => {
    "port" => 8081,
    "host" => "ci.typo3.local",
    "plugins" => [
      "analysis-collector",
      "analysis-core",
      "build-timeout",
      "checkstyle",
      "clover",
      "compact-columns",
      "dashboard-view",
      "disk-usage",
      "doclinks",
      "downstream-buildview",
      "dry",
      "favorite",
      "gerrit-trigger",
      "git",
      "greenballs",
      "groovy",
      "htmlpublisher",
      "hudson-wsclean-plugin",
      "iphoneview",
      "jdepend",
      "monitoring",
      "nested-view",
      "plot",
      "pmd",
      "promoted-builds",
      "redmine",
      "ruby",
      "scriptler",
      "sectioned-view",
      "sidebar-link",
      "sonar",
      "ssh-slaves",
      "subversion",
      "tasks",
      "template-project",
      "tmpcleaner",
      "twitter",
      "view-job-filters",
      "violations",
      "warnings",
      "xunit"
    ]
  },
  "node" => {
   "launcher" => "jnlp"
  },
  "http_proxy" => {
   "variant" => "nginx",
   "host_aliases" => "ci.typo3.local",
   "forward_port" => [ 8080 ],
   "listen_ports" => [ 80 ]
  }
}
=end


=begin
describe user("jenkins") do
  pending "To be implemented"
  it { should exist }
  it { should have_home_directory '/var/lib/jenkins' }
  it { should have_login_shell '/bin/bash' }
  it { should have_authorized_key /ssh-rsa/ }
end

describe package("jenkins") do
  pending "To be implemented"
  it { should be_installed }
end

describe service("jenkins") do
  pending "To be implemented"
  it { should be_enabled }
  it { should be_running }
end

describe port(8081) do
  pending "To be implemented"
  it { should be_listening }
end

describe port(8080) do
  pending "To be implemented"
  it { should be_listening }
end

describe file("/var/lib/jenkins/config.xml") do
  pending "To be implemented"
  it { should be_file }
  its(:content) { should match /TYPO3/ }
end

describe command("java -jar /var/lib/jenkins/jenkins-cli.jar -s http://localhost:8080 help") do
  pending "To be implemented"
  it { should return_stdout "PONG" }
end
=end
