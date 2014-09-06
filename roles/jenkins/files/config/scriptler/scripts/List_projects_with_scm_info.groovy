import hudson.model.*
import hudson.tasks.*

hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items

jobs = allItems.findAll{job -> 
  job.isBuildable()
  job instanceof FreeStyleProject
  job.name =~ "extension-a.*"
}

jobs.each{ item ->
	println("JOB : "+item.name);
	println("SCM local: "+item.scm.getLocations()[0].local);
	println("\n=======\n");
}
// We don't want a vardump
jobs = null