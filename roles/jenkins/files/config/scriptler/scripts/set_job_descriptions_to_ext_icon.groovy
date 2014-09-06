hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items
jobs = allItems.findAll{job -> job.isBuildable() && job.name =~ "^extension-([a-z_A-Z0-9]*)\$"}
jobs.each{ job -> 
  rootDir = job.getRootDir().getAbsolutePath()
  file       = rootDir + "/workspace/ext_icon.gif"
  f = new File(file)
//   println file
  
  if (f.isFile()) {
    description = '<img alt="" src="ws/ext_icon.gif" style="margin: 1em; width: 5em;"/><h2>Dashboard for ' + job.name.replaceFirst("-", " ") + '</h2>'
//    description = ''
    job.setDescription(description)
    println file + " is present. Changing description to" + description + " for EXT:" + job.name
    println job.getDescription()
  }
}

x = ""