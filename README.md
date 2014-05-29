# README

## Requires

```json
        "sansis/basebundle" : "dev-master",
        "friendsofsymfony/jsrouting-bundle" : "1.4.*@dev",
        "braincrafted/bootstrap-bundle" : "2.0.*",
        "twitter/bootstrap" : "3.1.*",
        "knplabs/knp-menu-bundle" : "1.1.2",
        "knplabs/knp-paginator-bundle" : "~2.4",
        "psliwa/pdf-bundle" : "dev-master"
```
        
## Use it in another project

composer.json:
```json
    [...]
    "require" : {
        [...]
        "sansis/reportbundle" : "dev-master"
    },
    "repositories" : [{
        "type" : "vcs",
        "url" : "https://github.com/phackwer/ReportBundle.git"
    }],
    [...]
```

## Add to AppKernel

```php
         //SanSIS Core Production Bundles
         new SanSIS\Core\ReportBundle\SanSISCoreReportBundle(),
```

## Add to routing.yml

```yml
san_sis_core_report:
    resource: "@SanSISCoreReportBundle/Resources/config/routing.yml"
    prefix:   /
```
