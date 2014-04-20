# Overview

This role installs and configures Jenkins, our CI server of choice.

Jenkins runs test and code metrics for extensions.
Jenkins sends the code metrics to Sonar (metrics.typo3.org).


## Dependencies

- Java
- Maven (for executing Sonar Runner)


# Example Playbook

An example of how to use this role:

    - hosts: all
      roles:
         - { role: jenkins }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
