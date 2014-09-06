# Overview

This role installs and configures `php` (e.g. memory settings) and installs PEAR / PHAR packages.

These tools are used by Jenkins to run code metrics and unit tests.


# Example Playbook

An example of how to use this role:

    - hosts: all
      roles:
         - { role: php }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
