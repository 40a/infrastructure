jobs = hudson.model.Hudson.instance.items.findAll{job -> job.getLastCompletedBuild() == null && job.name =~ "^extension-([a-z_A-Z0-9]*)\$"}
cause = new hudson.model.Cause.RemoteCause("localhost", "bulk build")
jobs.each{ run -> run.scheduleBuild(cause)}
