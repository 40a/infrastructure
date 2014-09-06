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


# Backup script

    #!/bin/bash
    today=`date +%Y-%m-%d`
    backup_to="~/job-archiv-${today}.tar.gz"
    find /var/lib/jenkins/ -name "config.xml" -print0 | xargs -0t tar -zcvf ${backup_to}
    echo "Backup created in ${backup_to}"


# Message levels

    // Warning
    <a href="http://typo3.org/" target="_blank"><img src="userContent/logo-typo3.gif" width="123" height="34" border="0" alt="TYPO3" title="TYPO3" alt="" title="" /></a>
    <p>This is a public build and test server for projects of <a href="http://forge.typo3.org/projects/" target="_blank">TYPO3</a>. All times on this server are UTC.</p>
    <p>See the <a href="http://forge.typo3.org/projects/9/wiki/Hudson_integration" target="_blank">TYPO3 wiki page</a> for more information about this service.</p>
    <div style="border: 2px solid red; padding:.5em; background: #fcc; -moz-border-radius: 5px; border-radius: 5px;"><p>This service is <i>very</i> young and in early alpha status.</p>
    <p>Help us collecting real world experience and tell us what would make this service better for <i>you</i>!</p><p>Report issues, ideas, feedback on <a href="http://forge.typo3.org/projects/team-forge/issues" target="_blank">http://forge.typo3.org/projects/team-forge/issues</a> or twitter to #t3con10-qatalk. Thanks!</p>
    <p>Even if there's a lot of <i>red</i> around here: Please stay calm and polite. red != bad ;) If you don't understand an error or warning, just <a href="http://forge.typo3.org/projects/team-forge/issues/new" target="_blank">drop us a line</a>.</p></div>
    <div style="border: 2px solid red; padding:.5em; background: #fcc; -moz-border-radius: 5px; border-radius: 5px;"><p>Hudson is working <i>very</i> hard. We are considering a shutdown at 14:00 UTC.</p></div>

    // Info
    <a href="http://typo3.org/" target="_blank"><img src="userContent/logo-typo3.gif" width="123" height="34" border="0" alt="TYPO3" title="TYPO3" alt="" title="" /></a>
    <p>This is a public build and test server for projects of <a href="http://forge.typo3.org/projects/" target="_blank">TYPO3</a>. All times on this server are UTC.</p>
    <p>See the <a href="http://forge.typo3.org/projects/9/wiki/Hudson_integration" target="_blank">TYPO3 wiki page</a> for more information about this service.</p>
    <div style="border: 2px solid green; padding:.5em; background: #fcf; -moz-border-radius: 5px; border-radius: 5px;"><p>Hudson is in good condition and running without problems.</p></div>

    // Notice
<a href="http://typo3.org/" target="_blank"><img src="userContent/logo-typo3.gif" width="123" height="34" border="0" alt="TYPO3" title="TYPO3" alt="" title="" /></a>
<p>This is a public build and test server for projects of <a href="http://forge.typo3.org/projects/" target="_blank">TYPO3</a>. All times on this server are UTC.</p>
<p>See the <a href="http://forge.typo3.org/projects/9/wiki/Hudson_integration" target="_blank">TYPO3 wiki page</a> for more information about this service.</p>
<div style="border: 2px solid yellow; padding:.5em; background: #ffc; -moz-border-radius: 5px; border-radius: 5px;"><p>Capacity is high. Thus Hudson is getting a massage and is currently not as responsive as usual. Patience please!</p></div>


# License

MIT


# Author Information

Christian Trabold <typo3@christian-trabold.de>
