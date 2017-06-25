<?php
/**
 * @author		Sandy <sandy@mimvp.com>
 * @copyright	2009-2017 mimvp.com
 * @datetime	2017.06.20
 * @version		1.0.1
 */


$xmlFile = 'sitemap_index.xml';
echo "<br> xmlFile : $xmlFile <br>";

$sitemap = new SitemapIndex($xmlFile);
// $sitemap->setUseGzip(true);

$sitemap->addSitemap('http://mimvp.com/sitemap.xml');
$sitemap->addSitemap('http://mimvp.com/sitemap-2.xml', time()-1000000);
$sitemap->addSitemap('http://mimvp.com/sitemap-3.xml', '2017-06-22
');
$sitemap->endSitemap();

echo "<script>window.open('" . $xmlFile . "')</script>";
echo "<br>Create SitemapIndex Success<br>";



/**
 * SitemapIndex
 *
 * 生成 Google Sitemaps index (sitemap_index.xml)
 *
 * @package    Sitemap
 * @author     Sandy <sandy@mimvp.com>
 * @copyright  2009-2017 mimvp.com
 * @license    http://opensource.org/licenses/MIT MIT License
 * @link       http://github.com/mimvp/sitemap-php
 */
class SitemapIndex
{
	private $writer;
	private $filePath;
	private $useGzip = false;
	
	public function __construct($filePath)
	{
		$this->filePath = $filePath;
	}
	
	public function getFilePath()
	{
		return $this->filePath;
	}
	
	private function createSitemap()
	{
		$this->writer = new XMLWriter();
		$this->writer->openMemory();
		$this->writer->startDocument('1.0', 'UTF-8');
		$this->writer->setIndent(true);
		$this->writer->startElement('sitemapindex');
		$this->writer->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
	}
	
	public function addSitemap($location, $lastModified = null)
	{
		if ($this->writer === null) {
			$this->createSitemap();
		}
		
		$this->writer->startElement('sitemap');
		$this->writer->writeElement('loc', $location);
		$this->writer->writeElement('lastmod', $this->getLastModifiedDate($lastModified));
		$this->writer->endElement();
	}
	
	public function endSitemap()
	{
		if ($this->writer instanceof XMLWriter) {
			$this->writer->endElement();
			$this->writer->endDocument();
			$filePath = $this->getFilePath();
// 			if ($this->useGzip) {
// 				$filePath = 'compress.zlib://' . $filePath;
// 			}
			file_put_contents($filePath, $this->writer->flush());
		}
	}
	
	public function setUseGzip($value)
	{
		if ($value && !extension_loaded('zlib')) {
			throw new \RuntimeException('Zlib extension must be installed to gzip the sitemap.');
		}
		$this->useGzip = $value;
	}
	
	private function getLastModifiedDate($date=null) {
		if(null == $date) {
			$date = time();
		}
		if (ctype_digit($date)) {
			return date('c', $date);
		} else {
			$date = strtotime($date);
			return date('c', $date);	// Y-m-d
		}
	}
}
