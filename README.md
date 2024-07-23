# Email Obfuscate Plugin

The **Email Obfuscate** plugin is an extension for [Grav CMS](https://github.com/getgrav/grav). The Plugin provides markdown and twig functionality to convert mail adresses to JavaScript obfuscated mailto links to prevent spam.  
This plugin is based on and provides similar functionality as the [Antispam Plugin](https://github.com/skinofthesoul/grav-plugin-antispam). The Antispam plugin automatically recognizes and obfuscates email addresses, this plugin provides a twig filter and markdown syntax to manually obfuscate email adresses.

## Installation

Installing the Email Obfuscate plugin can only be done in a manual way.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `email-obfuscate`. You can find these files on [GitHub](https://github.com//grav-plugin-email-obfuscate).

You should now have all the plugin files under

    /your/site/grav/user/plugins/email-obfuscate

## Configuration

Before configuring this plugin, you should copy the `user/plugins/email-obfuscate/email-obfuscate.yaml` to `user/config/plugins/email-obfuscate.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named email-obfuscate.yaml will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

**Markdown usage:**  
Enclose an email adress in dollar signs to convert it to an obfuscated mailto link in markdown:
```markdown
$john.doe@example.com$
```
**Twig usage:**  
Use the `|obfuscate` filter to convert an email adress to an obfuscated mailto link in twig (dont't forget to append the `|raw` filter).
```twig
{% set email = "john.doe@example.com" %}
{{ email|obfuscate|raw }}
```

## Credits

This plugin is based on the [Antispam Plugin](https://github.com/skinofthesoul/grav-plugin-antispam).
