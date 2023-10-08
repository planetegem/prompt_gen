A prompt generator would be an integral part in my next game, which I wanted to build in PHP. 
I knew that I would need lists of words which could be combined by an algorithm to create prompts.
As such, an SQL database containing these words seemed necessary.

To be able to create the lists of words, I first built an interface. 
Logging in gives the user access to a form to add new words with specific parameters. 
Entries can also be updated or deleted if necessary.

Once the interface was there, I got to work on the algorithm, which consists of 2 parts:
    (1) a javascript side which requests words and combines them and
    (2) a php side which handles the request and responds with an object pulled from the MySQL db.
For easy testing, I made the 2 sides communicate at every step, so you can follow the thinking process in the console.
