@annotation:tour codio
# Codio.com
## Starting point

@annotation:tour beginning
# Beginning...
We start with a simple html5 index file

The main reason for this annotation tour is to remember what why and how. I'm just fiddling, as I initially stated in the README.md.

@annotation:tour composer
# Adding custom namespaces
In composer.json:
    "autoload": {
        "psr-0": {
            "mtc2ms":        "vendor_mtc/",
            "mtc2ms\\Test": "examples/test/
        }
    }
In terminal: composer update
In example: 
new \mtc2ms\Test(), path=vendor_mtc/mtc2ms/
new \mtc2ms\SlimMiddleware\Test(), path=vendor_mtc/SlimMiddleware/

@annotation:tour npm
# adding npm
- npm install domify