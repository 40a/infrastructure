Vagrant.configure("2") do |config|
  config.vm.box = "debian-6-amd64"
  config.vm.box_url = [
    "vendor/lucid64.box",
    "http://st-g.de/fileadmin/downloads/2012-09/squeeze.box"
  ]

  config.vm.network "private_network", ip: "192.168.33.10"

  if Vagrant.has_plugin?("vagrant-cachier")
    config.cache.scope = :box
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
    ansible.verbose = "v"
    ansible.groups = {
      "metrics" => ["default"],
      "metrics_server_dkd" => ["default"],
    }
  end
end
