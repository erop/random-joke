### The task:

    Create HTML-form with fields input:email, select:category from http://www.icndb.com/api/ After submitting the form an email message should be dispatched with subject "Random joke of %category name%"
    The body of the message should contain the joke from the choosen category. The text should be written into the file on the local disk.

### Requirements:

    Working with API should be implemented with http://docs.guzzlephp.org/en/stable/
    The app should use Symfony (3 or 4)
    The app code should follow SOLID principles
    The app code should be covered with PHPUnit tests

### Recommended books:

	Martin, Robert C. (2009). Clean Code: A Handbook of Agile Software Craftsmanship

	Principles of Package Design
	Preparing your code for reuse
	Matthias Noback


### Build, test and run
Use Makefile commands (`Symfony CLI` and `make` required to be available in $PATH): `$ make build`, `$ make test`, `$ make run`



