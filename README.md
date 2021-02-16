# Textpattern support forum

[![Build Status](https://travis-ci.com/textpattern/textpattern-forum.svg)](https://travis-ci.com/textpattern/textpattern-forum)

This repository contains the source code for the [Textpattern support forum](https://forum.textpattern.com/).

## Supported web browsers

* Chrome, Edge, Firefox, Safari and Opera the last two recent stable releases.
* Firefox ESR latest major point release.

Older versions of the above and other browsers may work, but these are the ones we verify.

Building this repository requires:

* [Node.js](https://nodejs.org/)
* [Grunt](https://gruntjs.com/)
* [Composer](https://getcomposer.org/)
* cURL and patch CLI programs.

Environment must consist of:

* Apache >=2.2
* PHP >=5.6.4
* MySQL >=5.0.6
* Unix-like OS, e.g. Linux or macOS

## Setup

### Installing required tools

The project uses [Grunt](https://gruntjs.com/) to run tasks and [Sass](http://sass-lang.com/) for CSS pre-processing. This creates a few dependencies in addition to your normal PHP required by FluxBB. First make sure you have base dependencies installed: [Node.js](https://nodejs.org/) and [Grunt](https://gruntjs.com/). You can install Node using the [installer](https://nodejs.org/), Composer using the [installer](https://getcomposer.org/), and Grunt with npm:

```ShellSession
$ npm install -g grunt-cli
```

Consult the Grunt documentation for more instructions if necessary.

### Setting up Apache virtual host

The `public/` directory is intended to be set as the server's document root. You can do this by adding a new virtual host to your `httpd.conf`. Along the lines of:

```apache
<VirtualHost *:80>
    VirtualHost "/absolute/path/to/textpattern-forum/public"
    ServerName forum.textpattern.test
</VirtualHost>
```

On a local development server, after this you can use your hosts file to point the development domain to correct location (e.g. back to home), run a local DNS server that resolves .test TLDs, or use a [xip.io](http://xip.io/) domain.

### Installing dependencies

After you have the base dependencies taken care of, you can install the project's dependencies. Navigate to the project's directory, and run the dependency managers:

```ShellSession
$ cd textpattern-forum
$ npm install
$ composer install
```

**npm** installs Node modules for Grunt and **composer** installs PHP libraries.

### Installing FluxBB

Once you have Grunt installed, installing and updating FluxBB is easy. To setup or update FluxBB run:

```ShellSession
$ grunt setup
```

This will download the correct release version FluxBB, patch it with mods and place it in the `public/` directory. Complete FluxBB's installation by following the normal [Installation steps](http://fluxbb.org/docs/v1.5/installing), and use [this as your config.php template](https://github.com/textpattern/textpattern-forum/blob/main/src/setup/config.php.dist).

After you have finished installing, run the postsetup task to remove extra trash left by the setup:

```ShellSession
$ grunt postsetup
```

### Updating and patching FluxBB

Periodically we update the [patch files](https://github.com/textpattern/textpattern-forum/tree/main/src/setup/patches) that ship with the repository. To re-patch and update your FluxBB installation, run the setup task again:

```ShellSession
$ grunt setup
```

If FluxBB installation version has changed, access your forums and follow the updating steps. After done, run `postsetup` task:

```ShellSession
$ grunt postsetup
```

You may need to [adjust the write permissions](https://fluxbb.org/docs/v1.5/installing#write-permissions) on the `public/cache/` directory after updating.

## Building

This repository hosts sources and needs to be built before it can be used. After you have installed all dependencies, you will be able to run tasks using Grunt, including building:

```ShellSession
$ grunt @task@
```

Where the `@task@` is either `build`, `setup` or `watch`.

* The `build` task builds the project.
* The `setup` task installs the latest stable release of FluxBB (see details above).
* The `watch` task will launch a task that watches for file changes; the project is then automatically built if a source file is modified.

## REST API

We offer a public API that can be used to retrieve data from the forums and topics. The public end point can be found from `https://forum.textpattern.com/api`. Data can be retrieved as JSON, and options can be passed optional HTTP query parameters. This API is read-only, and all requests must be done using HTTP GET method. You can also ping resource existence with HEAD, but any other methods are rejected.

### Response codes

HTTP status codes can be used to detect the type of response. The server responds with:

* 200: Successful response.
* 401: You will need to log in to access this resource. The resource may be removed, or private.
* 404: The resource has been removed, or has never existed.
* 500: Internal server error.
* 503: Server is under maintenance.

### Authentication

Authentication happens using basic HTTP method and regular forum user account.

```ShellSession
$ curl -u "username" https://forum.textpattern.com/api/
```

If you request a private or removed resource without being logged in, you will be treated with 401 response:

```ShellSession
$ curl -I https://forum.textpattern.com/api/posts/401
```

Responds with:

```
HTTP/1.0 401 Unauthorized
Date: Tue, 15 Oct 2013 09:16:42 GMT
Server: Apache
WWW-Authenticate: Basic realm="Textpattern CMS Support Forum External Syndication"
Connection: close
Content-Type: application/json; charset=UTF-8
```

### Topics

```
GET https://forum.textpattern.com/api/topics/:forum
```

Returns topics from the specified forum.

#### Optional parameters

* **limit**: number items to show. A value between 1 and 50. Defaults to 15.
* **sort**: either `last_post` or `posted`.

#### Example request

```ShellSession
$ curl https://forum.textpattern.com/api/topics/2
```

Response headers:

```
HTTP/1.1 200 OK
Date: Sun, 13 Oct 2013 11:24:49 GMT
Server: Apache
Content-Type: application/json; charset=UTF-8
```

Response body:

```
{
  "url": ["https:\/\/forum.textpattern.com\/index.php"],
  "topic": [
    {
      "title": "Better way to upload media",
      "link": "https:\/\/forum.textpattern.com\/viewtopic.php?id=40096&action=new",
      "content": "<p>+ lots<\/p>",
      "author": {
        "name": "tye",
        "uri": "https:\/\/forum.textpattern.com\/profile.php?id=5751"
      },
      "posted": "26 September 2013 21:51",
      "postedutc": "2013-09-24T16:18:48Z",
      "id": "40096"
    }
  ]
}
```

### Posts

```
GET https://forum.textpattern.com/api/posts/:topic
```

Returns replies from a specific public topic.

#### Optional parameters

* **limit**: number items to show. A value between 1 and 50. Defaults to 15.

#### Example request

```ShellSession
$ curl https://forum.textpattern.com/api/posts/40092
```

Response headers:

```
HTTP/1.1 200 OK
Date: Sun, 13 Oct 2013 12:24:49 GMT
Server: Apache
Content-Type: application/json; charset=UTF-8
```

Response body:

```
{
  "url": ["https:\/\/forum.textpattern.com\/viewtopic.php?id=40092"],
  "post": [
    {
      "title": "Re: Tag tree best practise: to nest or not to nest",
      "link": "https:\/\/forum.textpattern.com\/viewtopic.php?pid=275473#p275473",
      "content": "<p>Stef, this is really useful &#8211; I&#8217;m on the right track. Thank you very much.<\/p>",
      "author": {
        "name": "gaekwad",
        "uri": "https:\/\/forum.textpattern.com\/profile.php?id=7456"
      },
      "posted": "26 September 2013 21:51",
      "postedutc": "2013-09-24T16:18:48Z",
      "id": "275473"
    }
  ]
}
```

## Contributing

Want to help out with the development of Textpattern CMS? Please refer to the [Contributing documentation](https://docs.textpattern.com/development/contributing) for full details.

## License

Licensed under MIT license.
