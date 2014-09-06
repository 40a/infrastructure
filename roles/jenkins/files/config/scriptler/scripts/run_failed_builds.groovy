hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
activeJobs = allItems.findAll{
	job -> job.isBuildable()
}
failedRuns = activeJobs.findAll{
	job -> job.lastBuild && job.lastBuild.result == hudson.model.Result.FAILURE  && job.name =~ "^extension-([a-z_A-Z0-9]*)\$"
}

cause = new hudson.model.Cause.RemoteCause("localhost", "Rebuilding failed job.")

failedRuns.each{
// 	run -> println(run.name)
	run -> run.scheduleBuild(cause)
}

println ""