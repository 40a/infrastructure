#
# Wrapper task for role handling
#

namespace :role do
  desc "Create a new role"
  task :create, :name do |t, args|
    sh "ansible-galaxy init --force --init-path=roles #{args.name}"
  end

  desc "Test roles with syntax-check"
  task :test do
    sh "ansible-playbook --syntax-check -i host.ini playbook.yml"
  end
end
