#
# Wrapper to execute spec tests
#

desc "Runs serverspec on a given VM (execute `vagrant status` to see valid names)"
task :serverspec, :name do |t, args|
  args.with_defaults(:name => "default")
  if system "vagrant status #{args.name}"
    sh "VAGRANT_VM_NAME=#{args.name} bundle exec rspec spec/#{args.name}"
  else
    abort "Please check the VM name '#{args.name}' for typos. Aborting now."
  end
end
