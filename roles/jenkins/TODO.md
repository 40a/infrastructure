# Jenkins

Here is an example of how these variables might be configured on a Unix machine in the .profile file:

    export JAVA_OPTS=-Djava.awt.headless=true -Xmx512m -DJENKINS_HOME=/data/jenkins
    export MAVEN_OPTS="-Xmx512m -XX:MaxPermSize=256m"
    export ANT_OPTS="-Xmx512m -XX:MaxPermSize=256m"

Aus "Jenkins_The_Definitive_Guide.pdf" Seite 54; Chapter 3: Installing Jenkins


## Aktuelle Jobs für TYPO3 Extensions ermitteln

    hudsonInstance = hudson.model.Hudson.instance
    allItems = hudsonInstance.items
    jobs = allItems.findAll{job -> job.isBuildable() && job.name =~ "^extension-([a-z_A-Z0-9]*)-metrics\$"}
    cause = new hudson.model.Cause.RemoteCause("localhost", "bulk build")
    jobs.each{ run -> run.scheduleBuild(cause)}

---

    activeJobs = hudson.model.Hudson.instance.items.findAll{job -> job.getLastCompletedBuild() == null && job.name =~ "^extension-([a-z_A-Z0-9]*)-metrics\$"}
    activeJobs.each{run -> println(run.name)}

---

    activeJobs = hudson.model.Hudson.instance.items.findAll{job -> job.getLastCompletedBuild() == null && job.name =~ "^extension-([a-z_A-Z0-9]*)-metrics\$"}
    activeJobs.each{run -> println(run.name)}


## Verzeichnisstruktur inkl. XML-Dateien archivieren

    find /var/lib/jenkins/ -name "config.xml" -print0 | xargs -0t tar -zcvf ~/job-archiv.tar.gz
    find /var/opt/jenkins/ -name "config.xml" -print0 | xargs -0t tar -zcvf ~/job-archiv.tar.gz


