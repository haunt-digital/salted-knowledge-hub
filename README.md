# Salted Knowledge Hub
Salted Herring's Knowledge Hub module

## Manual
### Installation

*Step 1*
Because this module isn’t public, you will need to make changes in composer.json to be able to access to it:

```
"repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:salted-herring/salted-knowledge-hub.git"
        }
    ]
```

```
"require": {
        …
        "salted-herring/salted-knowledge-hub": "dev-master”,
    …
```

And then you run `composer update`

Once the installation is done, do `dev/build?flush=all`  on the browser

*Step 2*
To give support to the frontend, you also need below JS libraries installed. Please add them into your bower’s `dependencies` block, and then run `bower update`

```
"salted-js": "*",
"handlebars": "^4.0.10",
"fancySelect": "fancyselect#^1.1.0",
"isotope": "^3.0.4",
"isotope-packery": "^2.0.0",
"imagesloaded": "^4.1.3"
```

*Step 3*
Now go to `mainsite/code/Page.php` file, find `init` function, and add below code accordingly

```
$combined_file = 'scripts.js';
Requirements::combine_files(
    $combined_file,
    array(
        ...
        'themes/default/js/components/fancySelect/fancySelect.js',
        'themes/default/js/components/isotope/dist/isotope.pkgd.min.js',
        'themes/default/js/components/isotope-packery/packery-mode.pkgd.min.js',
        'themes/default/js/components/imagesloaded/imagesloaded.pkgd.min.js',
        ...
    )
);

$this->KnowledgeHubJSinit($combined_file);
```

*Step 4*
… Almost there — now open `themes/[theme_name]/templates/Page.ss` file, and add below line before the `</head>` tag:

```
<script src="/$themeDir/js/components/handlebars/handlebars.min.js"></script>
```

The reason we have to include Handlebars’ library separately, because when we switch the site to Live mode, it will complain - seems Handlebars have a very picky lifestyle :/

### Backend
All your articles need to extend: `KnowledgeArticle`, and then do your fancy stuff from there.

### CMS
The page hierarchy is like this:
```
Knowledge Hub Landing Page
  |-Knowledge Hub Group Page A
    |-Knowledge Article Page 1
    |-Knowledge Article Page 2
    |-Knowledge Article Page 3
  |-Knowledge Hub Group Page B
    |-Knowledge Article Page 4
    |-Knowledge Article Page 5
    |-Knowledge Article Page 6
```

There is a possible gotcha after a Knowledge Hub Group page is created:
After the page is created, you won’t see the Child Page tab straightaway. You need to open up the right sidebar (assume it’s closed), and select the article types that you are going to allow in this particular group

![screengrab](http://lol.saltydev.com/knowledge-hub-screengrab.png)

Oh by the way, the right sidebar in Knowledge Hub Group Page editing interface is also where you define _how many items per page_

### Frontend
This module leaves the canvas blank for your awesome painting to take place. If you find the default templates trip you, just feel free to override them
