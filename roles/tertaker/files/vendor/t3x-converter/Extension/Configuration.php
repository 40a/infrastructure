<?php
/**
 * Created by JetBrains PhpStorm.
 * User: olly
 * Date: 01.05.11
 * Time: 23:44
 * To change this template use File | Settings | File Templates.
 */
 
class TYPO3_Extension_Configuration {
	const KEY_Title = 'title';
	const KEY_Description = 'description';
	const KEY_Category = 'category';
	const KEY_Shy = 'shy';
	const KEY_Version = 'version';
	const KEY_Dependencies = 'dependencies';
	const KEY_Conflicts = 'conflicts';
	const KEY_Priority = 'priority';
	const KEY_LoadOrder = 'loadOrder';
	const KEY_Module = 'module';
	const KEY_State = 'state';
	const KEY_UploadFolder = 'uploadfolder';
	const KEY_CreateDirectories = 'createDirs';
	const KEY_ModifyTables = 'modify_tables';
	const KEY_ClearCacheOnLoad = 'clearcacheonload';
	const KEY_LockType = 'lockType';
	const KEY_AuthorName = 'author';
	const KEY_AuthorMail = 'author_email';
	const KEY_AuthorCompany = 'author_company';
	const KEY_IgnoreInFrontend = 'doNotLoadInFE';
	const KEY_CglComplianceGeneral = 'CGLcompliance';
	const KEY_CglComplianceNote = 'CGLcompliance_note';
	const KEY_Constraints = 'constraints';
	const KEY_MD5Values = '_md5_values_when_last_written';

	const Category_Backend = 'be';
	const Category_BackendModule = 'module';
	const Category_Frontend = 'fe';
	const Category_FrontendPlugin = 'plugin';
	const Category_Miscellaneous = 'misc';
	const Category_Services = 'services';
	const Category_Templates = 'templates';
	const Category_Example = 'example';
	const Category_Documentation = 'doc';

	const LockType_Local = 'L';
	const LockType_Global = 'G';
	const LockType_System = 'S';

	const State_Alpha = 'alpha';
	const State_Beta = 'beta';
	const State_Stable = 'stable';
	const State_Experimental = 'experimental';
	const State_Test = 'test';
	const State_Obsolete = 'obsolete';
	const State_ExcludeFromUpdates = 'excludeFromUpdates';

	const Priority_Top = 'top';
	const Priority_Bottom = 'bottom';

	/**
	 * @var array
	 */
	protected $allowedCategories = array();

	/**
	 * @var array
	 */
	protected $allowedLockTypes = array();

	/**
	 * @var array
	 */
	protected $allowedStates = array();

	/**
	 * @var array
	 */
	protected $allowedPriorities = array();

	/**
	 * @var string
	 */
	protected $key;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var string
	 */
	protected $category;

	/**
	 * @var boolean
	 */
	protected $shy = FALSE;

	/**
	 * @var boolean
	 */
	protected $internal = FALSE;

	/**
	 * @var string
	 */
	protected $lockType;

	/**
	 * @var string
	 */
	protected $module;

	/**
	 * @var TYPO3_Version
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $state;

	/**
	 * @var array
	 * @deprecated
	 */
	protected $dependencies;

	/**
	 * @var array
	 * @deprecated
	 */
	protected $conflicts;

	/**
	 * @var string
	 */
	protected $priority;

	/**
	 * @var mixed
	 * @deprecated
	 */
	protected $loadOrder;

	/**
	 * @var boolean
	 */
	protected $clearCacheOnLoad;

	/**
	 * @var string
	 */
	protected $createDirectories;

	/**
	 * @var boolean
	 */
	protected $uploadFolder;

	/**
	 * @var string
	 */
	protected $modifyTables;

	/**
	 * @var string
	 */
	protected $authorName;

	/**
	 * @var string
	 */
	protected $authorMail;

	/**
	 * @var string
	 */
	protected $authorCompany;

	/**
	 * @var boolean
	 */
	protected $ignoreInFrontend;

	/**
	 * @var string
	 * @deprecated
	 */
	protected $cglComplianceGeneral;

	/**
	 * @var string
	 * @deprecated
	 */
	protected $cglComplianceNote;

