# Overview

This role installs and configures `ssl` in order to encrypt web traffic.

We use this role in the `nginx` role.


# Example Playbook

An example of how to use this role:

    - hosts: all
      roles:
         - { role: ssl }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
