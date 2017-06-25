<?php
/**
 * @author		Sandy <sandy@mimvp.com>
 * @copyright	2009-2017 mimvp.com
 * @datetime	2017.06.20
 * @version		1.0.1
 */

$cost_time_start = getMillisecond();

$CONFIG = array("domain"=>"http://mimvp.com",
				"xmlfile"=>"xmls/sitemap",
				"isschemamore"=>true);

$GChangeFreqArray = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
$GFileTypesArray = array('php', 'html', 'xml', 'txt', 'zip', 'pdf');	// 过滤文件类型
$GPriorityArray = array("1"=>"1", "2"=>"0.8", "3"=>"0.6");				// 按照层级对应优先级，第一层优先级为1，第二级为0.8，第三级为0.6

// 包含文件
$GIncludeArray = array("", "/index.php", "about.php", "hr.php");

// 排除文件
$GExcludeArray = array("usercenter", "sadmin");

var_dump($CONFIG);

$sitemap = new Sitemap($CONFIG['domain']);			// http://mimvp.com
$sitemap->setXmlFile($CONFIG['xmlfile']);			// 设置xml文件（可选）
$sitemap->setDomain($CONFIG['domain']);				// 设置自定义的根域名（可选）
$sitemap->setIsChemaMore($CONFIG['isschemamore']);	// 设置是否写入额外的Schema头信息（可选）

// $sitemap->addItem('/', '1.0', 'daily', 'Today');
// $sitemap->addItem('/index.php', '1.0', 'daily', 'Today');
// $sitemap->addItem('/hr.php', '0.8', 'monthly', 'Jun 25');
// $sitemap->addItem('/about.php', '0.8', 'monthly', 'Jun 25');

// $sitemap->addItem('/', '1.0', 'daily', '2017-06-01');
// $sitemap->addItem('/index.php', '1.0', 'daily', '2017-06-05');
// $sitemap->addItem('/hr.php', '0.8', 'monthly', '2017-06-13');
// $sitemap->addItem('/about.php', '0.8', 'monthly', '2017-06-25');

$scanRootPathArray = array();
scanRootPath($scanRootPathArray);	//  扫描当前根目录
createItems($sitemap, $GPriorityArray, $GIncludeArray, $GExcludeArray, $scanRootPathArray);

// createItems($sitemap, $GPriorityArray, $GIncludeArray, $GExcludeArray);

$sitemap->endSitemap();

echo "<script>window.open('".$sitemap->getCurrXmlFileFullPath()."')</script>";
echo "<br>Create Sitemap Success";

$cost_time = getMillisecond() - $cost_time_start;
$cost_time= sprintf('%01.6f', $cost_time);
echo "<br>cost_time : $cost_time (s)<br>";



/**
 * 创建sitemap items元素
 * @param unknown $sitemap				网站地图对象
 * @param unknown $GPriorityArray		优先级数组
 * @param unknown $GIncludeArray		包含文件数组
 * @param unknown $GExcludeArray		排除文件数组
 * @param array $scanRootPathArray		扫描根目录文件
 */
function createItems($sitemap, $GPriorityArray, $GIncludeArray, $GExcludeArray, $scanRootPathArray=array()) {
	
	echo "<br><br>";
	$itemsArray = array_merge($GIncludeArray, $scanRootPathArray);
	
	// 用于数组去重和格式化/开头文件
	$itemsArray2 = array();		
	foreach ($itemsArray as $item) {
		$item = str_replace(pathinfo ( __FILE__, PATHINFO_DIRNAME ), "", $item);
		
		// 格式化 about.php 转为 /about.php
		if(substr($item, 0, 1) !== "/") {
			$item = "/" . $item;
		}
		
		// 剔除排除的文件
		$isExclude = false;
		foreach ($GExcludeArray as $excludeFile) {
			if(stripos($item, $excludeFile) > 0) {
				echo "exclude  --  $item  $excludeFile   true <br>";
				$isExclude = true;
				break;
			}
		}
		if($isExclude) continue;
		
		// 数组去重
		if(!in_array($item, $itemsArray2)) {			
			array_push($itemsArray2, $item);
		}
	}
	var_dump($itemsArray2);
	
	// 生成sitemap item
	$idx = 0;
	foreach ($itemsArray2 as $item) {
		$idx += 1;
		echo "$idx \t\t $item<br>";
		$priority = $GPriorityArray[substr_count($item, "/")];
		$sitemap->addItem($item, $priority, "daily", time());
	}
}


/**
 * 递归扫描根目录，默认最多扫描三层
 * @param string $rootPath		根目录
 * @param number $dirLevel		扫描的当前层级
 * @param number $MaxDirLevel	扫描的最大层级
 */
