<?php

/**
 * Sitemap Generator
 *
 * Generate Sitemap and Sitemap Index files
 *
 * Example:
 * <pre>
 * $sitemap = new Sitemap();
 * $sitemap->baseurl( 'https://www.example.com' );
 * $sitemap->target = '../';
 * $sitemap->limit = 10000;
 * $sitemap->add( '/index.html' );
 * $sitemap->add( '/about.html' );
 * $sitemap->add( '/news.html', '2016-09-10', 'daily', 0.7 );
 * $sitemap->generate();
 * </pre>
 *
 * @author Zsolt Tovis
 * @copyright Copyright (c) 2016, Zsolt Tovis
 * @uses DOMDocument
 */
class Sitemap {

  /**
   * @var string Base Url of Sitemap Urls
   */
  public $baseurl = 'https://example.com/';

  /**
   * @var string Save path of Sitemap File(s)
   */
  public $target = './';

  /**
   * @var int Url limitation of Sitemap File(s)
   */
  public $limit = 25000;

  /**
   * @ignore
   */
  private $urls = array();

  /**
   * @ignore
   */
  private $sitemaps = array();

  /**
   * Add URL to Sitemap
   *
   * @param url $loc Relative Url (without baseurl)
   * @param string $lastmod Date or Datetime string (any format)
   * @param string $changefreq Sitemap Spec. [always|hourly|daily|weekly|monthly|yearly|never]
   * @param float $priority Sitemap Spec. Must between 0.0 and 1.0
   * @return void
   */
  public function add( $loc, $lastmod = '', $changefreq = '', $priority = '' ) {

    $res = array();

    /* Check Loc */
    if ( $loc == '' ) {
      return false;
    } else {
      $res['loc'] = rtrim( $this->baseurl, '/' ) . $loc;
    }
    /* Check lastmod */
    if ( $lastmod !== '' && strtotime( $lastmod ) > 0 ) {
      $lastmod = date( "c", strtotime( $lastmod ) );
      $res['lastmod'] = $lastmod;
    }
    /* Check changefreq */
    if ( $changefreq !== '' && in_array( $changefreq, array( 'always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never' ) ) ) {
      $res['changefreq'] = $changefreq;
    }
    /* Check priority */
    if ( $priority !== '' && $priority * 10 >= 0 && $priority * 10 <= 10 ) {
      $priority = number_format( ( float ) $priority, 1, '.', null );
      $res['priority'] = $priority;
    }

    $this->urls[] = $res;

  }

  /**
   * Generate and Save Sitemap Files
   *
   * @return void
   */
  public function generate() {
    foreach ( glob( $this->target . '/sitemap*.xml' ) as $unlink ) {
      unlink( $this->target . '/' . basename( $unlink ) );
    }
    $this->generateSitemaps();
    $this->generateSitemapIndex();

  }

  /**
   * Generate Sitemap File(s)
   * @ignore
   */
  private function generateSitemaps() {

    /** XML Init */
    $doc = new DOMDocument( '1.0', 'UTF-8' );
    $doc->formatOutput = false;

    $urlset = $doc->createElement( 'urlset' );
    $attr = $doc->createAttribute( 'xmlns' );
    $attr->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';

    $urlset->appendChild( $attr );
    $root = $doc->appendChild( $urlset );


    /* Loop URLs */
    $i = 0;
    foreach ( $this->urls as $key => $urlset ) {

      if ( ++$i > $this->limit ) {
        break;
      }

      $node = $root->appendChild( $doc->createElement( 'url' ) );
      if ( isset( $urlset['loc'] ) ) {
        $node->appendChild( $doc->createElement( 'loc', $urlset['loc'] ) );
      }
      if ( isset( $urlset['lastmod'] ) ) {
        $node->appendChild( $doc->createElement( 'lastmod', $urlset['lastmod'] ) );
      }
      if ( isset( $urlset['changefreq'] ) ) {
        $node->appendChild( $doc->createElement( 'changefreq', $urlset['changefreq'] ) );
      }
      if ( isset( $urlset['priority'] ) ) {
        $node->appendChild( $doc->createElement( 'priority', $urlset['priority'] ) );
      }

      unset( $this->urls[$key] );
    }

    /* Save Sitemaps */
    $file_index = (!empty( $this->urls ) || !empty( $this->sitemaps )) ? count( $this->sitemaps ) + 1 : '';
    $file_name = sprintf( '/sitemap%s.xml', $file_index );
    file_put_contents( $this->target . $file_name, $doc->saveXML() );

    /* Add Sitemap to Sitemap Index */
    if ( !empty( $this->urls ) || !empty( $this->sitemaps ) ) {
      $this->sitemaps[] = rtrim( $this->baseurl, '/' ) . $file_name;
    }
    /* Check More Urls */
    if ( !empty( $this->urls ) ) {
      $this->generateSitemaps();
    }

  }

  /**
   * Generate Sitemap Index File
   * @ignore
   */
  private function generateSitemapIndex() {

    if ( empty( $this->sitemaps ) ) {
      return false;
    }

    /** XML Init */
    $doc = new DOMDocument( '1.0', 'UTF-8' );
    $doc->formatOutput = false;

    $root = $doc->createElement( 'sitemapindex' );
    $attr = $doc->createAttribute( 'xmlns' );

    $attr->value = 'http://www.sitemaps.org/schemas/sitemap/0.9';
    $root->appendChild( $attr );

    $root = $doc->appendChild( $root );

    /* Loop Sitemaps */
    foreach ( $this->sitemaps as $url ) {

      $node = $root->appendChild( $doc->createElement( 'sitemap' ) );
      $loc = $node->appendChild( $doc->createElement( 'loc', $url ) );
      $loc = $node->appendChild( $doc->createElement( 'lastmod', date( 'c' ) ) );
    }

    /* Save Sitmap Index */
    $file_save = sprintf( '%s/sitemap.xml', $this->target );
    file_put_contents( $file_save, $doc->saveXML() );

  }

}
