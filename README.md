# php-sitemap-generator
PHP Sitemaps &amp; Sitemap Index Generator

# Requirements
Requires `PHP5` with `libxml` PHP extension. The DOM extension uses UTF-8 encoding.

# Installation
Download & Include `Sitemap.php` PHP Class File.

# Example
```php
$sitemap = new Sitemap();
$sitemap->baseurl = 'https://www.example.com';
$sitemap->target = '../';
$sitemap->limit = 10000;
$sitemap->add( '/index.html' );
$sitemap->add( '/about.html' );
$sitemap->add( '/news.html', '2016-09-10', 'daily', 0.7 );
$sitemap->generate();
```

# Documentation
```php
// Base Url of Sitemap Urls
public string $baseurl = 'https://example.com/'
```

```php
// Save path of Sitemap File(s)
public string $target = './'
```

```php
// Url limitation of Sitemap File(s)
public integer $limit = 25000
```

```php
// Add URL to Sitemap 
public function add( url $loc, string $lastmod = '', string $changefreq = '', float $priority = ''  )

// Parameters
$loc // Relative Url (without baseurl) 
$lastmod // Date or Datetime string (any format) 
$changefreq // Sitemap Spec. [always|hourly|daily|weekly|monthly|yearly|never] 
$priority // Sitemap Spec. Must between 0.0 and 1.0
```

```php
// Generate and Save Sitemap Files
public function generate( )
```
