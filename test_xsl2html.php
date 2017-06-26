<?php

header("Content-Type: text/html; charset=UTF-8");
$xml = new DOMDocument();
$xml->Load("sitemap.xml");
$xsl = new DOMDocument();
$xsl->Load("sitemap-xml.xsl");
$xslproc = new XSLTProcessor();
$xslproc->importStylesheet($xsl);
echo $xslproc->transformToXML($xml);

$f = fopen('sitem.html', 'w+');
fwrite($f, $xslproc->transformToXML($xml));
fclose($f);

?>
