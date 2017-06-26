<?php
/**
 * mimvp.com
 * 2017.06.22
 */

header("Content-type: text/html; charset=utf-8");
$xml = new XMLWriter();

$xml->openUri("php://output");	// 输出到网页控制台
$xml->openUri("mimvp.xml");		// 输出到文件，需要读写权限，推荐 chmod 766 mimvp.xml

// 设置缩进字符串
$xml->setIndentString("\t");
$xml->setIndent(true);

// xml文档开始
$xml->startDocument('1.0', 'utf-8');

// 创建根节点
$xml->startElement("MimvpInfo");

	// 节点1
	$xml->startElement("Item");
		$xml->writeAttribute("info","1");	// 属性
			$xml->startElement("id");
			$xml->text("01");
			$xml->endElement();
			
			$xml->startElement("name");
			$xml->text("米扑代理");
			$xml->endElement();
	$xml->endElement();
	
	// 节点2
	$xml->startElement("Item");
		$xml->writeAttribute("info","1");	// 属性
			$xml->startElement("id");
			$xml->text("02");
			$xml->endElement();
			
			$xml->startElement("name");
			$xml->text("米扑财富");
			$xml->endElement();
	$xml->endElement();
		
$xml->endElement();
$xml->endDocument();

//header("Content-type: text/xml");
//取得缓冲区里的xml字符串
//echo $xml->outputMemory();  
?>