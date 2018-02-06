# Twitter reach calculator

## Task description

Develop a simple Laravel application using best practices that calculates the reach of a specific tweet.
A user should be able to enter the URL of a tweet. The application will lookup the people who retweeted 
the tweet using the Twitter API. The application then sums up the amount of followers each user has that 
has retweeted the tweet. These results need to be stored in the cached for two hours. If someone tries to 
calculate the reach of a tweet that has already been calculated the results should be returned from the 
cache. After two hours the cache should be updated.

## Attribution

### Author
- Dick de Leeuw, [leeuw.studio](https://leeuw.studio)
