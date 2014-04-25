Feature: Create a test environment for each job that provides unit tests
  As the continuous integration server on ci.typo3.org
  I want to allow developers to test their code regularly
  In order to provide a realistic test scenario
  I want to create a clean test environment for each job
  
  As a test environment for a build job
  I want to provide a UNIX file system
  And I want to provide a clean mysql database with TYPO3 default tables
  And I want to provide these backend users
  | username     | password |
  | admin        | password |
  | _cli_phpunit |          |
  And I want to provide TYPO3 sources for these versions
    | HEAD |
    | 4.5  |
    | 4.4  |
    | 4.3  |
  And I want to provide a TYPO3 dummy package
  And I want to provide these default extensions
    | phpunit |
    | oelib   |

  As a build job
  I want to provide a test environment for unit tests on a build slave
  In order to test the PHP code with different TYPO3 versions
  I am a matrix job
  And I change the TYPO3 version dynamically on each build run
  The test environment should be rebuild from scratch on each build to provide a fresh state
  In order to reduce the amount of environments on the slave 
  I only process jobs that have a folder "Tests" including files named "*Test.php" in their file structure
  And I only process jobs that have the @test annotation in the test methods
  
  As a Tester
  Given there are fixtures in folder "Tests/Fixtures/"
  I want to setup the test environment based on these fixtures through the phpunit API
  I can create my fixtures in a mysql database through the TYPO3 DB-API
  And I can create artifacts for the tests within the build workspace
  
  As a Sysadmin
  I want to make sure that a build job does not harm my system
  In order to protect the system from unwanted changes in the system
  I provide an own UNIX user on the slave for each build job

  Scenario Outline: Setup and tear down a test environment
    Given there is a job for extension key <extkey>
    When I start the job
    Then I should have a job <job_name> running
    And I can access <database_name> over the TYPO3 DB-API
    And when I execute /typo3/cli_dispatch.phpsh phpunit /typo3conf/ext/<extkey>
    I should see a test result
    And I report the result back to the test job on ci.typo3.org
    When I stop the job
    Then I remove the database <database_name>
    And I should have not a job <job_name> running
    
    Examples:
      | extkey     | job_name             | database_name              |
      | tt_news    | extension-tt_news    | typo3-extension-tt_news    |
  
  Scenario: Create test environment
    Given I start a build that has unit tests
    And I provision the machine with "chef-solo"
    Then the packages <package> should be installed in version <version>
    | package     | version |
    | php         | 5.3.3   |
    | mysql       | 5.1     |
    | apache2     | 2.2     |
    | imagemagick | -       |
    | gdlib       | -       |
    | zlib        | -       |
    And the database "typo3-extension-extkey" should be accesible with user <extkey> and password <random>
    
  Scenario: Start a new build that has tests
    Given I start a build that has unit tests
    And the database credentials with user "ext_key" and a random password are stored in "typ3conf/localconf.php".
    And I can access the database through the TYPO3 DB-API
    And I can run "/typo3/cli_dispatch.phpsh phpunit /typo3conf/ext/<extkey>"
    Then I should see "by Sebastian Bergmann" in the output

  Scenario: Ensure TYPO3 environment
    Given I want to run tests for extension "tt_news"
    I can access the database table "typo3_extension_tt_news" on host "127.0.0.1" with user "tt_news" and password "<random>"
    And I can access TYPO3 sources
    And I can access file "typo3conf/localconf.php"
    And I can access extensions with tese extension keys
      | tt_news           |
      | phpunit           |
      | oelib             |
    And I can run "/typo3/cli_dispatch.phpsh phpunit /typo3conf/ext/<extkey>"
    And I should not get errors

  Scenario: Create fixtures in database
    Given I find fixtures in folder "Tests/Fixtures/"
    And I create fixtures in the database through TYPO3 DB-API
    I should not get errors
