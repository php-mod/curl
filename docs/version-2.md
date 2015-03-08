NOTE: Version 2 is not complete yet,  it is an alpha, so features may be changed in the future.

## Execute

```php
        // create a new cURL resource
        $ch = new Curl();

        // set URL and other appropriate options
        $ch
            ->setUrl("http://www.example.com/")
            ->includeHeader(false);

        // grab URL and pass it to the browser
        $ch->execute();

        // close cURL resource, and free up system resources
        unset($ch);
```

## Escape

```php
        // Create a curl handle
        $ch = new Curl();

        // Escape a string used as a GET parameter
        $location = $ch->escape('Hofbräuhaus / München');
        // Result: Hofbr%C3%A4uhaus%20%2F%20M%C3%BCnchen

        // Compose an URL with the escaped string
        $url = "http://example.com/add_location.php?location={$location}";
        // Result: http://example.com/add_location.php?location=Hofbr%C3%A4uhaus%20%2F%20M%C3%BCnchen

        // Send HTTP request and close the handle
        $ch
            ->setUrl($url)
            ->returnTransfer(true)
            ->execute();

        unset($ch);
```

## Errors

Curl Class throws an exceptions when an error occurred.

```php
        // Create a curl handle to a non-existing location
        $ch = new Curl('http://404.php.net/');

        try {
            $ch
                ->returnTransfer()
                ->execute();
        } catch(Exception $e) {
            echo 'Curl error: ' . $e->getCode() . ' - ' . $e->getMessage();
        }

        unset($ch);
```

## Clone a resource

Curl Class has a Copy Constructor that copy the cURL resource too.

```php
        $c2 = clone $c1;
        $c2->getHandle() === $c1->getHandle(); // False
```

## Destructor

Curl class has a destructor that closes the cURL resource when the Curl object is destructed.

```php
        $ch = new Curl();
        
        $ch
            ->setUrl('http://www.example.com/')
            ->includeHeader(false)
            ->execute();

        unset($ch);
```

## Get Info

```php
        // Create a curl handle
        $ch = new Curl('http://www.yahoo.com/');

        try {
            // Execute
            $ch->execute();

            $info = $ch->getInfo();

            echo 'Took ' . $info->getTotalTime() . ' seconds to send a request to ' . $info->getUrl();
        } catch (Exception $e) {

        }

        // Close handle
        unset($ch);
```
