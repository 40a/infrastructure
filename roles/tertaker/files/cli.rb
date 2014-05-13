module Tertaker
  class CLI < Thor
    include Thor::Actions
    def initialize(*)
      super
      @shell = Thor::Shell::Basic.new

      raise 'Set username for jenkins user.' unless ENV['JENKINS_USERNAME']
      raise 'Set password for jenkins user.' unless ENV['JENKINS_PASSWORD']
      raise 'Set base_url for jenkins.'      unless ENV['JENKINS_BASE_URL']
      raise 'Set URL for T3X repository.'    unless ENV['TER_URL']

      @root_dir         = "#{ENV['HOME']}"
      @jenkins_username = "#{ENV['JENKINS_USERNAME']}"
      @jenkins_password = "#{ENV['JENKINS_PASSWORD']}"
      @jenkins_base_url = "#{ENV['JENKINS_BASE_URL']}"
      @ter_url          = "#{ENV['TER_URL']}"

      `mkdir -p "#{@root_dir}/.tertaker/logs/"` unless File.exists? "#{@root_dir}/.tertaker/logs/"
      @logger = Logger.new("#{@root_dir}/.tertaker/logs/tertaker.log", 'weekly')
# TODO ct 2011-08-31 Add namespace support?
#      redis = Redis.new
#      namespaced = Redis::Namespace.new(:ci_typo3, :redis => redis)
      @redis = Redis.new(:host => "127.0.0.1")
    end

    desc "help", "Describes available command line options"

    def help
      @shell.say "The following methods are available through typing `tertaker method_name`"
      @shell.say ""
      available_methods = [
        ["method_name", "description"],
        ["", ""],
        ["Batch actions", ""],
        ["perform", "Run tertaker actions in the correct order."],
        ["test_perform", "Test tertaker actions with a limited set of extensions. For debugging purposes."],
        ["", ""],
        ["Single actions", ""],
        ["setup", "Setup required folder structure."],
        ["repositories", "Download XML files that act as repositories for extension infos."],
        ["converter", "Write XML content to redis database. Use parameter '--fixtures' for writing test extensions only."],
        ["tertaker", "Download T3X and extract them to local repository."],
        ["extractor", "Extracts all T3X-files to human readable PHP projects"],
        ["jobber", "Create or update jenkins jobs for all extensions in database."],
        ["jenkins", "Create jobs in jenkins for each extension."],
        ["", ""],
        ["Tools", ""],
        ["reader KEY", "Fetch details for all or one given extension key. Default is ALL"],
        ["counter", "Count all extensions in database."],
        ["show", "Show tertaker repository"],
        ["help", "Prints this page and describes available methods"],
        ["version", "Prints the currently installed version"]
      ]
      @shell.print_table(available_methods)
    end

    desc "version", "Prints the currently installed version"

    def version
      @shell.say Tertaker::VERSION
    end
    map %w(--version -v) => :version

    desc "perform", "Run tertaker actions in the correct order."

    def perform
      `tertaker setup`
      `tertaker repositories`
      `tertaker converter`
      `tertaker tertaker`
      `tertaker extractor`
      `tertaker jobber`
      `tertaker jenkins`
    end

    desc "test_perform", "Test tertaker actions with a limited set of extensions. For debugging purposes."

    def test_perform
      `tertaker setup`
      @logger.debug "END setup"
      `tertaker repositories`
      @logger.debug "END repositories"
      `tertaker converter --fixtures`
      @logger.debug "END converter"
      `tertaker tertaker`
      @logger.debug "END tertaker"
      `tertaker extractor`
      @logger.debug "END extractor"
      `tertaker jobber`
      @logger.debug "END jobber"
      `tertaker jenkins`
      @logger.debug "END jenkins"
    end

    desc "setup", "Setup required folder structure."

    def setup
      %w{
        .tertaker/logs/
        .tertaker/tmp/
        .tertaker/repositories/
        .tertaker/sandbox/
        .tertaker/ter/
        .tertaker/jobs/
      }.each do |d|
        `mkdir -p #{@root_dir}/#{d}`
      end
    end

    desc "repositories", "Download XML files that act as repositories for extension infos."

    def repositories
      # TODO ct 2011-08-02 Before all tasks - create file structure
      storage_folder = File.join(@root_dir, '.tertaker', 'repositories')

      %w{
        http://forge.typo3.org/extensions.xml?authKey=xyz
        http://typo3.org/fileadmin/ter/extensions.xml.gz
      }.each do |uri|
        file_name  = URI.parse(uri).path[%r{[^/]+\z}]
        `cd #{storage_folder} && wget -N #{uri} && cd -`
        `cd #{storage_folder} && gunzip -cv #{file_name} > #{file_name.gsub('.gz','')}` if ['gz'].include? File.extname(file_name).sub('.', '')
      end
    end

    desc "converter", "Write XML content to redis database. Use parameter '--fixtures' for writing test extensions only."
    method_options :fixtures => :boolean
    def converter

      # Yes, we start from zero. Can be optimized
      # TODO ct 2011-08-09 Change to "flush" so that only selected DB gets reseted?
      # TODO ct 2011-08-17 Implement garbage control for extensions that were removed from TER
      # say "check if in redis and not in file anymore"
      @redis.flushall

      if options[:fixtures]
        files = Dir.glob("#{File.expand_path("../../../spec/fixtures", __FILE__)}/extensions-small.xml")
      else
        files = Dir.glob("#{@root_dir}/.tertaker/repositories/*.xml")
      end

      files.each do |file|
        doc = Nokogiri::XML(open(file))

        # Collect download statistics / highscore
        # @see http://blog.agoragames.com/blog/2011/01/01/creating-high-score-tables-leaderboards-using-redis/
        doc.xpath('//extension/downloadcounter').each do |node|
          ext_key  = node.parent.attributes['extensionkey'].value.downcase
          @redis.zadd "downloads", node.inner_text(), ext_key
        end

        # Collect extension details
        # TODO ct 2011-08-25 Use hset?
        # TODO ct 2011-08-24 Read every version into Redis BUT only process / download the latest version for Jenkins
        doc.xpath('//extension/version').each do |node|
          details = Hash.new
          ext_key  = node.parent.attributes['extensionkey'].value.downcase
          details['version'] = node.attributes['version'].value

          node.element_children.each do |detail|
            details[detail.name] = detail.inner_text() if detail.element?
          end

          details['dependencies'] = PHP.unserialize(details['dependencies'])
          details['dependencies'].each do |d|
            next if d['kind'].nil? || d['extensionKey'] == ""

            kind = d['kind']
            versions = d['versionRange'].split('-').to_json

            if kind['conflicts']
              # TODO ct 2011-08-31 Reverse versioning is not possible due to version range
              # How to split version range in precice version numbers?
              @redis.sadd "#{ext_key}:conflicts", "#{d['extensionKey']}:#{versions}"
              @redis.sadd "#{ext_key}:#{details['version']}:conflicts", "#{d['extensionKey']}:#{versions}"
              @redis.sadd "#{d['extensionKey']}:conflicts", "#{ext_key}:#{details['version']}"
              @logger.debug "ADD conflict #{ext_key}:conflicts to #{d['extensionKey']}"

              # Dealing with version numbers
              @redis.set "#{ext_key}:conflicts:#{d['extensionKey']}", versions
              @redis.set "#{d['extensionKey']}:conflicts:#{ext_key}", versions

              @redis.sadd "conflicts", ext_key
              @redis.sadd "conflicts", d['extensionKey']
            end

            if kind['depends']
              @redis.sadd "#{ext_key}:depends", "#{d['extensionKey']}:#{versions}"
              @redis.sadd "#{ext_key}:#{details['version']}:depends", "#{d['extensionKey']}:#{versions}"
              @redis.sadd "#{d['extensionKey']}:supports", "#{ext_key}:#{details['version']}"
              @logger.debug "ADD dependency #{ext_key}:depends to #{d['extensionKey']}"

              # Dealing with version numbers
              @redis.set "#{ext_key}:depends:#{d['extensionKey']}", versions
              @redis.set "#{d['extensionKey']}:supports:#{ext_key}", versions

              @redis.sadd "dependencies", ext_key
              @redis.sadd "supports", d['extensionKey']
            end

            if kind['suggests']
              @redis.sadd "#{ext_key}:suggests", "#{d['extensionKey']}:#{versions}"
              @redis.sadd "#{ext_key}:#{details['version']}:suggests", "#{d['extensionKey']}:#{versions}"
              @redis.sadd "#{d['extensionKey']}:suggested", "#{ext_key}:#{details['version']}"
              @logger.debug "ADD suggestion #{ext_key}:suggest to #{d['extensionKey']}"

              # Dealing with version numbers
              @redis.set "#{ext_key}:suggests:#{d['extensionKey']}", versions
              @redis.set "#{d['extensionKey']}:suggested:#{ext_key}", versions

              @redis.sadd "suggestions", ext_key
              @redis.sadd "suggested", d['extensionKey']
            end
          end
          # TODO ct 2011-08-31 Support "latest" key for extension? How to deal with multiple versions + jenkins?
          # Q: How do I get jenkins to only build the latest extension version?
          # A: We store the latest (last) element in the extension key, which is used by all other tasks to reveal the details
          # So this should be downwards compatible unless we change it here

          # explode categories
          details['category'].split(',').each do |category|
            @redis.sadd "category:#{category}", ext_key
            @redis.sadd "categories", category
          end

          @redis.set ext_key, details.to_json
          @redis.set "#{ext_key}:#{details['version']}", details.to_json
          @redis.sadd "#{ext_key}:uploadcomments", "#{details['version']}:#{details['uploadcomment']}"
          @redis.sadd "#{ext_key}:versions", details['version']
          @redis.hset "#{ext_key}:downloads", details['version'], details['downloadcounter']
          @logger.debug "ADD new version #{details['version']} for #{ext_key}"
          @redis.sadd "extensions", ext_key
        end
      end
      total = @redis.scard "extensions"
      @shell.say "Total extensions indexed: #{total}"
    end

    desc "tertaker", "Download T3X and extract them to local repository."

    def tertaker
      extensions = @redis.smembers "extensions"
      extensions.each do |ext_key|
        puts "Get details for extension #{ext_key}"
        extension = JSON.parse(@redis.get(ext_key))
        #Resque.enqueue(Tertaker::DownloadWorker, ext_key)
        if extension['repositoryurl']
          @logger.info "We're only dealing with TER extensions here. Moving on..."
          next
        end

        version  = extension['version']

        @logger.debug "+++ Starting new tertaker job +++"

        t3x_name = "#{ext_key}_#{version}.t3x"
        path_seq = "#{ext_key[0,1]}/#{ext_key[1,1]}"
        path     = "#{path_seq}/#{t3x_name}"

        if (File.exists? "#{@root_dir}/.tertaker/tmp/#{path_seq}/#{t3x_name}") && (File.exists? "#{@root_dir}/.tertaker/ter/#{ext_key}")
          @logger.info "T3X for #{ext_key} not changed. Moving on."
          puts "T3X for #{ext_key} not changed. Moving on."
          next
        end

        `mkdir -p #{@root_dir}/.tertaker/tmp/#{path_seq}/`
        `cd #{@root_dir}/.tertaker/tmp/#{path_seq}/ && wget -N #{@ter_url}#{path} && cd -`
        @logger.info "Download for #{t3x_name} finished."
        @logger.info "Commiting job for EXT:#{ext_key} to extract_queue"