# Find duplicated Jenkins Plugins
# Show the files that exist in asterisk-old/ but not in asterisk/

    comm -23 <( ls /var/opt/jenkins/plugins/*.hpi | sort ) <( ls /var/opt/jenkins/plugins/*.jpi | sort )

    comm -12 <( ls -1 /var/opt/jenkins/plugins/*.hpi | sed "s/.hpi//" | sort ) <( ls -1 /var/opt/jenkins/plugins/*.jpi | sed "s/.jpi//" | sort )


# Sonar Setup

<pre>
# Required metadata

sonar.projectKey=${JOB_NAME}
sonar.projectName=${JOB_NAME}
sonar.projectVersion=${BUILD_ID}
sonar.projectDescription=
sonar.sourceEncoding=UTF-8
sonar.sources=./src/main/java
sonar.tests=./src/test/java
sonar.scm.url=scm:git:git@git.example.com:${JOB_NAME}

# Metadata

sonar.links.homepage =
sonar.links.ci       =
sonar.links.issue    =
sonar.links.scm      =
sonar.links.scm_dev  =
</pre>


## SSL Keytool

    su jenkins -

    keytool -importcert -file /tmp/key.crt

creates `/home/jenkins/.keystore`.


## Display jobs group by the build steps they use

    import hudson.model.*
    import hudson.tasks.*

    //All the projects on which we can apply the getBuilders method
    def allProjects = Hudson.instance.items.findAll{ it instanceof Project }

    //All the registered build steps in the current Jenkins Instance
    def allBuilders = Builder.all()

    //Group the projects by the build steps used
    def projectsGroupByBuildSteps = allBuilders.inject([:]){
       map, builder ->
       map[builder.clazz.name] = allProjects.findAll{it.builders.any{ it.class.name.contains(builder.clazz.name)}}.collect{it.name}
       map
    }

    //presentation
    projectsGroupByBuildSteps.each{
       println """--- $it.key ---
       \t$it.value\n"""
    }

    println ""

via https://wiki.jenkins-ci.org/display/JENKINS/Display+jobs+group+by+the+build+steps+they+use



# Tracking configuration changes in Jenkins

From my experience you want to put mostly everything in software projects under versioning. Not only your production code, but also build and deployment scripts. This doesn't always prove to be straightforward. With the SCM Sync configuration plugin there is a beautiful plugin for versioning all Jenkins configuration under Git (or SVN). Here are my quick notes on how I got it running on my newest project.

    Install the plugin through the normal Jenkins process
    As described here create a SSH key for the Jenkins box and register it as a "deploy key" with Github:
    $ sudo -u jenkins bash
    $ ssh-keygen
    Follow the steps and create a key without a passphrase. Now copy the contents from ~/.ssh/id_rsa.pub to https://github.com/YOUR_USER/YOUR_REPO/settings/keys
    Set-up Git globally on the machine:
    $ sudo -u jenkins bash
    $ HOME=/Users/Shared/Jenkins/ git config --global user.email YOUR_EMAIL
    $ HOME=/Users/Shared/Jenkins/ git config --global user.name Jenkins
    Add git@github.com:YOUR_USER/YOUR_REPO.git as SCM root to "SCM Sync configuration" under http://YOUR_JENKINS/configure

The path under 3. should point to your Jenkins home folder. If your setup is similar to mine, you might not have a regular Jenkins user that you can log in with. Faking the HOME directory was the quickest way to get Git to accept the parameters.

This should be enough to have the given user now push to Github whenever somebody changes something in the configuration.

via http://cburgmer.posterous.com/



# Restart via Job

    import hudson.model.*;
    println "+++++ RESTARTING NOW +++++"
    Hudson.instance.doSafeRestart();


# Find text (e.g. Variables) in job configs

    find . -name config.xml -type f -print0 | xargs -0 grep "FOO"


# Check if certain plug-in is installed or not.

via http://scriptlerweb.appspot.com/script/show/56001

    pluginName = "testlink";

    found = false;
    for(plugin in hudson.model.Hudson.instance.pluginManager.plugins) {
        if(plugin.shortName.equals(pluginName)) {
            found = true;
            break;
        }
    }
    if(found) { println "1"; } else { println "0"; }


#  Display jobs group by the build steps they use

Bored to search randomly jobs configuration to found a particular build step example.
This script may help you.

    import hudson.model.*
    import hudson.tasks.*

    //All the projects on which we can apply the getBuilders method
    def allProjects = Hudson.instance.items.findAll{ it instanceof Project }

    //All the registered build steps in the current Jenkins Instance
    def allBuilders = Builder.all()

    //Group the projects by the build steps used
    def projectsGroupByBuildSteps = allBuilders.inject([:]){
       map, builder ->
       map[builder.clazz.name] = allProjects.findAll{it.builders.any{ it.class.name.contains(builder.clazz.name)}}.collect{it.name}
       map
    }

    //presentation
    projectsGroupByBuildSteps.each{
       println """--- $it.key ---
       \t$it.value\n"""
    }


# Rename jobs

via http://stackoverflow.com/questions/14603615/renaming-jenkins-hudson-job
via https://github.com/rhusar/jenkins-smartfrog-plugin/blob/master/etc/groovy-reconfigure/BatchUpdate-MatcherJobRename.groovy
<pre>
// IMPORTANT ct 2013-08-21 USE JENKINS API for renaming
// ** NEVER RENAME JOBS ON FILESYSTEM **
// https://wiki.jenkins-ci.org/display/JENKINS/Administering+Jenkins#AdministeringJenkins-Batchrenamingjobs

def JOB_PATTERN = ~/[. ]/; //find all jobs containing dots and spaces

(Hudson.instance.items.findAll { job -> job.name =~ JOB_PATTERN }).each { job_to_update ->
  println ("Updating job " + job_to_update.name);
  def new_job_name = job_to_update.name.replaceAll("2.0", "").replaceAll(~/[. ]/, "_").toLowerCase();
  println ("New name: " + new_job_name)
  try {
    //job_to_update.renameTo(new_job_name)
    //job_to_update.save()
  } catch (Exception ex){
    println ("Had issues with: " + new_job_name)
  }
  println("="*80);
}

println ""
</pre>

---

<pre>
// IMPORTANT ct 2013-08-21 USE JENKINS API for renaming
// ** NEVER RENAME JOBS ON FILESYSTEM **
// https://wiki.jenkins-ci.org/display/JENKINS/Administering+Jenkins#AdministeringJenkins-Batchrenamingjobs

Hudson.instance.items.each { job_to_update ->
    println ("Updating job " + job_to_update.name);
    def new_job_name = job_to_update.name.replaceAll(~/[. ]/, "_").toLowerCase();
    println ("New name: " + new_job_name)
    //job_to_update.renameTo(new_job_name)
    //job_to_update.save()
    println ("Updated name: " + job_to_update.name);
    println("="*80);
}
</pre>



# Displays the list of mail recipients used for notifications in all jobs

This is the extended Version of displayMailNotificationsRecipients (http://scriptlerweb.appspot.com/script/show/29001) with ExternalJobs (no Error for this jobs)

    import hudson.plugins.emailext.*
    import hudson.model.*
    import hudson.maven.*
    import hudson.maven.reporters.*
    import hudson.tasks.*

    // For each project
    for(item in Hudson.instance.items) {
      println("JOB : "+item.name);
      // Find current recipients defined in project
      if(!(item instanceof ExternalJob)) {
      if(item instanceof MavenModuleSet) {
        println(">MAVEN MODULE SET");
        // Search for Maven Mailer Reporter
        println(">>Reporters");
        for(reporter in item.reporters) {
          if(reporter instanceof MavenMailer) {
            println(">>> reporter : "+reporter+" : "+reporter.recipients);
          }
        }
      } else
      if(item instanceof FreeStyleProject) {
        println(">FREESTYLE PROJECT");
      }
      println(">>Publishers");
      for(publisher in item.publishersList) {
        // Search for default Mailer Publisher (doesn't exist for Maven projects)
        if(publisher instanceof Mailer) {
          println(">>> publisher : "+publisher+" : "+publisher.recipients);
        } else
        // Or for Extended Email Publisher
        if(publisher instanceof ExtendedEmailPublisher) {
          println(">>> publisher : "+publisher+" : "+publisher.recipientList);
        }
      }
      } else {
      println("External Jobs cannot have MailNotificationsRecipients")
      }
      println("\n=======\n");
    }



# Disable / enable jobs

via https://raw.github.com/jenkinsci/jenkins-scripts/master/scriptler/disableEnableJobsMatchingPattern.groovy

    // Pattern to search for. Regular expression.
    def jobPattern = "puppet_standalone_vmware_.*"

    // Should we be disabling or enabling jobs? "disable" or "enable", case-insensitive.
    def disableOrEnable = "disable"

    def lcFlag = disableOrEnable.toLowerCase()

    if (lcFlag.equals("disable") || lcFlag.equals("enable")) {
        def matchedJobs = Jenkins.instance.items.findAll { job ->
            job.name =~ /$jobPattern/ && !job.name.contains("_template")
        }

        matchedJobs.each { job ->
            if (lcFlag.equals("disable")) {
                println "Disabling matching job ${job.name}"
                job.disable()
            } else if (lcFlag.equals("enable")) {
                println "Enabling matching job ${job.name}"
                job.enable()
            }
        }
    } else {
        println "disableOrEnable parameter ${disableOrEnable} is not a valid option."
    }

    println ""



# List project based security

import hudson.security.*
import jenkins.security.*

def jobs = Jenkins.instance.items
jobs.each {
  def authorizationMatrixProperty = it.getProperty(AuthorizationMatrixProperty.class)
  def sids = authorizationMatrixProperty?.getAllSIDs().plus('anonymous')

  if (sids) {
    println it.name.center(80,'-')
    for (sid in sids){
      if (authorizationMatrixProperty?.hasPermission(sid,hudson.model.Item.BUILD)){
        println ''+sid+' has Build permission and Cancel permission will be add'
        //if (!dryrun) authorizationMatrixProperty?.add(hudson.model.Item.CANCEL,sid)
      }
    }
  }
  //if (!dryrun) it.save()
}

println ""



# TYPO3 Restart failed extension jobs

hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
jobs = allItems.findAll{
  job -> job.isBuildable() && job.name =~ "^extension-([a-z_A-Z0-9]*)\$" && job.lastBuild && job.lastBuild.result == hudson.model.Result.FAILURE
}

//jobs.each{
//  run -> println(run.name)
//}

cause = new hudson.model.Cause.RemoteCause("localhost", "bulk build to refresh metrics")
jobs.each{ run -> run.scheduleBuild(cause)}

println("")