function scanRootPath(&$resArray, $rootPath=".", $dirLevel=1, $MaxDirLevel=3) {
	global $GFileTypesArray;
	
	if($dirLevel > $MaxDirLevel) {
		return;
	}
	
	if($rootPath == "." || $rootPath == "./") {
		$rootPath = pathinfo ( __FILE__, PATHINFO_DIRNAME );
	}
	$rootDir = opendir($rootPath);
	
	while(($file = readdir($rootDir)) !== false) {
		if($file == "." || $file == "..") continue;
		
		$filefullpath = $rootPath . "/" . $file;
		$fileinfo = pathinfo($filefullpath);
		if(isset($fileinfo['extension']) && in_array($fileinfo['extension'], $GFileTypesArray)) {
			echo $filefullpath. "<br>";
			array_push($resArray, $filefullpath);
		}
		
		if(is_dir($filefullpath)) {
			scanRootPath($resArray, $filefullpath, $dirLevel+1, $MaxDirLevel);
		}
	}
}

//  获取毫秒的时间戳
function getMillisecond() {
	$time = explode(" ", microtime());
	return $time[1] + $time[0];
}

/**
 * Sitemap
 *
 * 生成 Google Sitemap files (sitemap.xml)
 *
 * @package    Sitemap
 * @author     Sandy <sandy@mimvp.com>
 * @copyright  2009-2017 mimvp.com
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       http://github.com/mimvp/sitemap-php
 */
class Sitemap {
	
	private $writer;		// XMLWriter对象
	private $domain = "http://mimvp.com";			// 网站地图根域名
	private $xmlFile = "sitemap";					// 网站地图xml文件（不含后缀.xml）
	private $xmlFileFolder = "";					// 网站地图xml文件夹
	private $currXmlFileFullPath = "";				// 网站地图xml文件当前全路径
	private $isSchemaMore= true;					// 网站地图是否添加额外的schema
	private $current_item = 0;						// 网站地图item个数（序号）
	private $current_sitemap = 0;					// 网站地图的个数（序号）
	
	const SCHEMA_XMLNS = 'http://www.sitemaps.org/schemas/sitemap/0.9';
	const SCHEMA_XMLNS_XSI = 'http://www.w3.org/2001/XMLSchema-instance';
	const SCHEMA_XSI_SCHEMALOCATION = 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';
	const DEFAULT_PRIORITY = 0.5;
	const SITEMAP_ITEMS = 50000;
	const SITEMAP_SEPERATOR = '-';
	const INDEX_SUFFIX = 'index';
	const SITEMAP_EXT = '.xml';
	
	/**
	 * @param string $domain	：	初始化网站地图根域名
	 */
	public function __construct($domain) {
		$this->setDomain($domain);
	}
	
	/**
	 * 设置网站地图根域名，开头用 http:// or https://
	 * @param string $domain	：	网站地图根域名 <br>例如: http://mimvp.com
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}
	
	/**
	 * 返回网站根域名
	 */
	private function getDomain() {
		return $this->domain;
	}
	
	/**
	 * 设置网站地图的xml文件名
	 */
	public function setXmlFile($xmlFile) {
		$base = basename($xmlFile);
		$dir = dirname($xmlFile);
		if(!is_dir($dir)) {
			$res = mkdir(iconv("UTF-8", "GBK", $dir), 0777, true);
			if($res) {
				echo "mkdir $dir success";
			} else {
				echo "mkdir $dir fail.";
			}
		}
		$this->xmlFile = $xmlFile;
		return $this;
	}
	
	/**
	 * 返回网站地图的xml文件名
	 */
	private function getXmlFile() {
		return $this->xmlFile;
	}
	
	public function setIsChemaMore($isSchemaMore) {
		$this->isSchemaMore = $isSchemaMore;
	}
	
	private function getIsSchemaMore() {
		return $this->isSchemaMore;
	}
	
	/**
	 * 设置XMLWriter对象
	 */
	private function setWriter(XMLWriter $writer) {
		$this->writer = $writer;
	}
	
	/**
	 * 返回XMLWriter对象
	 */
	private function getWriter() {
		return $this->writer;
	}
	
	/**
	 * 返回网站地图的当前item
	 * @return int
	 */
	private function getCurrentItem() {
		return $this->current_item;
	}
	
	/**
	 * 设置网站地图的item个数加1
	 */
	private function incCurrentItem() {
		$this->current_item = $this->current_item + 1;
	}
	
	/**
	 * 返回当前网站地图（默认50000个item则新建一个网站地图）
	 * @return int
	 */
	private function getCurrentSitemap() {
		return $this->current_sitemap;
	}
	
	/**
	 * 设置网站地图个数加1
	 */
	private function incCurrentSitemap() {
		$this->current_sitemap = $this->current_sitemap + 1;
	}
	