#        Resque.enqueue(Tertaker::DownloadWorker, ext_key)
        @redis.sadd "extract_queue", ext_key
        @logger.info "Tertaker task's finished - now it's extract time!"
      end
    end

    desc "extractor", "Extracts all T3X-files to human readable PHP projects"

    def extractor
      extensions = @redis.smembers "extract_queue"

      # TODO ct 2011-08-24 Implement listener on job queue to separate responsibility
      return unless extensions.lenght > 0

      extensions.each do |ext_key|
        puts "Get details for extension #{ext_key}"
        extension = JSON.parse(@redis.get(ext_key))

        if extension['repositoryurl']
          @logger.info "We're only dealing with TER extensions here. Moving on..."
          next
        end

        version  = extension['version']

        @logger.debug "+++ Starting new extractor job +++"

        t3x_name = "#{ext_key}_#{version}.t3x"
        path_seq = "#{ext_key[0,1]}/#{ext_key[1,1]}"
        path     = "#{path_seq}/#{t3x_name}"

        unless File.exists? "#{@root_dir}/.tertaker/tmp/#{path_seq}/#{t3x_name}"
          @logger.fatal "T3X for #{ext_key} not found. Please download it first! Moving on."
          puts "T3X for #{ext_key} not yet downloaded. Moving on."
          next
        end

        if File.directory? "#{@root_dir}/.tertaker/ter/#{ext_key}"
          puts "removing old extension repository"
          `rm -rf #{@root_dir}/.tertaker/ter/#{ext_key}/*`
          @logger.info "Performing rm #{@root_dir}/.tertaker/ter/#{ext_key}/*"
        end

        unless File.directory? "#{@root_dir}/.tertaker/ter/#{ext_key}/.git"
          `git init #{@root_dir}/.tertaker/ter/#{ext_key}/`
          @logger.info "Creating git repository '#{@root_dir}/.tertaker/ter/#{ext_key}/.git'"
        else
          @logger.info "Git repository '#{@root_dir}/.tertaker/ter/#{ext_key}/.git' exists. Doing nothing."
        end

        if File.exists? "#{@root_dir}/.tertaker/tmp/#{path_seq}/#{t3x_name}"
          `mkdir -p #{@root_dir}/.tertaker/sandbox/#{ext_key}`
          @logger.info "Extracting #{@root_dir}/.tertaker/tmp/#{path_seq}/#{t3x_name} to #{@root_dir}/.tertaker/sandbox/#{ext_key}"
          `php #{File.expand_path("../../../vendor", __FILE__)}/t3x-converter.php #{@root_dir}/.tertaker/tmp/#{path_seq}/#{t3x_name} #{@root_dir}/.tertaker/sandbox/`
          puts "SUCCESS: #{t3x_name} extracted!"
          @logger.info "Cleaning #{@root_dir}/.tertaker/sandbox/#{ext_key}"
          Dir.glob("#{@root_dir}/.tertaker/sandbox/#{ext_key}/**/.svn").each {|dir| FileUtils.rm_rf(dir) }
          Dir.glob("#{@root_dir}/.tertaker/sandbox/#{ext_key}/**/.git").each {|dir| FileUtils.rm_rf(dir) }
          Dir.glob("#{@root_dir}/.tertaker/sandbox/#{ext_key}/**/.cvs").each {|dir| FileUtils.rm_rf(dir) }

          puts "SUCCESS: #{t3x_name} cleaned!"
          @logger.info "Moving #{@root_dir}/.tertaker/sandbox/#{ext_key}/* -> #{@root_dir}/.tertaker/ter/#{ext_key}/"
          `mv #{@root_dir}/.tertaker/sandbox/#{ext_key}/* #{@root_dir}/.tertaker/ter/#{ext_key}/`
          @logger.info "Removing #{@root_dir}/.tertaker/sandbox/#{ext_key}"
          `rm -rf #{@root_dir}/.tertaker/sandbox/#{ext_key}`
        end

        if File.directory? "#{@root_dir}/.tertaker/ter/#{ext_key}"
          puts "Commiting new changes for EXT:#{ext_key}"
          `cd #{@root_dir}/.tertaker/ter/#{ext_key} && git add . && git commit -m"Updated extension #{ext_key} to #{version}" . && git tag --force -m"Tagging extension #{ext_key} version #{version}"`
          @logger.info "Commiting new changes for EXT:#{ext_key}"
          @logger.info "Commiting job for EXT:#{ext_key} to build_queue"
          @redis.sadd "build_queue", ext_key
          @logger.info "Removing job for EXT:#{ext_key} to extract_queue"
          @redis.srem "extract_queue", ext_key
        end
      end
    end

    desc "jobber", "Create or update jenkins jobs for all extensions in database."

    def jobber
      extensions = @redis.smembers "build_queue"
      extensions.each do |ext_key|
        em_conf = JSON.parse(@redis.get(ext_key))
        # Create template based on SCM
        # Choose 'forge' if a SCM location is present
        # Choose 'ter' if no SCM location is present
        if em_conf['repositoryurl']
          @logger.debug "Choosing forge template for #{ext_key}"
          filename = File.dirname(__FILE__) + '/../../templates/extension.forge.erb'
        else
          @logger.debug "Choosing ter template for #{ext_key}"
          filename = File.dirname(__FILE__) + '/../../templates/extension.ter.erb'
          em_conf['repositoryurl'] = "#{@root_dir}/.tertaker/ter/#{ext_key}/"
        end
        begin
          template = ERB.new(File.read(filename))
          job = Typo3::Ci::Job.new(ext_key, em_conf)

          Dir::mkdir("#{@root_dir}/.tertaker/jobs/extension-#{ext_key}/") unless File.exists? "#{@root_dir}/.tertaker/jobs/extension-#{ext_key}/"
          file = File.new("#{@root_dir}/.tertaker/jobs/extension-#{ext_key}/config.xml",'w')
          content = template.result(job.get_binding)
          file.puts(content)
          file.close
          @logger.info "SUCCESS: Created job for #{ext_key} in #{@root_dir}/.tertaker/jobs/extension-#{ext_key}/"
        rescue Exception => e
          @logger.fatal "Could NOT create job for #{ext_key} in #{@root_dir}/.tertaker/jobs/extension-#{ext_key}/:\n#{e.message}"
          exit 1
        end
      end
    end

    desc "jenkins", "Create jobs in jenkins for each extension."

    # TODO ct 2011-08-17 Always call jobber beforehand so config.xml is present.
    # Both build based on the build_queue, but only jenkins removes from queue so no data is lost
    def jenkins(reset=nil)
      sess = Patron::Session.new
      sess.timeout = 20
      sess.base_url = "#{@jenkins_base_url}"
      sess.headers['User-Agent'] = 'Mozilla/4.0'
      sess.headers['Content-Type'] = 'text/xml'
      sess.enable_debug "#{@root_dir}/.tertaker/logs/patron.log"

      # Authenticate
      sess.username = "#{@jenkins_username}"
      sess.password = "#{@jenkins_password}"
      sess.auth_type = :basic

      extensions = @redis.smembers "build_queue"
      extensions.each do |ext_key|
        # Delete all jobs
        if (reset)
          resp = sess.post("job/extension-#{ext_key}/doDelete", "DELETE extension-#{ext_key}")
          if resp.status == 200
            @logger.debug "DELETED extension-#{ext_key} from job list. (Status #{resp.status})"
          else
            @logger.debug "FAIL Could NOT delete extension-#{ext_key} from job list. (Status #{resp.status})"
          end
          sleep(0.01)
          next
        end

        # Create or update jobs
        @logger.debug "POST #{@root_dir}/.tertaker/jobs/extension-#{ext_key}/config.xml"
        config = IO.read("#{@root_dir}/.tertaker/jobs/extension-#{ext_key}/config.xml")

        resp = sess.get("job/extension-#{ext_key}")
        if resp.status == 200
          @logger.debug "GET returned #{resp.status} == Job exists. Updating."
          sess.post("job/extension-#{ext_key}/config.xml", config)
        else
          @logger.debug "GET returned #{resp.status} == Job does not exists. Creating."
          sess.post("createItem?name=extension-#{ext_key}", config)
        end
        # Add job to job-queue & create metrics for this extension
        @redis.sadd "jobs", ext_key
        @logger.info "Removing build for EXT:#{ext_key} in build_queue"
        @redis.srem "build_queue", ext_key
        sleep(0.01)
      end

      @logger.info "+++ Finnished job creation. Moving on to building these jobs +++"

      # TODO ct 2011-08-09 invoke runner imediatly after creating jobs?
      #invoke :runner
      extensions = @redis.smembers "jobs"
      extensions.each do |ext_key|
        # POST to job/extension-#{ext_key}/buildWithParameters + EXTENSION_VERSION as post data
        resp = sess.post("job/extension-#{ext_key}/build", "BUILD extension-#{ext_key}")
        # INFO ct 2011-08-09 Jenkins throws 40x errors but still builds the jobs (on my Mac).
        if resp.status == 200
          @logger.info "BUILDING extension-#{ext_key}. (Status #{resp.status})"
        else
          @logger.fatal "ERROR: not sure if building extension-#{ext_key} succeed. (Status #{resp.status})"
        end
        sleep(0.01)
      end
    end

    desc "reader", "Display all extension keys or display details for given extension keys (ext_key1,ext_key2). Default: Display all extension keys."

    def reader(ext_keys=nil)
      if ext_keys
        ext_keys = ext_keys.split(',').each do |key| key.strip! end
        ext_keys.each do |ext_key|
          puts "Get details for extension #{ext_key}"
          extension = JSON.parse(@redis.get(ext_key))
          puts ""
          puts "+++ #{extension['title']} (#{extension['version']}) +++"
          puts extension['description']
        end
      else
        extensions = @redis.smembers "extensions"
        puts extensions
      end
    end

    desc "counter", "Count all extensions in database."

    def counter
      total = @redis.scard "extensions"
      puts "Total extensions indexed: #{total}"
    end

    desc "show", "Show tertaker repository"

    def show
      puts `tree -L 2 #{@root_dir}/.tertaker/`
    end
  end
end
