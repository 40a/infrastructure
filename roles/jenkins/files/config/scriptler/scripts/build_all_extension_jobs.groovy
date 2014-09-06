hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
jobs = allItems.findAll{job -> job.isBuildable() && job.name =~ "^extension-([a-z_A-Z0-9]*)\$"}
cause = new hudson.model.Cause.RemoteCause("localhost", "bulk build")
jobs.each{ run -> run.scheduleBuild(cause)}