hudsonInstance = hudson.model.Hudson.instance
allItems = hudsonInstance.items

logRotator = new hudson.tasks.LogRotator(14, -1)

jobs = allItems.findAll{job -> job.name =~ "^extension-([a-z_A-Z0-9]*)\$"}
jobs.each{ run ->
  run.setLogRotator(logRotator)
  run.logRotate()

  println run.name
  println run.getLogRotator()
  println run.supportsLogRotator()
  println "===="
}
x = ""

mfg
