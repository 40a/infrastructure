# Overview

This role installs common packages and settings.

Normally these should already be present on the production servers, because they are managed via Chef by the TYPO3 Server Team.

However we need to ensure that we also install them on our local environments in order to run tests.

Besides that we can use this role for a 'contract' with the TYPO3 Server Team, which 'common' packages we expect.

In the best case, the packages are already present and Ansible does not change anything (indempotence that is).
In the worst case we just install them on our own.


# Example Playbook

Example of how to use this role:

    - hosts: all
      roles:
         - { role: common }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
