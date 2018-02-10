# Twitter reach calculator

## Task description

Develop a simple Laravel application using best practices that calculates the reach of a specific tweet.
A user should be able to enter the URL of a tweet. The application will lookup the people who retweeted 
the tweet using the Twitter API. The application then sums up the amount of followers each user has that 
has retweeted the tweet. These results need to be stored in the cached for two hours. If someone tries to 
calculate the reach of a tweet that has already been calculated the results should be returned from the 
cache. After two hours the cache should be updated.

So:

- Input: URL of individual Tweet (string).
- Output: calculated reach of Tweet (integer).
- Cache lifetime: 120 minutes.
- Definitions:
	- *Reach*: actual (!= potential) size of audience for Tweet, i.e. being the sum of all followers
	for everyone who retweeted the Tweet.
	- *Tweet URL*: URI with the [Twitter status ID](https://developer.twitter.com/en/docs/basics/twitter-ids) in it.
	
Out of scope:

- Contracts/interfaces.
- Hitting the API rate limiter.

Limitation:

- By design, the Twitter API v1.1 only returns up to 100 user IDs who retweeted a Tweet, see the 
[API docs](https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/get-statuses-retweeters-ids).
There is no way around this for now. One could make a web scraper or some other convoluted way of solving the problem
at hand, but not this time :)

## Commands

A selection of useful `php artisan ...` commands.

| Command                                           | Description                                        		|
| --------------------------------------------------| --------------------------------------------------------- |
| `ide-helper:generate`                             | Re-generate IDE documentation.                     		|
| `tweet:reach {tweetUrl}`                          | Compute the reach of a Tweet by providing a Tweet URL.    |

## Sysops

### Requirements

- PHP `>= 7.2.0`

### Environments

- Local: 
  - `php artisan serve` to start [localhost:8000](http://localhost:8000)
  - On macOS, install [Valet](https://laravel.com/docs/5.5/valet), run `valet link twitter-reach-calculator` from `/public` and open 
  [twitter-reach-calculator.dev](https://twitter-reach-calculator.dev). Use `valet secure twitter-reach-calculator` to secure with TLS.

### Debugbar

The [Laravel Debugbar](https://github.com/barryvdh/laravel-debugbar) is installed for development environments. 
Auto enabled when `APP_DEBUG=true`.

```php
Debugbar::info($object);
Debugbar::error('Error!');
Debugbar::warning('Watch out…');
Debugbar::addMessage('Another message', 'mylabel');
```

### Kint
[Kint](https://github.com/kint-php/kint) is installed for easy debugging when in CLI with `Kint::dump($var);`.

### IDE

I suggest using [Jetbrain's PHPStorm](https://www.jetbrains.com/phpstorm/). IDE config files are present in the repo to
ease setting up your development environment and to ensure consistent code style and conventions.

## Attribution

### Author
- Dick de Leeuw, [leeuw.studio](https://leeuw.studio)

### Dependencies
- https://github.com/kint-php/kint
- https://github.com/thujohn/twitter
- https://github.com/coduo/php-humanizer
- https://github.com/LaravelCollective/html
