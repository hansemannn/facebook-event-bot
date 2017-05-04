# Facebook Event Bot

A quick Facebook Event Bot that will attend all specified events of the specified Facebook page.

## Example

```php
require_once __DIR__ . '/FacebookEventBot.php';

$bot = new FacebookEventBot(array(
   'appId' => 'APP_ID',
   'appSecret' => 'APP_SECRET',
   'userId' => 'USER_ID',
   'accessToken' => 'ACCESS_TOKEN',
   'pageId' => 'KleineFreiheitOS',
   'matchingEvent' => 'Kleiner Freitag'
));

$result = $bot->attendAllEvents();

echo(json_encode($result));
```

## Author

Hans Knoechel ([@hansemannnn](https://twitter.com/hansemannnn) / [Web](http://hans-knoechel.de))

## License

MIT
