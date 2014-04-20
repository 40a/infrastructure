require "highline/import"
require "serverspec"
require "pathname"
require "net/ssh"
require "pp"

include SpecInfra::Helper::Ssh
include SpecInfra::Helper::DetectOS
include Serverspec::Helper::Properties

RSpec.configure do |c|
  if ENV["ASK_SUDO_PASSWORD"]
    c.sudo_password = ask("Enter sudo password: ") { |q| q.echo = false }
  else
    c.sudo_password = ENV["SUDO_PASSWORD"]
  end

  target_vm  = ENV["VAGRANT_VM_NAME"]

  c.ssh.close if c.ssh

  say "running 'vagrant up #{target_vm}'"
  say "this can take quite a while, if the box is not yet up!"
  vagrant_up = `vagrant up #{target_vm}`
  say "running 'vagrant ss-config #{target_vm}'"
  config = `vagrant ssh-config #{target_vm}`

  if config != ""
    host = user = keys = port = ""

    config.each_line do |line|
      if match = /HostName (.*)/.match(line)
        host = match[1]
      elsif  match = /User (.*)/.match(line)
        user = match[1]
      elsif match = /IdentityFile (.*)/.match(line)
        keys =  [match[1].gsub(/"/,'')]
      elsif match = /Port (.*)/.match(line)
        port = match[1]
      end
    end

    options = Net::SSH::Config.for(host)
    options[:port] = port
    options[:keys] = keys

    c.ssh   = Net::SSH.start(host, user, options)
  else
    raise("Could not find ssh configuration for #{target_vm}")
  end

end
