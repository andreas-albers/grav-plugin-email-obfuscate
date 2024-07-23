<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Common\Grav;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class EmailEncryptPlugin
 * @package Grav\Plugin
 */
class EmailObfuscatePlugin extends Plugin
{
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main events we are interested in
        $this->enable([
            // Put your main events here
            'onTwigInitialized' => ['onTwigInitialized', 0],
            'onAssetsInitialized' => ['onAssetsInitialized', 0],
            'onMarkdownInitialized' => ['onMarkdownInitialized', 0]
        ]);
    }

    /**
     * @param Event $e
     */
    public function onMarkdownInitialized(Event $e)
    {
        $markdown = $e['markdown'];

        // Initialize new InlineType of Parsedown parser
        $markdown->addInlineType('$', 'ObfuscateMail');

        // Add function to handle this
        $markdown->inlineObfuscateMail = function($excerpt)
        {
            $re = '/\$([a-zA-Z0-9]+[._\-0-9a-zA-Z]*@[a-zA-Z0-9]+[._\-0-9a-zA-Z]*\.[a-zA-Z]{2,})\$/m';

            if (preg_match($re, $excerpt['text'], $matches))
            {
                $encrypted = $this->encryptEmail($matches[1]);
                return array(
                    'extent' => strlen($matches[0]),
                    'element' => array(
                        'name' => 'span',
                        'text' => $this->grav['language']->translate(['PLUGIN_EMAIL_OBFUSCATE.NOSCRIPT']),
                        'attributes' => array(
                            'class' => 'encrypted-email',
                            'data-key' => $encrypted['data-key'],
                            'data-coded' => $encrypted['data-coded'],
                        ),
                    ),
                );
            }
        };
    }


    /**
     * @param Event $e
     */
    public function onTwigInitialized(Event $e)
    {
        $this->grav['twig']->twig()->addFilter(
            new \Twig_SimpleFilter('obfuscate', [$this, 'writeObfuscatedEmailSpan'])
        );
    }

    /**
     * @param Event $e
     */
    public function onAssetsInitialized(Event $e)
    {
        $this->grav['assets']->addJs($this->grav['base_url'] . '/user/plugins/email-obfuscate/js/decrypt-email.js', ['group' => 'bottom']);
    }

    public function writeObfuscatedEmailSpan($address)
    {
        $encrypted = $this->encryptEmail($address);
        $no_script_txt = $this->grav['language']->translate(['PLUGIN_EMAIL_OBFUSCATE.NOSCRIPT']);
        $txt = "<span class=\"encrypted-email\" data-key=\"";
        $txt .= $encrypted['data-key'];
        $txt .= "\" data-coded=\"";
        $txt .= $encrypted['data-coded'];
        $txt .= "\">$no_script_txt</span>";
        return $txt;
    }

    /**
     * Encrypt email
     */
    public function encryptEmail($address)
    {
        // Generate cipher
        $coded = "";
        $unmixedkey = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789.@-_?=&/:";
        $inprogresskey = $unmixedkey;
        $mixedkey = "";
        $unshuffled = strlen($unmixedkey);
        while ($unshuffled > 0)
        {
            $ranpos = mt_rand(0, $unshuffled-1);
            $nextchar = $inprogresskey[$ranpos];
            $mixedkey .= $nextchar;
            $before = substr($inprogresskey,0,$ranpos);
            $after = substr($inprogresskey,$ranpos+1,$unshuffled-($ranpos+1));
            $inprogresskey = $before.''.$after;
            $unshuffled -= 1;
        }
        $cipher = $mixedkey;

        // Obfuscate $address using $cipher and store as $coded
        $shift = strlen($address);
        for ($j=0; $j<strlen($address); $j++)
        {
            if (strpos($cipher,$address[$j]) == -1 )
            {
                $chr = $address[$j];
                $coded .= $address[$j];
            }
            else
            {
                $chr = (strpos($cipher,$address[$j]) + $shift) % strlen($cipher);
                $coded .= $cipher[$chr];
            }
        }
        return array(
            'data-key' => $cipher,
            'data-coded' => $coded,
        );
    }
}
