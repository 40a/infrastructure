# Overview

This role installs and configures `mysql`, which is used by `sonar` to store code metrics.

# Backups

    mysqldump --opt --single-transaction -u sonar -p sonar > $HOME/`date --iso-8601`-sonar.backup.sql


# Example Playbook

An example of how to use this role:

    - hosts: all
      roles:
         - { role: mysql }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
