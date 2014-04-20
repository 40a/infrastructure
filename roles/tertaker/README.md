# Overview

This OBSOLETE role installs and configures `tertaker`, which is used to fetch and process TYPO3 extensions from [ter.typo3.org](ter.typo3.org).

It uses Redis as persistent storage and creates Jenkins Jobs via HTTP API.

It is the 'clue' between TER and ci.typo3.org (which then sends code metrics to Sonar).

It runs every 6 hours in order to sync TER with ci.typo3.org.


Please note that this tool is marked OBSOLETE because we should replace it with a leaner approach based on Job DSL and Groovy scripts WITHIN Jenkins.

We do not need the dependency on Redis.
We would need to execute the T3X extractor tool within Jenkins.


# Dependencies

- Ruby
- Redis
- Cron


# Example Playbook

An example of how to use this role:

    - hosts: all
      roles:
         - { role: tertaker }


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
