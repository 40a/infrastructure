hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
jobs = allItems.findAll{job -> job.isBuildable() && job.name =~ "^extension-([a-z_A-Z0-9]*)-metrics\$"}
// jobs = allItems.findAll{job -> job.isBuildable() && job.name =~ "^extension-ab_booking-metrics\$"}
cause = new hudson.model.Cause.RemoteCause("localhost", "bulk build")
jobs.each{
 	run -> println(run.name)
//        run -> run.delete()
//	run -> run.scheduleBuild(cause)
}

println ""