	/**
	 * @var TYPO3_Extension_Configuration_ConstraintCollection
	 */
	protected $constraintCollection;

	/**
	 * @var string
	 */
	protected $MD5Values;

	/**
	 * @var TYPO3_Extension_IO_File
	 */
	protected $file;

	protected function initialize() {
		$this->allowedCategories = array(
			self::Category_Backend, self::Category_BackendModule, self::Category_Documentation,
			self::Category_Example, self::Category_Frontend, self::Category_FrontendPlugin,
			self::Category_Miscellaneous, self::Category_Services, self::Category_Templates
		);
		$this->allowedLockTypes= array(
			self::LockType_Global, self::LockType_Local, self::LockType_System
		);
		$this->allowedStates = array(
			self::State_Alpha, self::State_Beta, self::State_Stable, self::State_Obsolete,
			self::State_Test, self::State_Experimental, self::State_ExcludeFromUpdates
		);
		$this->allowedPriorities = array(
			self::Priority_Bottom, self::Priority_Top
		);
	}

	public static function read(TYPO3_Extension_IO_File $file, $key) {
		$_EXTKEY = uniqid('extension');
		$EM_CONF[$_EXTKEY] = NULL;

		eval(preg_replace('#<\?(php)?|\?>#', '', $file->getContent()));

		$configuration = self::create($EM_CONF[$_EXTKEY], $key);
		$configuration->setFile($file);

		return $configuration;
	}

	public static function create(array $array, $key) {
		$configuration = new TYPO3_Extension_Configuration(
			$key,
			self::getValue($array, self::KEY_Title),
			self::getValue($array, self::KEY_Version),
			self::getValue($array, self::KEY_State),
			self::getValue($array, self::KEY_Category)
		);

		$configuration->setDescription(self::getValue($array, self::KEY_Description));
		$configuration->setShy(self::getValue($array, self::KEY_Shy));
		$configuration->setDependencies(self::getValue($array, self::KEY_Dependencies));
		$configuration->setConflicts(self::getValue($array, self::KEY_Conflicts));
		$configuration->setPriority(self::getValue($array, self::KEY_Priority));
		$configuration->setLoadOrder(self::getValue($array, self::KEY_LoadOrder));
		$configuration->setModule(self::getValue($array, self::KEY_Module));
		$configuration->setUploadFolder(self::getValue($array, self::KEY_UploadFolder));
		$configuration->setCreateDirectories(self::getValue($array, self::KEY_CreateDirectories));
		$configuration->setModifyTables(self::getValue($array, self::KEY_ModifyTables));
		$configuration->setClearCacheOnLoad(self::getValue($array, self::KEY_ClearCacheOnLoad));
		$configuration->setLockType(self::getValue($array, self::KEY_LockType));
		$configuration->setAuthorName(self::getValue($array, self::KEY_AuthorName));
		$configuration->setAuthorMail(self::getValue($array, self::KEY_AuthorMail));
		$configuration->setAuthorCompany(self::getValue($array, self::KEY_AuthorCompany));
		$configuration->setIgnoreInFrontend(self::getValue($array, self::KEY_IgnoreInFrontend));
		$configuration->setCglComplianceGeneral(self::getValue($array, self::KEY_CglComplianceGeneral));
		$configuration->setCglComplianceNote(self::getValue($array, self::KEY_CglComplianceNote));
		$configuration->setContraintCollection(
			TYPO3_Extension_Configuration_ConstraintCollection::create(
				self::getValue($array, self::KEY_Constraints)
			)
		);
		$configuration->setMD5Values(self::getValue($array, self::KEY_MD5Values));

		return $configuration;
	}

	public static function getValue(array $array, $key) {
		$value = NULL;
		if (isset($array[$key])) {
			$value = $array[$key];
		}
		return $value;
	}

	public function __construct($key, $title, $version, $state, $category) {
		$this->initialize();
		$this->setKey($key);
		$this->setTitle($title);
		$this->setVersionNumber($version);
		$this->setState($state);
		$this->setCategory($category);
	}

