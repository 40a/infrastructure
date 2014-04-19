# TYPO3 CI Infrastructure

Infrastructure as Code for [ci.typo3.org](https://ci.typo3.org) and [metrics.typo3.org](https://metrics.typo3.org).

The main goal of this repo is to provide a simple starting point for people who want to contribute to the infrastructure and QA processes for TYPO3.

We've learned, that the current approach of contributing to the TYPO3 infrastructure (based on [chef](http://www.getchef.com/chef/)) does not work well when collaborating with different teams.

This repo is a new approach how we can make contributions to the infrastructure easier.

[![Build Status](https://travis-ci.org/typo3-ci/infrastructure.svg)](https://travis-ci.org/typo3-ci/infrastructure)


# Important

This repo is *work in progress*, as we still collect requirements and ideas how to make the setup even simpler.


# What's inside?

The infrastructure for these services is completely covered with [Ansible](http://ansible.com/), an easy to read configuration management tool written in Python.

If you want to know how our services work, dig into the `roles` folder and explore their settings.

This repo contains:

- Ansible roles for all parts of ci.typo3.org and metrics.typo3.org
- Vagrantfile to start a local test setup e.g. to verify the roles are working correctly
- Rake-Tasks that wrap common actions (e.g. testing or creating roles)


# Requirements

In order to setup a local development environment you need these tools:

- Python 2.6+
- [Ansible 1.5.5 or higher](http://docs.ansible.com/intro_installation.html)
- [Vagrant 1.3.5 or higher](http://www.vagrantup.com/downloads.html)
- Ruby 1.9.3 or higher (2.1.0 recommended)

We strongly recommend `rbenv` for installing ruby, as it makes our lifes easier:

- https://github.com/sstephenson/rbenv#installation
- https://github.com/sstephenson/ruby-build#installation
- https://github.com/sstephenson/rbenv-gem-rehash#installation

If you never worked with ruby before and wonder why we need Ruby and `rbenv`:

The testsuite is written in Ruby and in order to execute it you need Ruby.

We also provide [Rake](http://rake.rubyforge.org/)-Tasks e.g. for executing the tests.
Rake tasks are also written in Ruby.

`rbenv` helps you to manage multiple Ruby versions and avoids messing around with your system.
It allows you to install Ruby versions and Rubygems for your user only (without using `sudo` etc.).


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
A: Run this command: `rake role:test`

Q: How do I execute the server tests?<br>
A: Run these commands:

    vagrant up --provision
    rake serverspec

The server tests will be executed via SSH on the VM and might take some time to finish.

Q: How do I write server tests?<br>
A: Open a file within `spec/default` folder and extend an existing test.
   You can also create a new `*_spec.rb` file for a new feature or service.
   All files ending with `_spec.rb` are considered test files.

Q: I get strange `bundler` warnings. What shall I do?<br>
A: Run `rake setup` and see if this fixes your problem. Otherwise [file an issue here](https://github.com/typo3-ci/infrastructure/issues).


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
├── ansible.cfg                                 - Default settings for ansible
├── features                                    - Highlevel Feature descriptions (TODO convert to serverspecs)
├── Gemfile                                     - Stores all rubygems we use. Install with `bundle install` (similar to `composer install`). See http://bundler.io/ for details
├── Gemfile.lock                                - Dependency tree of rubygems
├── host.ini                                    - The hosts we manage with Ansible
├── playbook.yml                                - The actions we apply on each host
├── Rakefile                                    - Loads Rake tasks from `tasks` folder. Not to be confused with Ansible tasks! Ansible tasks are ONLY stored within `roles`
├── README.md                                   - The file you are reading right now
├── roles                                       - Ansible roles (http://docs.ansible.com/playbooks_roles.html)
│   ├── common                                  -
│   ├── java                                    -
│   ├── jenkins                                 -
│   ├── mysql                                   -
│   ├── nginx                                   -
│   ├── pear                                    -
│   ├── php                                     -
│   ├── pirum                                   -
│   ├── redis                                   -
│   ├── sonar                                   -
│   ├── ssl                                     -
│   └── tertaker                                -
├── spec                                        - ServerSpec tests (written in Ruby) http://serverspec.org/
│   ├── default                                 - Tests for the `default` VM (which simulates the ci.typo3.org server)
│   └── spec_helper.rb                          - Setup for ServerSpec
├── tasks                                       - Rake tasks
│   ├── role.rake                               - Tasks for validating & creating roles
│   ├── setup.rake                              - Tasks for local setup. Run this after each `git pull --rebase` or when ever you want
│   └── spec.rake                               - Run serverspec tests on the VM
├── .travis.yml                                 - Sets up build environment on travis-ci.org: https://travis-ci.org/typo3-ci/infrastructure
├── vagrant_ansible_inventory_default           - Generated by Vagrant for local testing. Ignored
├── Vagrantfile                                 - Vagrant setup
└── vendor                                      - Third party tools and binaries are stored here
</pre>


# TODOS & IDEAS

## Christian

- Create CI pipeline for this repo on ci.typo3.org
- Replace `tertaker` with Job DSL and job tasks.
  Store Repo within the Job
  Perform `tertaker` actins in each Job (managed by Job DSL)
- Idea: create docker packages for these services that are plugged into TYPO3's infrastructure


# Migration

In order to provide the same functionality in Ansible we have right now with Chef cookbooks we need to convert the following roles and cokbooks:

## roles

- [ ] base                  - managed by Server Team via chef
- [X] typo3_ci_master       -
- [ ] typo3_ci_slave        - obsolete
- [X] typo3_metrics_master  -
- [ ] ubuntu                - managed by Server Team via chef


## cookbooks

- [ ] apache2          - obsolete
- [ ] apt              - managed by Server Team via chef
- [X] build-essential  -
- [X] git              -
- [X] java             -
- [X] jenkins          - ssh_keys for Gerrit etc. (https://github.com/TYPO3-cookbooks/site-citypo3org)
- [X] jenkins scripts  - Scriptler / Groovy scripts for maintenance
- [X] maven            -
- [X] mysql            -
- [X] nginx            - 500x error messages!
- [ ] ntp              - managed by Server Team via chef
- [ ] openssl          - managed by Server Team via chef
- [X] pear             -
- [X] pear_config      - can these packages be installed via composer nowadays? Is there a better approach?
- [X] php              - Include modules e.g. `xdebug`
- [X] pirum            - https://github.com/TYPO3-cookbooks/pirum
- [ ] redmine          - obsolete
- [X] ruby             -
- [ ] runit            - obsolete
- [X] sonar            - https://github.com/TYPO3-cookbooks/sonar
- [X] subversion       -
- [ ] sudo             - managed by Server Team via chef
- [ ] tertaker         - Repos / ruby scripts / rake tasks. Can be replaced by Job-DSL?
- [ ] typo3_ci         - obsolete
- [ ] varnish          - obsolete

- [ ] ci.typo3.org
- [ ] metrics.typo3.org
- [ ] pear.typo3.org


## Links

- https://github.com/ICTO/ansible-jenkins
- https://github.com/jgrowl/ansible-playbook-jenkins


# License

MIT
