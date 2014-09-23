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
