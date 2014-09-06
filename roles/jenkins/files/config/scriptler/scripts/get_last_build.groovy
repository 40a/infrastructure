hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
jobs = allItems.findAll{job -> job.isBuildable() && job.name =~ "^extension-dkd-([a-z_A-Z0-9]*)\$"}
jobs.each{ job -> 
    description = ''
    job.getLastBuild().inspect
    println job.name
}

x = ""