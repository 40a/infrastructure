# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.box = "debian-6-amd64"
  config.vm.box_url = "http://st-g.de/fileadmin/downloads/2012-09/squeeze.box"

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  # config.vm.network "private_network", ip: "192.168.33.10"

  # If true, then any SSH connections made will enable agent forwarding.
  # Default value: false
  # config.ssh.forward_agent = true

  # Detects vagrant-cachier plugin
  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.auto_detect = true
    config.cache.scope = :box
    # If you are using VirtualBox, you might want to enable NFS for shared folders
    #config.cache.synced_folder_opts = {
    #  type: :nfs,
    #  mount_options: ['rw', 'vers=3', 'tcp', 'nolock']
    #}
  else
    puts "WARN:  Vagrant-cachier plugin not detected. Continuing unoptimized."
  end

  config.vm.provider "virtualbox" do |vb|
    vb.gui = false

    vb.customize [ "modifyvm", :id,
      "--memory", "1024"
    ]
  end

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "playbook.yml"
    ansible.sudo = true
    ansible.verbose = "vv"
    ansible.groups = {
      "metrics" => ["default"],
    }
  end
end