	public function __toArray($includeDeprecated = FALSE) {
		$array = array(
			self::KEY_Title => $this->getTitle(),
			self::KEY_Description => $this->getDescription(),
			self::KEY_Category => $this->getCategory(),
			self::KEY_Shy => $this->getShy(),
			self::KEY_Version => $this->getVersion()->__toString(),
			self::KEY_Priority => $this->getPriority(),
			self::KEY_Module => $this->getModule(),
			self::KEY_State => $this->getState(),
			self::KEY_UploadFolder => $this->getUploadFolder(),
			self::KEY_CreateDirectories => $this->getCreateDirectories(),
			self::KEY_ModifyTables => $this->getModifyTables(),
			self::KEY_ClearCacheOnLoad => $this->getClearCacheOnLoad(),
			self::KEY_LockType => $this->getLockType(),
			self::KEY_AuthorName => $this->getAuthorName(),
			self::KEY_AuthorMail => $this->getAuthorMail(),
			self::KEY_AuthorCompany => $this->getAuthorCompany(),
			self::KEY_IgnoreInFrontend => $this->getIgnoreInFrontend(),
			self::KEY_Constraints => $this->getContraintCollection()->__toArray(),
			self::KEY_MD5Values => $this->getMD5Values(),
		);

		if ($includeDeprecated) {
			$array = array(
				self::KEY_Dependencies => $this->getDependencies(),
				self::KEY_Conflicts => $this->getConflicts(),
				self::KEY_LoadOrder => $this->getLoadOrder(),
				self::KEY_CglComplianceGeneral => $this->getCglComplianceGeneral(),
				self::KEY_CglComplianceNote => $this->getCglComplianceNote(),
			);
		}

		return $array;
	}

	public function __toString() {
		$header = <<<EOD
########################################################################
# Extension Manager/Repository config file for ext "{key}".
#
# Auto generated {datetime}
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "constraints" must not be touched!
########################################################################
EOD;

		$header = str_replace(
			array('{key}', '{datetime}'),
			array($this->getKey(), date('Y-m-d H:i')),
			$header
		);

		$emConfiguration = preg_replace(
			array('/  /', '/\s+array/m', '/(\w+)\s+\(/', '/=>\s+false,/',  '/=>\s+true,/'),
			array("\t", ' array', '${1}(', '=> FALSE,', '=> TRUE,'),
			var_export($this->__toArray(), TRUE)
		);

		$content = '<?php' . PHP_EOL .
			$header . PHP_EOL . PHP_EOL .
			'$EM_CONF[$_EXTKEY] = ' . $emConfiguration . ';' . PHP_EOL .
			'?>';

		return $content;
	}

	public function setKey($key) {
		$this->key = (string) $key;
	}

	public function getKey() {
		return $this->key;
	}

	public function setTitle($title) {
		$this->title = (string) $title;
	}

	public function getTitle() {
		return $this->title;
	}

	public function setDescription($description) {
		$this->description = (string) $description;
	}

	public function getDescription() {
		return $this->description;
	}

	public function setCategory($category) {
		$this->category = (string) $category;
	}

	public function getCategory() {
		return $this->category;
	}

	public function setShy($shy) {
		$this->shy = (bool) $shy;
	}

	public function getShy() {
		return $this->shy;
	}

	public function setInternal($internal) {
		$this->internal = (bool) $internal;
	}

	public function getInternal() {
		return $this->internal;
	}

	public function setLockType($lockType) {
		if ($lockType !== '' && in_array($lockType, $this->allowedLockTypes) === FALSE) {
			throw new LogicException('The defined lockType is not allowed.');
		}

		$this->lockType = $lockType;
	}

	public function getLockType() {
		return $this->lockType;
	}

	public function setModule($module) {
		$this->module = (string) $module;
	}

	public function getModule() {
		return $this->module;
	}

	public function setVersionNumber($versionNumber) {
		$version = TYPO3_Version::create($versionNumber);
		$this->setVersion($version);
	}

	public function setVersion(TYPO3_Version $version) {
		$this->version = $version;
	}

	public function getVersion() {
		return $this->version;
	}

	public function setState($state) {
		if (in_array($state, $this->allowedStates) === FALSE) {
			throw new LogicException('The defined state is not allowed.');
		}

		$this->state = $state;
	}

	public function getState() {
		return $this->state;
	}

	public function setDependencies($dependencies) {
		$this->dependencies = (string) $dependencies;
	}

	public function getDependencies() {
		return $this->dependencies;
	}

