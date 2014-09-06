import hudson.plugins.emailext.*
import hudson.model.*
import hudson.maven.*
import hudson.maven.reporters.*
import hudson.tasks.*

for(item in Hudson.instance.queue.items) {
    println("Cancel build: $item");
    Hudson.instance.queue.cancel(item);
}
