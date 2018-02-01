# Caskmaster 2.0

Caskmaster2 is a CRM designed with a primary focus on Alcohol Producers across the UK.

## Getting Started

Once you've downloaded it, then all you need to is to execute the setup.sql file and modify framework/misc/config/config.php with your database details and you should be good to go.

### Prerequisites

You must have access to a Redis server, as Caskmaster2 uses it extensively.
In the future we may start to use AMQP, but as of now, this is not required.

### Installing

Download Caskmaster2.0 into a directory. Once you've done that, you will need to set up a host entry for your directory in Apache.
*Caskmaster2.0 will not be able to run if this step is not completed*

Once that is done, you should then run ```framework/misc/sql/setup.sql```

Then the last step is to modify ```framework/misc/config/config.php``` with the details of your database server.

Once you've got that working, navigate to your install and then append /v to the url as in the below example:

```
http://caskmaster2.local/v
```
This should return back the latest version of Caskmaster2 that is available to download.

## Deployment

Caskmaster2 is built with an auto updater system. It handles 90% of the deployment process for new versions. It does not yet handle or execute sql scripts, so these will need to be run manually, however it will handle code deployment. The necessary steps for deployment are outlined in an XML file viewable at ```framework/misc/update/update.xml``` . These contain the steps required to get your Caskmaster2 Install at the next version. *It is advisable that you do not modify this file.*

## Built With

* [FlightPHP](http://flightphp.com/) - The chosen PHP Framework used for Caskmaster2

## Contributing

### Coming Soon

## Versioning

We use [SemVer](http://semver.org/) for versioning.

The Caskmaster2 team aim to do monthly releases. For the latest version please visit http://getcaskmaster.com/v then checkout the appropriate branch.

## Authors

* **Ben Hassan** - (https://github.com/loftyD/

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
