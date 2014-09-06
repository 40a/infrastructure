import jenkins.model.*;

// start in the state that doesn't do any build.
// @see https://wiki.jenkins-ci.org/display/JENKINS/Post-initialization+script
Jenkins.instance.doQuietDown();
