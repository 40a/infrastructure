#
# Wrapper to execute spec tests
#

desc "Runs serverspec on a given VM. Defaults to: default. Execute `vagrant status` to see valid names"
task :spec, :name do |t, args|
  args.with_defaults(:name => "default")
  if system "vagrant status #{args.name} > /dev/null"
    sh "VAGRANT_VM_NAME=#{args.name} bundle exec rspec spec/#{args.name}"
  else
    abort "Please check the VM name '#{args.name}' for typos. Aborting now."
  end
end
