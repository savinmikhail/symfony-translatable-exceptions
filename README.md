## Installation

```bash
composer require --dev savinmikhail/symfony-translatable-exceptions
```

## Usage 

In your `psalm.xml` add the following lines

```xml
    <plugins>
        <pluginClass class="SavinMikhail\TranslatableExceptionsPlugin\Plugin"/>
    </plugins>
```

## Output

```bash

ERROR: InvalidArgument - src/Service/Extractor/WhlExtractor.php:33:40 - Avoid hardcoding exception messages, use a translation mechanism instead. (see https://psalm.dev/004)
            throw new RuntimeException('File upload required.');
```

To fix this, we need to use

```bash
throw new RuntimeException(this->translator->trans('error.file_upload_required'));
```