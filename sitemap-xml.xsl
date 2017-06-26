<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
                xmlns:html="http://www.w3.org/TR/REC-html40"
                xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
	<xsl:template match="/">
		<html xmlns="http://www.w3.org/1999/xhtml">
			<head>
				<title>XML Sitemap</title>
				<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
				<style type="text/css">
					body {
						font-family:"Lucida Grande","Lucida Sans Unicode",Tahoma,Verdana;
						font-size:12px;
					}
					h1 {color:#0099CC;}
					#intro {
						background-color:#CFEBF7;
						border:1px #2580B2 solid;
						padding:5px 13px 5px 13px;
						margin:10px;
					}
					
					#intro p {
						line-height: 16.8667px;
					}
					
					td {
						font-size: 14px;
					}
					
					th {
						text-align:left;
						padding-right:30px;
						font-size:14px;
					}
					
					tr.high {
						background-color:whitesmoke;
					}
					
					#footer {
						padding:2px;
						margin:10px;
						font-size:8pt;
						color:gray;
					}
					
					#footer a {
						color:gray;
					}
					
					a {
						color:black;
					}
				</style>
			</head>
			<body>
				<h1>XML Sitemap</h1>
				<div id="intro">
					<p>
						This is a XML Sitemap which is supposed to be processed by search engines like <a href="http://www.google.com">Google</a>, <a href="http://bing.com">Bing</a>, <a href="http://www.yahoo.com">Yahoo</a> and <a href="http://www.baidu.com">Baidu</a>.<br />
						With such a sitemap, it's much easier for the crawlers to see the complete structure of your site and retrieve it more efficiently.<br />
						More information about what <a href="https://github.com/mimvp/sitemap-php" title="XML Sitemap">XML Sitemap</a> is and how it can help you to get indexed by the major search engines can be found at <a href="http://www.sitemapx.com" title="sitemap">SitemapX.com</a>.<br />
						© 2009 - 2017 All Rights by <a href="http://mimvp.com">mimvp.com</a>, Sitemap Demo: <a href="http://mimvp.com/sitemap.html">http://mimvp.com/sitemap.html</a>, Sitemap Github: <a href="https://github.com/mimvp/sitemap-php">https://github.com/mimvp/sitemap-php</a>.
					</p>
	        </div>
				<div id="content">
					<table cellpadding="5" width="100%">
						<tr style="border-bottom:1px black solid;">
							<th width="60%">URL</th>
							<th>Priority</th>
							<th>Change Frequency</th>
							<th width="20%">Last Change</th>
						</tr>
						<xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
						<xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
						<xsl:for-each select="sitemap:urlset/sitemap:url">
							<tr>
								<xsl:if test="position() mod 2 != 1">
									<xsl:attribute  name="class">high</xsl:attribute>
								</xsl:if>
								<td>
									<xsl:variable name="itemURL">
										<xsl:value-of select="sitemap:loc"/>
									</xsl:variable>
									<a href="{$itemURL}">
										<xsl:value-of select="sitemap:loc"/>
									</a>
								</td>
								<td>
									<xsl:value-of select="concat(sitemap:priority*100,'%')"/>
								</td>
								<td>
									<xsl:value-of select="concat(translate(substring(sitemap:changefreq, 1, 1),concat($lower, $upper),concat($upper, $lower)),substring(sitemap:changefreq, 2))"/>
								</td>
								<td>
									<xsl:value-of select="concat(substring(sitemap:lastmod,0,11),concat(' ', substring(sitemap:lastmod,12,5)))"/>
								</td>
							</tr>
						</xsl:for-each>
					</table>
				</div>
				<div id="footer">
					Generated with Google <a href="http://mimvp.com/sitemap.html" title="MIMVP Sitemap Generator">Sitemap Generator</a> Plugin for Website by <a href="http://mimvp.com" title="sitemap">MIMVP.com</a>.</div>
			</body>
		</html>
	</xsl:template>
</xsl:stylesheet>