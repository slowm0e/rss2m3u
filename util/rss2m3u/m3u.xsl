<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:strip-space elements="*"/>
<xsl:output method="text" encoding="UTF-8" omit-xml-declaration="yes"/>

<xsl:template name="monthNum">
    <xsl:param name="mon"/>
    <xsl:choose>
        <xsl:when test="$mon='Jan'">01</xsl:when>
        <xsl:when test="$mon='Feb'">02</xsl:when>
        <xsl:when test="$mon='Mar'">03</xsl:when>
        <xsl:when test="$mon='Apr'">04</xsl:when>
        <xsl:when test="$mon='May'">05</xsl:when>
        <xsl:when test="$mon='Jun'">06</xsl:when>
        <xsl:when test="$mon='Jul'">07</xsl:when>
        <xsl:when test="$mon='Aug'">08</xsl:when>
        <xsl:when test="$mon='Sep'">09</xsl:when>
        <xsl:when test="$mon='Oct'">10</xsl:when>
        <xsl:when test="$mon='Nov'">11</xsl:when>
        <xsl:when test="$mon='Dec'">12</xsl:when>
    </xsl:choose>
</xsl:template>

<xsl:template match="rss/channel">
<xsl:text>#EXTM3U&#10;</xsl:text>
<xsl:apply-templates select="item"/>
</xsl:template>

<xsl:template match="item">
<xsl:if test="string-length(enclosure/@url) > 10">
<xsl:text>#EXTINF:-1,</xsl:text>
<xsl:if test="string-length(pubDate) > 0">
    <xsl:value-of select="substring(pubDate, 6, 2)"/>
    <xsl:text>.</xsl:text>
    <xsl:call-template name="monthNum">
        <xsl:with-param name="mon" select="substring(pubDate, 9, 3)"/>
    </xsl:call-template>
    <xsl:text> </xsl:text>
</xsl:if>
<xsl:value-of select="normalize-space(title)"/>
<xsl:text>&#10;</xsl:text>
<xsl:value-of select="enclosure/@url"/><xsl:text>&#10;</xsl:text>
</xsl:if>
</xsl:template>

</xsl:stylesheet>
