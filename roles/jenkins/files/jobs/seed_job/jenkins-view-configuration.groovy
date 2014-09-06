/**
 * VIEWS
 */
view(type: NestedView) {
  name('TYPO3')
  views {
    view(type: NestedView) {
      name('Extensions')
      views {
        (a..z).each { i ->
          view {
            name(i)
            jobs {
              regex('^extension-' + i + '.*$')
            }
            columns {
              status()
              weather()
              name()
              lastSuccess()
              lastFailure()
            }
          }
        }
      }
    }
  }
}



labels = ('A'..'Z')

view(type: NestedView) {
    name('TYPO3')
    views {
        view(type: NestedView) {
            name('Extensions')
            views {
                view {
                    name("All")
                    jobs {
                        regex('^extension-.*')
                    }
                    columns {
                        status()
                        weather()
                        name()
                        lastSuccess()
                        lastFailure()
                    }
                }

                labels.each { j ->
                    view {
                        name(j)
                        jobs {
                            regex('^extension-'+ j +'.*')
                        }
                        columns {
                            status()
                            weather()
                            name()
                            lastSuccess()
                            lastFailure()
                        }
                    }
                }
            }
        }
    }
}



def views = [
  [
    name: "All",
    description: "",
    regex: ".*",
    recurse: true,
  ],
  [
    name: "TYPO3",
    description: "",
    regex: ".*application-.*\$",
    recurse: true,
  ],
  [
    name: "Deployments",
    description: "",
    regex: ".*deploy-.+",
    recurse: true,
  ]
].each { i ->
  view {
    name i['name']
    description i['description']

    filterBuildQueue()
    filterExecutors()

    jobs {
      regex(i['regex'])
    }

    // We can not build project 'folders'
    // and disable the unneeded columns to cleanup the view
    if (i['name'] == 'Projects') {
      columns {
        status()
        weather()
        name()
      }
    } else {
      columns {
        status()
        weather()
        name()
        lastSuccess()
        lastFailure()
        lastDuration()
        buildButton()
      }
    }

    configure {
      (it / 'recurse').setValue(i["recurse"])
    }
  }
}