	private function getXMLFileFullPath() {
		$xmlfileFullPath = "";
		if ($this->getCurrentSitemap()) {
			$xmlfileFullPath = $this->getXmlFile() . self::SITEMAP_SEPERATOR . $this->getCurrentSitemap() . self::SITEMAP_EXT;	// 第n个网站地图xml文件名 + -n + 后缀.xml
		} else {
			$xmlfileFullPath = $this->getXmlFile() . self::SITEMAP_EXT;	// 第一个网站地图xml文件名 + 后缀.xml
		}
		$this->setCurrXmlFileFullPath($xmlfileFullPath);		// 保存当前xml文件全路径
		return $xmlfileFullPath;
	}
	
	public function getCurrXmlFileFullPath() {
		return $this->currXmlFileFullPath;
	}
	
	private function setCurrXmlFileFullPath($currXmlFileFullPath) {
		$this->currXmlFileFullPath = $currXmlFileFullPath;
	}
	
	/**
	 * Prepares sitemap XML document
	 */
	private function startSitemap() {
		$this->setWriter(new XMLWriter());
		$this->getWriter()->openURI($this->getXMLFileFullPath());	// 获取xml文件全路径
		
		$this->getWriter()->startDocument('1.0', 'UTF-8');
		$this->getWriter()->setIndentString("\t");
		$this->getWriter()->setIndent(true);
		$this->getWriter()->startElement('urlset');
		if($this->getIsSchemaMore()) {
			$this->getWriter()->writeAttribute('xmlns:xsi', self::SCHEMA_XMLNS_XSI);
			$this->getWriter()->writeAttribute('xsi:schemaLocation', self::SCHEMA_XSI_SCHEMALOCATION);
		}
		$this->getWriter()->writeAttribute('xmlns', self::SCHEMA_XMLNS);
	}
	
	/**
	 * 写入item元素，url、loc、priority字段必选，changefreq、lastmod可选
	 */
	public function addItem($loc, $priority = self::DEFAULT_PRIORITY, $changefreq = NULL, $lastmod = NULL) {
		if (($this->getCurrentItem() % self::SITEMAP_ITEMS) == 0) {
			if ($this->getWriter() instanceof XMLWriter) {
				$this->endSitemap();
			}
			$this->startSitemap();
			$this->incCurrentSitemap();
		}
		$this->incCurrentItem();
		$this->getWriter()->startElement('url');
		$this->getWriter()->writeElement('loc', $this->getDomain() . $loc);			// 必选
		$this->getWriter()->writeElement('priority', $priority);					// 必选
		if ($changefreq) {
			$this->getWriter()->writeElement('changefreq', $changefreq);			// 可选
		}
		if ($lastmod) {
			$this->getWriter()->writeElement('lastmod', $this->getLastModifiedDate($lastmod));	// 可选
		}
		$this->getWriter()->endElement();
		return $this;
	}
	
	/**
	 * 转义时间格式，返回时间格式为 2016-09-12
	 */
	private function getLastModifiedDate($date=null) {
		if(null == $date) {
			$date = time();
		}
		if (ctype_digit($date)) {
			return date('c', $date);	// Y-m-d
		} else {
			$date = strtotime($date);
			return date('c', $date);
		}
	}
	
	/**
	 * 结束网站xml文档，配合开始xml文档使用
	 */
	public function endSitemap() {
		if (!$this->getWriter()) {
			$this->startSitemap();
		}
		$this->getWriter()->endElement();
		$this->getWriter()->endDocument();
		$this->getWriter()->flush();
	}
	
	/**
	 * Writes Google sitemap index for generated sitemap files
	 *
	 * @param string $loc Accessible URL path of sitemaps
	 * @param string|int $lastmod The date of last modification of sitemap. Unix timestamp or any English textual datetime description.
	 */
	public function createSitemapIndex($loc, $lastmod = 'Today') {
		$indexwriter = new XMLWriter();
		$indexwriter->openURI($this->getXmlFile() . self::SITEMAP_SEPERATOR . self::INDEX_SUFFIX . self::SITEMAP_EXT);
		$indexwriter->startDocument('1.0', 'UTF-8');
		$indexwriter->setIndent(true);
		$indexwriter->startElement('sitemapindex');
		$indexwriter->writeAttribute('xmlns:xsi', self::SCHEMA_XMLNS_XSI);
		$indexwriter->writeAttribute('xsi:schemaLocation', self::SCHEMA_XSI_SCHEMALOCATION);
		$indexwriter->writeAttribute('xmlns', self::SCHEMA_XMLNS);
		for ($index = 0; $index < $this->getCurrentSitemap(); $index++) {
			$indexwriter->startElement('sitemap');
			$indexwriter->writeElement('loc', $loc . $this->getFilename() . ($index ? self::SITEMAP_SEPERATOR . $index : '') . self::SITEMAP_EXT);
			$indexwriter->writeElement('lastmod', $this->getLastModifiedDate($lastmod));
			$indexwriter->endElement();
		}
		$indexwriter->endElement();
		$indexwriter->endDocument();
	}
	
}
