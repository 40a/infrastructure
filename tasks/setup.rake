#
# Setup environment after initial checkout
#
# Creates necessary files and folders to work with the repo.
#
# The idea is to provide a simple command that installs all requirements.
# This allows us to speed up on-boarding and to avoid common errors
# due to missing files, plugins etc.
#
# This task can be executed multiple times and asks before overriding things.
#

desc "Setup repo after initial checkout"
task :setup do
  puts "WARN Please remove the vagrant gem and install vagrant as a package: http://downloads.vagrantup.com/" if `which vagrant`.include?("/gems/")
  puts "WARN Vagrant seems not installed as a package or is not in the PATH." unless `which vagrant`
  puts "WARN Vagrant version is not correct." unless `vagrant --version`.include?("1.6.6")
  Rake::Task["install"].invoke
end


desc "Install gems"
task :install do
  puts "Installing vagrant plugins..."
  %w[
    vagrant-cachier
    vagrant-vbox-snapshot
  ].each do |plugin|
    system("vagrant plugin install #{plugin}") unless `vagrant plugin list`.include? plugin
  end

  puts "Fetching all rubygems now..."
  system("bundle")

  puts "Refresh ruby shims..."
  system("rbenv rehash") if `which rbenv`

  puts "All set. Enjoy!"
end
