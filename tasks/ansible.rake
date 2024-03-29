#
# Tasks for managing Ansible assets
#

namespace :ansible do
  namespace :create do
    desc "Create a new role"
    task :role, :name do |t, args|
      sh "ansible-galaxy init --force --init-path=roles #{args.name}"
    end
  end

  desc "Test roles with syntax-check"
  task :syntax_check do
    sh "ansible-playbook --syntax-check -i host.ini playbook.yml"
  end
end
