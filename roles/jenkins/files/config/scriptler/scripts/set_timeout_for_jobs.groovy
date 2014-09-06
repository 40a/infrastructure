hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
activeJobs = allItems.findAll{job -> job.isBuildable()}
defaultFailBuild = true

println "Cur   |  Est  | Name"
activeJobs.each { job ->
 // Get the Timeout-PlugIn
 wrapper = job.getBuildWrappersList().findAll{it instanceof hudson.plugins.build_timeout.BuildTimeoutWrapper }[0]

 // Get the current Timeout, if any
 currentTimeout = (wrapper != null) ? wrapper.timeoutMinutes : ""

 // Calculate a new timeout with a min-value
 defaultTimeout = Math.round(job.estimatedDuration * 2 / 1000 / 60)
 if (defaultTimeout < 10) defaultTimeout = 10

 // Update the timeout, maybe requires instantiation
 action = (wrapper != null) ? "updated" : "established"
 if (wrapper == null) {
 plugin = new hudson.plugins.build_timeout.BuildTimeoutWrapper(defaultTimeout, defaultFailBuild)
 job.getBuildWrappersList().add(plugin)
 } else {
 wrapper.timeoutMinutes = defaultTimeout
 }

 // String preparation for table output
 String defaultTimeoutStr = defaultTimeout
 defaultTimeoutStr = defaultTimeoutStr.padLeft(5)
 String currentTimeoutStr = currentTimeout
 currentTimeoutStr = currentTimeoutStr.padLeft(5)
 String jobname = job.name.padRight(40)

 // Table output
 println "$currentTimeoutStr | $defaultTimeoutStr | $jobname | $action "
}

x = ""
