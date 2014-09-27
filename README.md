# TYPO3 CI Infrastructure

Infrastructure as Code for these services:

- [ci.typo3.org](https://ci.typo3.org)
- [metrics.typo3.org](https://metrics.typo3.org).
- [pear.typo3.org](https://pear.typo3.org).

The main goal of this repo is to provide a starting point for people who want to contribute to the infrastructure.

[![Build Status](https://travis-ci.org/typo3-ci/infrastructure.svg)](https://travis-ci.org/typo3-ci/infrastructure)


# What's inside?

The infrastructure for these services is completely covered with [Ansible](http://ansible.com/), an easy to read configuration management tool written in Python.

If you want to know how our services work, dig into the `roles` folder and explore their settings.

This repo contains:

- Ansible roles for all parts of ci.typo3.org and metrics.typo3.org
- Vagrantfile to start a local test setup
- Tests to verify that the roles are working correctly
- Rake-Tasks that provide simple commands for common actions


# Requirements

In order to setup a local development environment you need these tools:

- Python 2.6+
- [Ansible 1.6 or higher](http://docs.ansible.com/intro_installation.html)
- [Vagrant 1.6 or higher](http://www.vagrantup.com/downloads.html)
- Ruby 1.9.3 or higher (2.1.0 recommended)

We strongly recommend `rbenv` for installing ruby.

`rbenv` helps you to manage multiple Ruby versions and avoids messing around with your system.
It allows you to install Ruby versions and Rubygems for your user only (without using `sudo` etc.):

- https://github.com/sstephenson/rbenv#installation
- https://github.com/sstephenson/ruby-build#installation
- https://github.com/sstephenson/rbenv-gem-rehash#installation

## Why Ruby?

The testsuite is written in Ruby and in order to execute it you need Ruby.

We also provide [Rake-Tasks](http://rake.rubyforge.org/) e.g. for executing the tests.
Rake tasks are also written in a DSL based on Ruby.


# Installation

    # Check versions
    python --version
    ruby --version
    vagrant --version
    ansible --version

    # Install `bundler` gem to fetch dependencies
    gem install bundler --no-ri --no-rdoc
    # Get the repo
    git clone https://github.com/typo3-ci/infrastructure.git typo3-ci-infrastructure
    cd !$
    # Run setup task to download Vagrant plugins, Rubygems etc. (can be run multiple times)
    rake setup


# FAQ

Q: How do I validate my ansible roles e.g. for syntax checks?<br>
A: Run this command: `rake ansible:syntax_check` (requires a valid password)

Q: How do I execute the server tests?<br>
A: Run these commands:

    vagrant up --provision
    rake spec

The server tests will be executed via SSH on the VM and might take some time to finish.

Q: How do I write server tests?<br>
A: Open a file within `spec/default` folder and extend an existing test.
   You can also create a new `*_spec.rb` file for a new feature or service.
   All files ending with `_spec.rb` are considered test files.

Q: I get strange `bundler` warnings. What shall I do?<br>
A: Run `rake setup` and see if this fixes your problem. Otherwise [file an issue here](https://github.com/typo3-ci/infrastructure/issues).

Q: How do I run ansible on the remote server?<br>
A: Run this command:

    ansible-playbook -i host.ini playbook.yml -vv


# How to contribute

1) Join the organization.<br>
   By joining the organisation you can directly contribute to the code base.<br>
   Please note that you need an account on [typo3.org](https://typo3.org) to join.

2) Fork the repo and provide pull requests.<br>
   This is the best hassle free option.

The changes in the ansible roles are verified by [travis.ci](https://travis.ci) and [ci.typo3.org](https://ci.typo3.org) itself.

We foster true open source spirit in order to inspire people to share.


# File structure

<pre>
.
├── roles                                       - Ansible roles (http://docs.ansible.com/playbooks_roles.html)
├── spec                                        - ServerSpec tests (written in Ruby) http://serverspec.org/
│   ├── default                                 - Tests for the `default` VM (which simulates the ci.typo3.org server)
│   └── spec_helper.rb                          - Setup for ServerSpec
├── tasks                                       - Rake tasks
│   ├── ansible.rake                            - Tasks for validating and creating roles
│   ├── setup.rake                              - Tasks for local setup. Run this after each `git pull --rebase` or whenever you want
│   └── spec.rake                               - Run serverspec tests on the VM
├── vendor                                      - Third party tools and binaries are stored here
├── .travis.yml                                 - Sets up build environment on travis-ci.org: https://travis-ci.org/typo3-ci/infrastructure
├── ansible.cfg                                 - Default settings for ansible
├── Gemfile                                     - Stores all rubygems we use. Install with `bundle install` (similar to `composer install`). See http://bundler.io/ for details
├── Gemfile.lock                                - Dependency tree of rubygems
├── host.ini                                    - The hosts we manage with Ansible
├── playbook.yml                                - The actions we apply on each host
├── Rakefile                                    - Loads Rake tasks from `tasks` folder. Not to be confused with Ansible tasks! Ansible tasks are ONLY stored within `roles`
├── README.md                                   - The file you are reading right now
├── vagrant_ansible_inventory_default           - Generated by Vagrant for local testing (ignored in git)
└── Vagrantfile                                 - Vagrant setup
</pre>


# License

MIT
