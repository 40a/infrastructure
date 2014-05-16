require "serverspec"

include SpecInfra::Helper::DetectOS
include Serverspec::Helper::Properties

# Run tests locally WITHOUT SSH on travis-ci
if ENV["TRAVIS"]
  include SpecInfra::Helper::Exec
else
  # Run tests via SSH when testing locally on a VM
  RSpec.configure do |c|
    require "net/ssh"
    require "highline/import"
    include SpecInfra::Helper::Ssh

    c.before :suite do
      options = {}
      target_vm  = ENV["VAGRANT_VM_NAME"]

      c.ssh.close if c.ssh

      say "running 'vagrant up #{target_vm}'"
      say "this can take quite a while, if the box is not yet up!"
      vagrant_up = `vagrant up #{target_vm}`
      say "running 'vagrant ss-config #{target_vm}'"
      config = `vagrant ssh-config #{target_vm}`

      if config != ""
        config.each_line do |line|
          if match = /HostName (.*)/.match(line)
            c.host = match[1]
          elsif  match = /User (.*)/.match(line)
            options[:user] = match[1]
          elsif match = /IdentityFile (.*)/.match(line)
            options[:keys] =  [match[1].gsub(/"/,"")]
          elsif match = /Port (.*)/.match(line)
            options[:port] = match[1]
          end
        end
        c.ssh = Net::SSH.start(c.host, options[:user], options)
      else
        raise("Could not find ssh configuration for #{target_vm}")
      end
    end
  end
end
