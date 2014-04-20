# Overview

This role installs and configures `sonar`, which is used for [metrics.typo3.org](metrics.typo3.org).

We use this tool to provide static code analytics of TYPO3 Core and Extensions for developers.


# Dependencies

- Java
- Maven
- MySQL


# Example Playbook

An example of how to use this role:

    - hosts: all
      roles:
         - { role: sonar }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