	public function setConflicts($conflicts) {
		$this->conflicts = (string) $conflicts;
	}

	public function getConflicts() {
		return $this->conflicts;
	}

	public function setPriority($priority) {
		if ($priority !== '' && in_array($priority, $this->allowedPriorities) === FALSE) {
			throw new LogicException('The defined priority is not allowed.');
		}

		$this->priority = $priority;
	}

	public function getPriority() {
		return $this->priority;
	}

	public function setLoadOrder($loadOrder) {
		$this->loadOrder = (string) $loadOrder;
	}

	public function getLoadOrder() {
		return $this->loadOrder;
	}

	public function setClearCacheOnLoad($clearCacheOnLoad) {
		$this->clearCacheOnLoad = (bool) $clearCacheOnLoad;
	}

	public function getClearCacheOnLoad() {
		return $this->clearCacheOnLoad;
	}

	public function setCreateDirectories($createDirectories) {
		$this->createDirectories = (string) $createDirectories;
	}

	public function getCreateDirectories() {
		return $this->createDirectories;
	}

	public function setUploadFolder($uploadFolder) {
		$this->uploadFolder = (bool) $uploadFolder;
	}

	public function getUploadFolder() {
		return $this->uploadFolder;
	}

	public function setModifyTables($modifyTables) {
		$this->modifyTables = (string) $modifyTables;
	}

	public function getModifyTables() {
		return $this->modifyTables;
	}

	public function setAuthorName($authorName) {
		$this->authorName = (string) $authorName;
	}

	public function getAuthorName() {
		return $this->authorName;
	}

	public function setAuthorMail($authorMail) {
		$this->authorMail = (string) $authorMail;
	}

	public function getAuthorMail() {
		return $this->authorMail;
	}

	public function setAuthorCompany($authorCompany) {
		$this->authorCompany = (string) $authorCompany;
	}

	public function getAuthorCompany() {
		return $this->authorCompany;
	}

	public function setIgnoreInFrontend($ignoreInFrontend) {
		$this->ignoreInFrontend = (bool) $ignoreInFrontend;
	}

	public function getIgnoreInFrontend() {
		return $this->ignoreInFrontend;
	}

	public function setCglComplianceGeneral($cglComplianceGeneral) {
		$this->cglComplianceGeneral = (string) $cglComplianceGeneral;
	}

	public function getCglComplianceGeneral() {
		return $this->cglComplianceGeneral;
	}

	public function setCglComplianceNote($cglComplianceNote) {
		$this->cglComplianceNote = (string) $cglComplianceNote;
	}

	public function getCglComplianceNote() {
		return $this->cglComplianceNote;
	}

	public function setContraintCollection(TYPO3_Extension_Configuration_ConstraintCollection $constraintCollection) {
		$this->constraintCollection = $constraintCollection;
	}

	public function getContraintCollection() {
		return $this->constraintCollection;
	}

	public function setMD5Values($MD5Values) {
		$this->MD5Values = (string) $MD5Values;
	}

	public function getMD5Values() {
		return $this->MD5Values;
	}

	public function setFile(TYPO3_Extension_IO_File $file) {
		$file->setWriteCallback($this, 'writeFileCallback');
		$this->file = $file;
	}

	public function getFile() {
		return $this->file;
	}

	public function write() {
		if (is_null($this->file)) {
			throw new RuntimeException('This is an anonymous configuration and not related to a file.');
		}

		$this->getFile()->write();
	}

	public function writeFileCallback(TYPO3_Extension_IO_File $file) {
		$file->setContent($this->__toString());
	}

	public function updateMD5Values() {
		if (is_null($this->file)) {
			throw new RuntimeException('This is an anonymous configuration and not related to a file.');
		}

		if (is_null($this->file->getDirectory())) {
			throw new RuntimeException('This is an anonymous file and not related to a directory.');
		}

		$MD5Values = array();

		/** @var $file TYPO3_Extension_IO_File */
		foreach ($this->getFile()->getDirectory()->getIO()->getAllFiles() as $file) {
			if ($file !== $this->file) {
				$MD5Values[$file->getRelativePath()] = $file->getShortHash();
			}
		}

		$this->setMD5Values(serialize($MD5Values));
	}
}
