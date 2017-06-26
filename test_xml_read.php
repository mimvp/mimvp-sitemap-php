<?php
/**
 * mimvp.com
 * 2017.06.22
 */

header ( "Content-type: text/html; charset=utf-8" );
$xml = new XMLReader ();

// 载入xml文件，如果是字符串直接使用xml方法
$xml->open ( "mimvp.xml" );

// 开始读取xml
while ( $xml->read () ) {
	// 根据节点类型和元素名称取得文本节点或属性
	if ($xml->nodeType == XMLREADER::ELEMENT && $xml->localName == 'item') {
		print $xml->getAttribute ( "info" ) . "</br>";
	}
	if ($xml->nodeType == XMLREADER::ELEMENT && $xml->localName == 'id') {
		$xml->read ();	// 移动指针到下一个节点
		print $xml->value . "\t";
	}
	if ($xml->nodeType == XMLREADER::ELEMENT && $xml->localName == 'name') {
		$xml->read ();	
		print $xml->value . "</br>";
	}
	if ($xml->nodeType == XMLREADER::ELEMENT && $xml->localName == 'age') {
		$xml->read ();
		print $xml->value . "</br>";
	}
}
?>  