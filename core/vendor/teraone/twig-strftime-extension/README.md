# Twig strftime Filter
Use strftime in twig

```twig 
{{ now|date_modify("+3 day")|strftime('%A') }} //-> Monday (if today is thursday at least :) )

{{ date()|strftime('%I:%M:%S %p') }} //-> Display the current time

{{ '2017-12-01'|strftime('%d %B %Y') }} //-> Accept string filtering (compatible with strtotime)

```

# Installation
```bash
composer require teraone/twig-strftime-extension
```

Add the extension to your twig environment
```php
// set your locale
setlocale(LC_ALL, 'de_DE');
$twig->addExtension(new Teraone\Twig\Extension\StrftimeExtension());

// optional: set Timezone
$twig->getExtension('core')->setTimezone('Europe/Berlin');
```

For the full list of supported time formats have a look at the <a href="http://php.net/manual/en/function.strftime.php">PHP strftime documentation</a>
