# Twitter reach calculator

## Task description

A simple Laravel application that calculates the reach of a specific tweet.
A user can enter the URL of a tweet. The application will lookup the people
who retweeted the tweet using the Twitter API. The application then sums up 
the amount of followers each user has that has retweeted the tweet. These 
results are cached for two hours.

So:

- Input: URL of individual Tweet (string).
- Output: calculated reach of Tweet (integer).
- Cache lifetime: 120 minutes.
- Definitions:
	- *Reach*: actual (!= potential) size of audience for Tweet, i.e. being the sum of all followers
	for everyone who retweeted the Tweet.
	- *Tweet URL*: URI with the [Twitter status ID](https://developer.twitter.com/en/docs/basics/twitter-ids) in it.

## Deliverable

### CLI

A `php artisan tweet:reach {url}` command to compute a Tweet's reach. Properly shows human input error's.

![CLI](public/img/cli.gif "CLI")

### Web UI

A small web tool calculate a Tweet's reach. Default, success and error state.

![Web](public/img/web.gif "Web")

### Unit test

Basic tests as proof of concept. Including model factories in `/database/factories/`.

![Unit tests](public/img/tests.gif "Unit tests")

### Limitation

- By design, the Twitter API v1.1 only returns up to 100 user IDs who retweeted a Tweet, see the 
[API docs](https://developer.twitter.com/en/docs/tweets/post-and-engage/api-reference/get-statuses-retweeters-ids).
There is no way around this for now. One could make a web scraper or some other convoluted way of solving the problem
at hand, but not this time :)

Out of scope:

- Contracts/interfaces.
- Hitting the API rate limiter.
- 100% code coverage.
- DDD's domain research.

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

## Conventions

### Code style

Laravel follows the [PSR-2](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) 
coding standard and the [PSR-4](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-4-autoloader.md) 
autoloading standard. We use a custom code style, i.e. some small modifications of PSR-2. Please adhere to `.editorconfig`
and the IDE settings defined in `/.idea` folder (set PHPStorm code style to 'Project').

## Branching

Please adhere to [Gitflow](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow) branching
conventions.

## Javascript pattern

Please use the [publish-subscribe](https://en.wikipedia.org/wiki/Publish–subscribe_pattern) (pub-sub) pattern. It allows
a high cohesion and low coupling.

## Attribution

### Author
- Dick de Leeuw, [leeuw.studio](https://leeuw.studio)

### Dependencies
- https://github.com/kint-php/kint
- https://github.com/thujohn/twitter
- https://github.com/coduo/php-humanizer
- https://github.com/LaravelCollective/html
